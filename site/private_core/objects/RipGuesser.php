<?php

namespace RipDB\RipGuesser;

enum Difficulty: string
{
	case Beginner = "Ideal for those unfamiliar with rips";
	case Standard = "The normal difficulty";
	case Hard = "Designed for those very familiar with rips";

	static function enumByValue(string $value)
	{
		return match ($value) {
			'Beginner' => self::Beginner,
			'Standard' => self::Standard,
			'Hard' => self::Hard
		};
	}
}

const PTS_CORRECT_JOKE = 100;
const PTS_CORRECT_RIP_NAME = 150;
const PTS_CORRECT_GAME = 200;
const PTS_CORRECT_RIPPER = 200;
const PTS_CORRECT_ALT_NAME = 250;

// If the player guessed more than the correct number of jokes and gets one incorrect, they will receive the following penalty.
const PTS_INCORRECT_JOKE = -100;

/**
 * @property private string $id The unique ID of the game. This is essentially the session ID of the game.
 * @property private array $rounds The round objects for the game, i.e. the rips for the game.
 * @property private int $round The current round in the game.
 * @property private int $roundCount The number of rounds in the game.
 * @property private int $score The total score for the game.
 * @property private Settings The settings object that defines the settings to use in the game.
 */
class Game
{
	// Maximum number of rounds you can have in a game
	const MAX_ROUNDS = 15;

	private string $id;
	private array $rounds;
	public readonly int $roundCount;
	private Settings $settings;
	public readonly bool $tooFewRips;

	/**
	 * Creates a new game with the given settings
	 */
	public function __construct($model, int $rounds, Settings $settings)
	{
		$this->settings = $settings;
		$this->rounds = [];

		$maxRoundCount = min($model->getTotalRipsAvailable($settings), self::MAX_ROUNDS);
		if ($maxRoundCount < $rounds) {
			$this->tooFewRips = true;
		} else {
			$this->tooFewRips = false;
		}

		if ($rounds < 1) {
			$rounds = 1;
		} elseif ($rounds > $maxRoundCount) {
			$rounds = $maxRoundCount;
		}
		$this->roundCount = $rounds;
		$this->id = session_id();
	}

	/**
	 * Starts the game. Can only be run once.
	 * @return bool True if the game successfully starts. False otherwise.
	 */
	public function startGame(): bool
	{
		$success = false;

		if (count($this->rounds) == 0) {
			error_log("START!");
		}

		return $success;
	}

	/**
	 * Gets the current round.
	 */
	public function &getCurrentRound(): ?Round
	{
		$round = null;
		if (count($this->rounds) > 0) {
			$round = $this->rounds[count($this->rounds) - 1];
		}
		return $round;
	}

	public function getRoundNumber(): int
	{
		return count($this->rounds);
	}


	/**
	 * Gets the score for the game or the specified round
	 * @param ?int $round The index of the round to get the score from. The index is zero-based, so round 1 is retrieved by specifying '0' as the round.
	 * @return int Returns the score for the specified round. If no round is specified ($round is null), the total score of all rounds is returned.  
	 * If the specified round does not exist, a score of 0 is returned.
	 */
	public function getScore(?int $round = null): int
	{
		$score = 0;
		if ($round !== null) {
			if ($round < count($this->rounds)) {
				$score = $this->rounds[$round]->getScore();
			}
		} else {
			foreach ($this->rounds as $round) {
				$score += $round->getScore();
			}
		}
		return $score;
	}

	/**
	 * Determines if all rounds in the game are complete.
	 * @return bool True if all rounds for the number of rounds in the game are complete.
	 */
	public function isFinished(): bool
	{
		$complete = 0;
		foreach ($this->rounds as $round) {
			$complete += (int)$round->isComplete();
		}
		return $complete == $this->roundCount;
	}

	/**
	 * Returns an array of RipIDs that have been played in the game's rounds.
	 */
	public function getPlayedRips(): array
	{
		$played = [];

		foreach ($this->rounds as $round) {
			array_push($played, $round->ripID);
		}

		return $played;
	}

	/**
	 * Returns an associative array summarising the game's data.
	 */
	public function getGameSummary(): array
	{
		$summary = ['Rounds' => []];

		foreach ($this->rounds as $round) {
			array_push($summary['Rounds'], [
				'Score' => $round->getScore(),
				'MaxScore' => $round->getMaxScore(),
				'RipID' => $round->ripID,
				'RipName' => $round->ripName,
				'GameName' => $round->gameName,
				'YTID' => $round->ytID
			]);
		}
		return $summary;
	}

	/**
	 * Attempts to fetch a rip for the next round, returning it if successful.
	 * Will attempt to search for a rip that has not already been played in a previous round and that matches the given criteria specified in the setting's filters.
	 * If a rip is fetched, its answers are stored in this object on the server. Only the fields that need to be answered are returned.
	 * Special values that are not to be fields are prefixed with an underscore.
	 * @param \RipDB\Model\GuesserModel $model The RipGuesserModel object to query the database with.
	 * @return false|array If no rip was found, false is returned. Else, an associative array of fields to answer and their number of valid answers are returned.
	 */
	public function nextRound(\RipDB\Model\GuesserModel $model): false|array
	{
		$round = false;

		// First, check if the current round is complete or null. Only advance the round if it is complete.
		$current = $this->getCurrentRound();
		if ($current === null || $this->getCurrentRound()->isComplete()) {
			$rip = $model->getRip($this->settings, $this->getPlayedRips());

			// If the rip found is empty, or somehow has no jokes, abort.
			if ($rip !== false && !empty($rip['Jokes'])) {
				// format jokes and rippers to be ID => name key-pair.
				$jokes = [];
				foreach ($rip['Jokes'] as $jokeID => $joke) {
					$jokes[$jokeID] = $joke['JokeName'];
				}
				if (isset($rip['Rippers'])) {
					$rippers = [];
					foreach (($rip['Rippers'] ?? []) as $ripperID => $ripper) {
						$rippers[$ripperID] = $ripper['RipperName'];
					}
				}

				$round = new Round($this->settings->difficulty, $rip['RipID'], $rip['RipYouTubeID'], $jokes, $rip['RipName'] ?? null, $rip['GameName'] ?? null, $rip['RipGame'] ?? null, $rip['RipAlternateName'] ?? null, $rippers ?? null);
				array_push($this->rounds, $round);
			}
		}
		// If the round is not yet complete, get the current round data.
		else {
			$round = $this->getCurrentRound() ?? false;
		}

		if ($round instanceof Round) {
			$round = ['RoundData' => $round->getApplicableFields(), 'RoundNumber' => count($this->rounds)];
		}

		return $round;
	}
}

/**
 * Defines a game's settings
 * 
 * @property private bool $showAnswerCount Shows the number of valid answers a particular rip has for an input (e.g. number of jokes that the rip has).
 * @property private int $roundCount The number of rounds a game will have.
 * @property private int $minJokes The minimum number of jokes a rip must have to appear in a round.
 * @property private int $maxJokes The maximum number of jokes a rip can have to appear in a round.
 * @property private array $metaJokeFilters A list of meta joke IDs a rip must be associated with to appear in a round. Helps to filter rips based on a genre or theme (such as rips featuring a particular artist). Default is no filter (any rip).
 * @property private array $metaFilters A list of meta IDs a rip must be associated with to appear in a round. Similar to $metaJokeFilters, except metas are more broad in scope (i.e. rips featuring 80s music, or rips featuring TV show themes). Default is no filter (any rip).
 */
class Settings
{
	// Rips featured in a round must have at least one joke.
	const MIN_JOKES = 1;
	// Rips featured in a round cannot have more than thirty jokes.
	const MAX_JOKES = 30;
	// Rips featured in a round cannot exceed 10 minutes.
	const MAX_RIP_LENGTH = '10:00';
	// Rips featured in a round cannot be less than 1 second.
	const MIN_RIP_LENGTH = '00:01';

	public readonly bool $showAnswerCount;
	public readonly int $roundCount;
	public readonly int $minJokes;
	public readonly int $maxJokes;
	public readonly string $minLength;
	public readonly string $maxLength;
	public readonly array $metaJokeFilters;
	public readonly array $metaFilters;
	public readonly Difficulty $difficulty;

	public function __construct(bool $showAnswerCount = true, int $roundCount = 3, int $minJokes = 1, int $maxJokes = 2, string $minLength = self::MIN_RIP_LENGTH, string $maxLength = self::MAX_RIP_LENGTH, ?array $metaJokeFilters = [], ?array $metaFilters = [], Difficulty $difficulty = Difficulty::Beginner)
	{
		$this->showAnswerCount = $showAnswerCount;

		if ($roundCount < 1) {
			$roundCount = 1;
		} elseif ($roundCount > Game::MAX_ROUNDS) {
			$roundCount = Game::MAX_ROUNDS;
		}
		$this->roundCount = $roundCount;

		// Ensure the min and max for jokes are not exceeded
		if ($minJokes < self::MIN_JOKES) {
			$minJokes = self::MIN_JOKES;
		} elseif ($minJokes > $maxJokes || $minJokes > self::MAX_JOKES) {
			$minJokes = min($maxJokes, self::MAX_JOKES);
		}

		if ($maxJokes > self::MAX_JOKES) {
			$maxJokes = self::MAX_JOKES;
		} elseif ($maxJokes < $minJokes) {
			$maxJokes = $minJokes;
		}

		$this->minJokes = $minJokes;
		$this->maxJokes = $maxJokes;
		$this->minLength = $minLength;
		$this->maxLength = $maxLength;
		$this->metaJokeFilters = $metaJokeFilters ?? [];
		$this->metaFilters = $metaFilters ?? [];
		$this->difficulty = $difficulty;
	}
}

class Round
{
	// Standard Round properties
	public readonly int $ripID;
	private int $score = 0;
	private int $maxScore = 0; // The maximum possible score available for the round.
	private bool $complete = false;
	public readonly string $ytID;
	private readonly Difficulty $difficulty;

	// Variables for guessable items
	private array $jokes;
	public readonly ?string $ripName; // The name of the rip. Displayed in easier difficulties, for guessing on harder ones.
	public readonly ?string $gameName; // Used to display the name of the game the rip is from in easier difficulties.
	private ?int $gameID; // The ID of the game, should the game need to be guessed.
	private ?string $altName; // The alternative name of the game.
	private ?array $rippers; // The rippers associated to the rip.

	/**
	 * Creates a new round for the rip guesser game.
	 * The passed values are the correct answers for the rip of the round.
	 * If a rip does not have any of the data for a particular parameter (due to a difficulty setting or just not having that data), it is set to null and is not counted towards points.
	 * @param int $ripID The ID of the rip for the round.
	 * @param string $ytID The ID of the YouTube video that plays the rip.
	 * @param array $jokes An associative array of jokes associated to the rip of the round. The key should be the ID of the joke, and the value the name.
	 * @param ?string $ripName The name of the rip.
	 * @param ?string $gameName The name of the game.
	 * @param ?int $gameID The ID of the game associated to the rip.
	 * @param ?string $altName The alternative name of the rip.
	 * @param ?array $rippers The rippers who made the rip.
	 */
	public function __construct(Difficulty $difficulty, int $ripID, string $ytID, array $jokes, ?string $ripName = null, ?string $gameName = null, ?int $gameID = null, ?string $altName = null, ?array $rippers = null)
	{
		$this->difficulty = $difficulty;
		$this->ripID = $ripID;
		$this->ytID = $ytID;
		$this->jokes = $jokes;
		$this->gameID = $gameID;
		$this->gameName = $gameName;
		$this->ripName = $ripName;
		$this->altName = $altName;
		$this->rippers = $rippers;
	}

	public function isComplete(): bool
	{
		return $this->complete;
	}

	/**
	 * @return int The calculated score of this round.
	 */
	public function getScore(): int
	{
		return $this->score;
	}

	/**
	 * @return int The maximum obtainable score for this round.
	 */
	public function getMaxScore(): int
	{
		return $this->maxScore;
	}

	/**
	 * Retrieves all data fields applicable to the rounds based on its difficulty.
	 * Used for the client-side JavaScript so it knows which fields to create inputs for.  
	 * - Keys that are prefixed with an underscore are used to display its value to the user.  
	 * - Keys without an underscore define fields that need to be given an answer. Their values are a number of how many inputs are needed to be supplied.
	 * @return array An associative array where the keys define what inputs are allowed to appear.
	 */
	public function getApplicableFields(): array
	{
		$fields = ['_RipYouTubeID' => $this->ytID, 'Jokes' => count($this->jokes)];

		switch ($this->difficulty) {
			case Difficulty::Beginner:
				$fields['_RipName'] = $this->ripName;
				$fields['_GameName'] = $this->gameName;
				$this->maxScore += count($this->jokes) * PTS_CORRECT_JOKE;
				break;
			// Hard and standard difficulty have the game and rip name hidden.
			case Difficulty::Hard:
				if (is_array($this->rippers)) {
					$fields['Rippers'] = count($this->rippers);
					$this->maxScore += count($this->rippers) * PTS_CORRECT_RIPPER;
				}
				if ($this->altName !== null) {
					$fields['AlternateName'] = $this->altName;
					$this->maxScore += PTS_CORRECT_ALT_NAME;
				}
			case Difficulty::Standard:
				$fields['GameName'] = 1;
				$fields['RipName'] = 1;
				$this->maxScore += PTS_CORRECT_GAME;
				$this->maxScore += PTS_CORRECT_RIP_NAME;
				break;
		}

		return $fields;
	}

	/**
	 * Checks the submitted answers against the correct ones and calculates the score for the round.
	 * @return array The correct answers for each input and a tally of the number of correct answers for each input (based on the difficulty)
	 */
	public function checkSubmission(array $data): array
	{
		$results = ['Correct' => [], 'Answers' => []];
		$score = 0;
		$penalty = 0;

		// Check the submitted jokes against those that are correct.
		$jokes = array_keys($this->jokes);
		$correctJokes = 0;
		foreach ($data['jokes'] ?? [] as $jokeID) {
			if (in_array($jokeID, $jokes)) {
				$correctJokes++;
			}
		}
		$score += ($correctJokes * PTS_CORRECT_JOKE);
		$results['Answers']['Jokes'] = $this->jokes;
		$results['Correct']['Jokes'] = $correctJokes;

		// Check the other variables of the rip
		switch ($this->difficulty) {
			case Difficulty::Beginner:
				break;
			case Difficulty::Hard:
				if ($this->altName !== null) {
					$correctName = (int)($data['altName'] == $this->altName);
					$results['Answers']['AlternateName'] = $this->altName;
					$results['Correct']['AlternateName'] = $correctName;
					$score += ($correctName * PTS_CORRECT_ALT_NAME);
				}
				if (!empty($this->rippers)) {
					$rippers = array_keys($this->rippers);
					$correctRippers = 0;
					foreach ($data['rippers'] ?? [] as $ripperID) {
						if (in_array($ripperID, $rippers)) {
							$correctRippers++;
						}
					}
					$score += ($correctRippers * PTS_CORRECT_RIPPER);
					$results['Answers']['Rippers'] = $this->rippers;
					$results['Correct']['Rippers'] = $correctRippers;
				}
			case Difficulty::Standard:
				$correctName = (int)($data['rip'] == $this->ripID);
				$results['Answers']['RipName'] = $this->ripName;
				$results['Correct']['RipName'] = $correctName;
				$score += ($correctName * PTS_CORRECT_RIP_NAME);

				$correctGame = (int)($data['game'] == $this->gameID);
				$results['Answers']['GameName'] = $this->gameName;
				$results['Correct']['GameName'] = $correctGame;
				$score += ($correctGame * PTS_CORRECT_GAME);
				break;
		}

		// Apply the penalty, if one is given
		// TODO
		$this->score = ($score - $penalty);

		// Set the round as complete so it does not play again.
		$this->complete = true;

		return $results;
	}
}
