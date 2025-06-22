<?php

namespace RipDB\Controller;

use AsyncHandler;
use RipDB\Model as m;
use RipDB\RipGuesser as game;

require_once('Controller.php');
require_once('private_core/objects/IAsyncHandler.php');
require_once('private_core/model/GuesserModel.php');
require_once('private_core/objects/RipGuesser.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\GuesserModel $model
 */
class GuesserController extends Controller implements \RipDB\Objects\IAsyncHandler
{
	use \RipDB\DataValidator;
	private ?\RipDB\RipGuesser\Game $game = null;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\GuesserModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void {}

	/**
	 * Handles the GET requests for the game.
	 */
	public function get(string $method, ?string $methodGroup = null): mixed
	{
		$response = null;
		switch ($methodGroup) {
			// There is only one "start" request, so any requests made here will start the round.
			case 'game':
				switch ($method) {
					case 'check':
						$response = $this->model->checkSessionForGame();
						break;
					case 'round-next':
						$response = $this->advanceRound();
						break;
					case 'round-submit':

						break;
				}
				break;
		}
		return $response;
	}

	/**
	 * Handles the POST requests for the game
	 */
	public function post(string $method, ?string $methodGroup = null): mixed
	{
		$response = null;
		switch ($methodGroup) {
			// There is only one "start" request, so any requests made here will start the round.
			case 'game':
				if ($method == 'start') {
					$response = $this->startGame($_POST);
				}
				break;
		}
		return $response;
	}

	public function put(string $method, ?string $methodGroup = null): mixed
	{
		return null;
	}

	public function delete(string $method, ?string $methodGroup = null): mixed
	{
		$response = null;
		switch ($methodGroup) {
			// There is only one "start" request, so any requests made here will start the round.
			case 'game':
				if ($method == 'purge') {
					$response = $this->model->purgeGameSession();
				}
				break;
		}
		return $response;
	}

	/**
	 * Sets the controller's game property if it exists in the session.
	 * @return bool Returns if the controller's game property has been set properly.
	 */
	private function deserializeGame(): bool
	{
		if (isset($_SESSION[m\GuesserModel::SESS_GAME_OBJ])) {
			$this->game = unserialize($_SESSION[m\GuesserModel::SESS_GAME_OBJ]);
		}

		return $this->game instanceof game\Game;
	}

	/**
	 * Updates the game object and serialises it back to the client's session.
	 */
	private function updateGame()
	{
		// TODO
	}

	/**
	 * Attempts to start the game.
	 * Validates the given form data and if the model accepts the 
	 */
	private function startGame(array $data): bool
	{
		$gameStarted = false;
		error_log(session_status());

		// These validation error messages should never be returned to the user, but in case someone tries to bypass the constraints, they're here.
		$showAnswerCount = $this->validateBool($data['show-count'] ?? null, 'Invalid value for "Show Answer Count" field.', true);
		$rounds = $this->validateNumber($data['rounds'] ?? null, 'Invalid number of rounds given.', game\Game::MAX_ROUNDS, 1);
		$difficulty = $this->validateFromList($data['difficulty'] ?? null, [game\Difficulty::Beginner->name, game\Difficulty::Standard->name, game\Difficulty::Hard->name], 'Invalid difficulty.');
		// If the min is greater than the max or vice versa, these will be rectified when creating the settings object.
		$jokesMin = $this->validateNumber($data['jokes-min'] ?? null, 'You need at least one joke to find in a rip!', game\Settings::MAX_JOKES, game\Settings::MIN_JOKES);
		$jokesMax = $this->validateNumber($data['jokes-max'] ?? null, "You can't play with rips that have more than " . game\Settings::MAX_JOKES . " jokes!", game\Settings::MAX_JOKES, game\Settings::MIN_JOKES);
		$minLength = '00:00:00';
		$maxLength = $this->validateTimestamp($data['length'] ?? null, "Rips must be less than " . game\Settings::MAX_RIP_LENGTH . " minutes long.", game\Settings::MAX_RIP_LENGTH, game\Settings::MIN_RIP_LENGTH);
		$metaJokes = [];
		$metas = [];

		// Ensure that all the parameters are valid. If any is invalid, get the error message.
		$valid = true;
		$validated = [$showAnswerCount, $rounds, $difficulty, $jokesMin, $jokesMax, $minLength, $maxLength, $metaJokes, $metas];
		foreach ($validated as $val) {
			if ($val instanceof \RipDB\Error) {
				error_log($val->getMessage());
				$valid = false;
			}
		}

		if ($valid) {
			// Convert the difficulty into an enum
			$difficulty = game\Difficulty::enumByValue($difficulty);
			$settings = new game\Settings($showAnswerCount, $rounds, $jokesMin, $jokesMax, $minLength, $maxLength, $metaJokes, $metas, $difficulty);
			$gameStarted = $this->model->initGame($settings);
		}

		return $gameStarted;
	}

	/**
	 * Allows a player to play in a currently running game.
	 * Retrieves the game's settings from the server with the given game ID and sets up the player's game data for this session.
	 */
	private function resumeGame(string $gameID): bool
	{
		$joinSuccess = false;

		return $joinSuccess;
	}

	private function advanceRound()
	{
		$roundData = false;
		if ($this->deserializeGame()) {
			$roundData = $this->game->fetchRip($this->model);
		}

		return $roundData;
	}
}
