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
	private \RipDB\RipGuesser\Game $game;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\GuesserModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void {}

	public function get(string $method, ?string $methodGroup = null): mixed
	{
		return null;
	}
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
			case 'round':

				break;
		}
		return $response;
	}
	public function put(string $method, ?string $methodGroup = null): mixed
	{
		return null;
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
		$jokesMax = $this->validateNumber($data['jokes-max'] ?? null, "You can't play with rips that have more than " . game\Settings::MAX_JOKES . " jokes!" , game\Settings::MAX_JOKES, game\Settings::MIN_JOKES);
		$length = $this->validateTimestamp($data['length'] ?? null, "Rips must be less than " . game\Settings::MAX_RIP_LENGTH . " minutes long." , game\Settings::MAX_RIP_LENGTH, game\Settings::MIN_RIP_LENGTH);
		$metaJokes = [];
		$metas = [];

		// Ensure that all the parameters are valid. If any is invalid, get the error message.
		$valid = true;
		$validated = [$showAnswerCount, $rounds, $difficulty, $jokesMin, $jokesMax, $length, $metaJokes, $metas];
		foreach ($validated as $val) {
			if ($val instanceof \RipDB\Error) {
				error_log($val->getMessage());
				$valid = false;
			}
		}

		if ($valid) {
			$settings = new game\Settings($showAnswerCount, $rounds, $jokesMin, $jokesMax, $metaJokes, $metas);
			$gameStarted = $this->model->initGame($settings);
		}

		return $gameStarted;
	}
}
