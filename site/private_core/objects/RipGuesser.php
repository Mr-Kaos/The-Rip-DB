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
	private int $round = 0;
	private int $roundCount;
	private int $score = 0;
	private Settings $settings;

	/**
	 * Creates a new game with the given settings
	 */
	public function __construct(int $rounds, Settings $settings)
	{
		$this->settings = $settings;
		$this->rounds = [];

		if ($rounds < 1) {
			$rounds = 1;
		} elseif ($rounds > self::MAX_ROUNDS) {
			$rounds = self::MAX_ROUNDS;
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

		if ($this->round == 0) {
		}

		return $success;
	}

	/**
	 * Gets the current round.
	 */
	public function getRound() {}

	/**
	 * Returns an array of RipIDs that have been played in the game's rounds.
	 */
	public function getPlayedRips(): array
	{
		$played = [];

		foreach ($this->rounds as $round) {
			array_push($round->ripID);
		}

		return $played;
	}

	/**
	 * Advances the round by one.
	 */
	public function nextRound()
	{
		// First, check if the current round is complete or null. Only advance the round if it is complete.
		if ($this->rounds[$this->round] !== null || !$this->rounds[$this->round]->isComplete()) {
		}
	}

	/**
	 * Fetches a rip based on the game's settings and previous rounds.
	 * Will attempt to search for a rip that has not already been played in a previous round and that matches the given criteria specified in the setting's filters.
	 * If a rip is fetched, its answers are stored in this object on the server. Only the fields that need to be answered are returned.
	 * @param GuesserModel $model The RipGuesserModel object to query the database with.
	 * @return false|array If no rip was found, false is returned. Else, an associative array of fields to answer and their number of valid answers are returned.
	 */
	public function fetchRip(\RipDB\Model\GuesserModel $model): false|array
	{
		$rip = $model->getRip($this->settings, $this->getPlayedRips());
		$fields = false;

		if (!empty($rip)) {
			$fields = ['RipYouTubeID' => $rip['RipYouTubeID']];
			// From the rip data, only return the fields that are to be filled and the number of answers they require.
			switch ($this->settings->difficulty) {
				case Difficulty::Hard:
					$fields['AlternateName'] = 1;
					$fields['Rippers'] = count($rip['Rippers']);
				case Difficulty::Standard:
					$fields['GameName'] = 1;
				case Difficulty::Beginner:
					$fields['Jokes'] = count($rip['Jokes']);
					break;
			}
		}

		return $fields;
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
	private int $score;
	private bool $complete = false;

	// Variables for guessable items
	private int $gameID;
	private string $ripName;
	private string $altName;
	private array $jokeIDs;
	private array $ripperIDs;

	public function __construct(array $ripData) {}

	public function isComplete(): bool
	{
		return $this->complete;
	}
}
