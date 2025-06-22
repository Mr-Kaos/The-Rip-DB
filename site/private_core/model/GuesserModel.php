<?php

namespace RipDB\Model;

use RipDB\RipGuesser as game;

require_once('Model.php');

class GuesserModel extends Model
{
	const TABLE = 'RipGuesserGame';
	const SESS_GAME_ID = 'GameSessionID';

	public function __construct()
	{
		if (session_status() != \PHP_SESSION_ACTIVE) {
			session_start();
		}
		parent::__construct();
	}

	/**
	 * Initialises the game. If a game is already active in the current session, it uses it instead.
	 * @return false|\RipDB\RipGuesser\Game If the game is successfully initialised, a RipGuesser Game object is returned. Else, false is returned.
	 */
	public function initGame(game\Settings $settings): false|game\Game
	{
		$success = false;
		$gameID = uniqid("", true);

		try {
			// Need to be careful that the serialised settings does not exceed 8196 bytes. Without filters, it takes roughly 320 bytes.
			// If A LOT of filters were given, this may be exceeded. Perhaps, a limit on filters should be set in place for the game.
			$this->db->execute("CALL usp_NewRipGuesserGame(?, ?)", [$gameID, serialize($settings)]);
			$_SESSION[self::SESS_GAME_ID] = $gameID;
		} catch (\PicoDb\SQLException $error) {
			error_log($error->getMessage());
		}
		return $success;
	}

	public function getGame(string $gameID) {}

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
}
