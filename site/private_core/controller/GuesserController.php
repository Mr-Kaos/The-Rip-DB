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
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'rip-guesser-play':
				break;
		}
	}

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
					case 'skip':
						$response = $this->resetRound();
						break;
				}
				break;
			case 'search':
				switch ($method) {
					case 'jokes':
						$response = $this->model->getJokesWithMetaNames($_GET['q'] ?? null);
						break;
				}
			case 'setup':
				switch($method) {
					case 'playlists-search':
						if (!empty($_GET['code'] ?? null)) {
							if (strlen($_GET['code']) == 8) {
								$response = $this->model->getPlaylistByCode($_GET['code']);
								if (empty($response)) {
									$response = 'No playlist could be found with this code.';
								}
							} else {
								$response = 'Please enter a valid code';
							}
						} else {
							$response = $this->model->getPlaylists($_GET['page'] ?? 0, $_GET['search'] ?? null);
						}
						break;
				}
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
				switch ($method) {
					case 'start':
						$response = $this->startGame($_POST);
						break;
					case 'submit':
						$response = $this->submitRound($_POST);
						break;
					case 'feedback':
						$response = $this->submitFeedback($_POST);
						break;
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
					$response = $this->endGame();
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
	 * Attempts to start the game.
	 * Validates the given form data and applies filters to the game's settings if they are valid.
	 */
	private function startGame(array $data): bool
	{
		$gameStarted = false;

		// These validation error messages should never be returned to the user, but in case someone tries to bypass the constraints, they're here.
		$showAnswerCount = $this->validateBool($data['show-count'] ?? null, 'Invalid value for "Show Answer Count" field.', true);
		$rounds = $this->validateNumber($data['rounds'] ?? null, 'Invalid number of rounds given.', game\Game::MAX_ROUNDS, 1);
		$playlists = $this->validateArray($data['playlists'] ?? null, 'validateNumber', [], 'Invalid playlists given.', false);
		$difficulty = $this->validateFromList($data['difficulty'] ?? null, [game\Difficulty::Beginner->name, game\Difficulty::Standard->name, game\Difficulty::Hard->name], 'Invalid difficulty.');
		// If the min is greater than the max or vice versa, these will be rectified when creating the settings object.
		$jokesMin = $this->validateNumber($data['jokes-min'] ?? null, 'You need at least one joke to find in a rip!', game\Settings::MAX_JOKES, game\Settings::MIN_JOKES);
		$jokesMax = $this->validateNumber($data['jokes-max'] ?? null, "You can't play with rips that have more than " . game\Settings::MAX_JOKES . " jokes!", game\Settings::MAX_JOKES, game\Settings::MIN_JOKES);
		$maxLength = $this->validateTimestamp($data['maxlength'] ?? null, "Rips must be less than " . game\Settings::MAX_RIP_LENGTH . " minutes long.", game\Settings::MAX_RIP_LENGTH, game\Settings::MIN_RIP_LENGTH);
		$minLength = $this->validateTimestamp($data['minlength'] ?? null, "Rips must be more than " . game\Settings::MIN_RIP_LENGTH . " minutes long.", game\Settings::MAX_RIP_LENGTH, game\Settings::MIN_RIP_LENGTH);

		$metaJokes = $this->model->getValidMetaJokes($data['filter-metajokes'] ?? []);
		$metas = $this->model->getValidMetas($data['filter-metas'] ?? []);

		// Ensure that all the parameters are valid. If any is invalid, get the error message.
		$valid = true;
		$validated = [$showAnswerCount, $rounds, $difficulty, $jokesMin, $jokesMax, $minLength, $maxLength, $metaJokes, $metas, $playlists];
		foreach ($validated as $val) {
			if ($val instanceof \RipDB\Error) {
				error_log($val->getMessage());
				$valid = false;
			}
		}

		if ($valid) {
			// Convert the difficulty into an enum
			$difficulty = game\Difficulty::enumByValue($difficulty);
			$settings = new game\Settings($showAnswerCount, $rounds, $jokesMin, $jokesMax, $minLength, $maxLength, $metaJokes, $metas, $difficulty, $playlists);
			$gameStarted = $this->model->initGame($settings);
		}

		return $gameStarted;
	}

	/**
	 * Ends the game.
	 */
	private function endGame(): void
	{
		$this->model->purgeGameSession();
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

	/**
	 * Advances the round.
	 * @return false|array An array containing the round's data. If a round could not be created, false is returned.
	 */
	private function advanceRound(): false|array
	{
		$roundData = false;

		if ($this->deserializeGame()) {
			if ($this->game->isFinished()) {
				$roundData = ['GameEnd' => true, 'Summary' => $this->game->getGameSummary()];
				// If the game ended early (too few rips for the requested number of rounds), supply a message
				if ($this->game->tooFewRips) {
					$roundData['Message'] = "There weren't enough rips with the supplied filters to play the number of requested rounds.\n(You played all rips with the filters given!)";
				}
				$this->endGame();
			} else {
				$roundData = $this->game->nextRound($this->model);
			}
			$this->model->saveGame($this->game);
		}

		return $roundData;
	}

	/**
	 * Resets the current round by replacing it with a new round.
	 * Used if the rip's video is unplayable and a new one is needed.
	 * @return false|array An array containing the new round's data. If a round could not be created, false is returned.
	 */
	private function resetRound(): false|array
	{
		$roundData = false;
		if ($this->deserializeGame()) {
			$roundData = $this->game->resetRound($this->model);
			$this->model->saveGame($this->game);
		}
		return $roundData;
	}

	/**
	 * Checks the round's submitted answers and returns an array of incorrect results and the score.
	 * @return array An associative array with the following keys:  
	 * - `CorrectAnswers` - Stores the correct answers for any incorrectly guessed values.
	 * - `Score` - The score for the round.
	 */
	private function submitRound(array $data): false|array
	{
		$result = false;
		// Preliminary checks to ensure that the game's data is not lost.
		if ($this->deserializeGame()) {
			if (!is_null($round = $this->game->getCurrentRound())) {
				$result = [
					'Results' => $round->checkSubmission($data),
					'Score' => $round->getScore()
				];
			}

			// error_log(print_r($this->game->getCurrentRound(), true));
			$this->model->saveGame($this->game);
		}

		return $result;
	}

	/**
	 * Submits feedback for a round's rip.
	 * @param array $data The feedback data submitted. Will contain a key of either "upvote" or "joke".  
	 * "Upvote" is a boolean indicating if the rip is suitable for the game, and "joke" is a string containing information on missing/incorrect jokes in the rip.
	 * @return array|true Returns true if submission was successful. Else, returns an array of error messages (strings) if a failure occurred.
	 */
	private function submitFeedback(array $data): array|true
	{

		$submission = ['ERROR' => 'Failed to submit'];

		if ($this->deserializeGame()) {
			$ripId = $this->game->getCurrentRound()->ripID;
			$upvote = $joke = null;

			if (array_key_exists('upvote', $data)) {
				$upvote = $this->validateBool($data['upvote']);
			} elseif (array_key_exists('joke', $data)) {
				$joke = $this->validateString($data['joke'], 'Invalid text given.', 1024, 1);
			}

			$submission = $this->model->submitFormData([$ripId, $upvote, $joke], 'usp_InsertRipFeedback');

			if (is_array($submission)) {
				// get error message and return it as response.
				foreach ($submission as $key => &$error) {
					$submission[$key] = $error->getMessage();
				}
			}
		}

		return $submission;
	}
}
