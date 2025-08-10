<?php

namespace RipDB\Model;

use RipDB\RipGuesser as game;

require_once('Model.php');
require_once('RipModel.php');

class GuesserModel extends Model
{
	const TABLE = 'RipGuesserGame';
	const SESS_GAME_ID = 'GameSessionID';
	const SESS_GAME_OBJ = 'GameInstance';
	/** Defines how many playlists are visible per row in the user-interface. Used to query additional playlists with an offset. */
	const PLAYLISTS_PER_ROW = 3;
	/** Defines the number of rows to get per fetch of playlists. Modify this value to increase the number of rows retrieved. */
	const PLAYLISTS_ROW_BATCH = 3;

	public function __construct()
	{
		if (session_status() != \PHP_SESSION_ACTIVE) {
			session_start();
		}
		parent::__construct();
	}

	/**
	 * Initialises the game. If a game is already active in the current session, it uses it instead.
	 * @return bool True if the game is successfully created, otherwise false.
	 */
	public function initGame(game\Settings $settings): bool
	{
		$success = false;
		$gameID = uniqid("", true);

		try {
			// NOTE: Need to be careful that the serialised settings does not exceed 8196 bytes. Without filters, it takes roughly 320 bytes.
			// If A LOT of filters were given, this may be exceeded. Perhaps, a limit on filters should be set in place for the game.
			$this->db->execute("CALL usp_NewRipGuesserGame(?, ?)", [$gameID, serialize($settings)]);
			$_SESSION[self::SESS_GAME_ID] = $gameID;
			$game = new game\Game($this, $settings->roundCount, $settings);
			$this->saveGame($game);
			$success = true;
		} catch (\PicoDb\SQLException $error) {
			error_log($error->getMessage());
		}
		return $success;
	}

	/**
	 * Retrieves a game object by the session ID.
	 */
	public function getGame(string $gameID)
	{
		//TODO
	}

	/**
	 * Serialises the game object to the client's session.
	 * @param Game $game The game object to save.
	 */
	public function saveGame(game\Game $game)
	{
		$_SESSION[self::SESS_GAME_OBJ] = serialize($game);
	}

	/**
	 * Checks the user's session to see if a game is already active.
	 * Also checks if the game exists in the database.
	 * @return false|string Returns false if no game is active, else the game ID of the active game is returned.
	 */
	public function checkSessionForGame(): false|string
	{
		$result = false;
		if (!empty($_SESSION[self::SESS_GAME_ID] ?? null)) {
			$result = $_SESSION[self::SESS_GAME_ID];

			// If the game record does not exist in the database, unset the ID from the player's session.
			$existsInDB = $this->db->table(self::TABLE)->eq('SessionID', $result)->count();
			if ($existsInDB == 0) {
				unset($_SESSION[self::SESS_GAME_ID]);
				$result = false;
			}
		}

		return $result;
	}

	/**
	 * Purges the player's active game session.
	 */
	public function purgeGameSession(): void
	{
		$gameID = $this->checkSessionForGame();
		if ($gameID !== false) {
			$this->db->table(self::TABLE)->eq('SessionID', $gameID)->remove();
			unset($_SESSION[self::SESS_GAME_ID]);
		}
	}

	/**
	 * Gets a rip based on the given settings round
	 * @param game\Settings The settings object containing filters for rip searching.
	 * @param array $excludedRips An array of ripIDs that have already been played in the game's round to prevent being picked again.
	 */
	public function getRip(game\Settings $settings, array $excludedRips): false|array
	{
		$rip = false;

		$qry = $this->buildRipQuery($settings, $excludedRips);

		$ripID = $qry->findOneColumn('RipID');

		// If a rip was found, retrieve its data.
		if ($ripID !== false) {
			$ripModel = new RipModel();

			$rip = $ripModel->getRip($ripID);
		}

		return $rip;
	}

	/**
	 * Finds the number of rips with the given filters.
	 * @param game\Settings The settings object containing filters for rip searching.
	 * @return int The number of rips that exist with the given filters.
	 */
	public function getTotalRipsAvailable(game\Settings $settings): int
	{
		$count = 0;

		$rips = $this->buildRipQuery($settings)->findAllByColumn('RipID');
		$count = count($rips);

		return $count;
	}

	/**
	 * Builds the query to select a random rip for a rip guesser round.
	 * @param game\Settings The settings object containing filters for rip searching.
	 * @param array $excludedRips An array of ripIDs that have already been played in the game's round to prevent being picked again. Only used when called from getRip.
	 */
	private function buildRipQuery(game\Settings $settings, array $excludedRips = []): \PicoDb\Table
	{
		$qry = $this->db
			->table('vw_RipsDetailed')
			->gte('RipLength', $settings->minLength)
			->lte('RipLength', $settings->maxLength);

		// Apply filters

		// Excluded rips
		if (!empty($excludedRips)) {
			$qry = $qry->notIn('RipID', $excludedRips);
		}

		// Meta jokes
		if (!empty($settings->metaJokeFilters)) {
			$qry = $qry->in('MetaJokeID', $settings->metaJokeFilters);
		}
		// Metas
		if (!empty($settings->metaJokeFilters)) {
			$qry = $qry->in('MetaID', $settings->metaFilters);
		}
		// Playlists
		// If a playlist is used for the game, only select rips from the playlist
		if (!empty($settings->playlists)) {
			// Get all the unique Rip IDs from the playlists.
			// This query is run every new round to ensure that if any rips are deleted/added from the playlist are reflected.
			$ripIDs = $this->db->table('Playlists')
				->in('PlaylistID', $settings->playlists)
				->findAllByColumn('RipIDs');

			$rips = [];
			foreach ($ripIDs as $ripJSON) {
				$rips = array_merge($rips, json_decode($ripJSON, true));
			}
			$rips = array_filter($rips);

			$qry = $qry->in('RipID', $rips);
		}

		$qry = $qry->addCondition("RipID IN (SELECT RipID FROM RipJokes GROUP BY RipID HAVING COUNT(JokeID) >= $settings->minJokes AND COUNT(JokeID) <= $settings->maxJokes) GROUP BY RipID ORDER BY RAND()");

		return $qry;
	}

	/**
	 * Returns a list of Meta Joke IDs that exist in the database from the given list.
	 * I.e., it removes false Meta Joke IDs from the given list.
	 */
	public function getValidMetaJokes(array $ids): array
	{
		return $this->db->table('MetaJokes')->in('MetaJokeID', $ids)->findAllByColumn('MetaJokeID');
	}

	/**
	 * Returns a list of Meta IDs that exist in the database from the given list.
	 * I.e., it removes false Meta IDs from the given list.
	 */
	public function getValidMetas(array $ids): array
	{
		return $this->db->table('Metas')->in('MetaID', $ids)->findAllByColumn('MetaID');
	}

	public function getJokesWithMetaNames(?string $search)
	{
		$records = $this->db->table('Jokes')
			->columns('Jokes.JokeID', 'JokeName', 'MetaJokeName')
			->join('JokeMetas', 'JokeID', 'JokeID')
			->join('MetaJokes', 'MetaJokeID', 'MetaJokeID', 'JokeMetas')
			->limit(50)
			->asc('JokeName');

		$results = null;
		if (!empty($search)) {
			$records->beginOr()->ilike('JokeName', "%$search%")->ilike('MetaJokeName', "%$search%")->closeOr();

			$jokes = $records->findAll();

			$results = [];
			foreach ($jokes as $joke) {
				array_push($results, ['ID' => $joke['JokeID'], 'NAME' => $joke['JokeName'] . ' (' . $joke['MetaJokeName'] . ')']);
			}
		}
		return $results;
	}

	/**
	 * Gets a batch of 3 rows of public playlists from the given offset. 
	 */
	public function getPlaylists(int $offset = 0, ?string $search = null)
	{
		$rows = self::PLAYLISTS_PER_ROW * self::PLAYLISTS_ROW_BATCH;
		$qry = $this->db->table('vw_Playlists')
			->columns('PlaylistID', 'PlaylistName', 'Username', 'RipCount')
			->eq('IsPublic', 1)
			->offset($offset * $rows)->limit($rows);

		if (!empty($search)) {
			$qry = $qry->like('PlaylistName', "%$search%");
		}

		return $qry->findAll();
	}

	public function getPlaylistByCode(string $code) {
		return $this->db->table('vw_Playlists')
			->columns('PlaylistID', 'PlaylistName', 'Username', 'RipCount')
			->eq('ShareCode', $code)
			->findAll();
	}
}