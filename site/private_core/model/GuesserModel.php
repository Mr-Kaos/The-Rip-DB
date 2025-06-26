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
			$game = new game\Game($settings->roundCount, $settings);
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
		$ripID = $this->db->execute("CALL usp_SelectRandomRip(?,?,?,?,?,?,?)", [
			$settings->minJokes,
			$settings->maxJokes,
			$settings->minLength,
			$settings->maxLength,
			json_encode($settings->metaJokeFilters),
			json_encode($settings->metaFilters),
			json_encode($excludedRips)
		])->fetch();

		// If a rip was found, retrieve its data.
		if ($ripID !== false) {
			$ripID = $ripID['RipID'];
			$ripModel = new RipModel();

			$rip = $ripModel->getRip($ripID);
		}

		return $rip;
	}
}
