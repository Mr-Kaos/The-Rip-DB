<?php

namespace RipDB\Controller;

use RipDB\Model as m;
use RipDB\Error;

require_once('Controller.php');
require_once('private_core/model/RipModel.php');
require_once('private_core/objects/DataValidators.php');

class RipController extends Controller
{
	use \RipDB\DataValidator;
	public function __construct(string $page)
	{
		parent::__construct($page, new m\RipModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'rip':
				$rip = null;
				if (is_numeric($data['id'])) {
					$rip = $this->model->getRip($data['id']);
					$this->setData('jokes', $this->sortJokesByTimestamp($rip['Jokes']));
				}

				$this->setData('rip', $rip);
				break;
			case 'new-rip':
				$this->setData('rippers', $this->model->getRippers());
				$this->setData('channels', $this->model->getChannels());
				$this->setData('games', $this->model->getGames());
				$this->setData('jokes', $this->model->getJokes());
				$this->setData('genres', $this->model->getGenres());
				break;
		}
	}

	/**
	 * Sorts the jokes from the rip in order of their timestamps (starting time).
	 * If a joke has multiple timestamps, it will have multiple entries in the array.
	 * @param array $jokes The jokes from the rip to sort.
	 */
	private function sortJokesByTimestamp(array $jokes): array
	{
		$sortedTimes = [];
		$sortedJokes = [];

		/**
		 * Formats the given string timestamp by inserting colons between every second pair of numbers.
		 * e.g. 0132 --> 01:32 (1 min, 32 seconds)
		 * e.g. 10232 -> 1:02:49 (1 hr, 2 min, 49 seconds)
		 * @param string $timestamp The timestamp string to insert colons into.
		 * @return string The formatted timestamp.
		 */
		function formatTimestamp(?string $timestamp): string
		{
			if (!empty($timestamp)) {
				$formatted = $timestamp;
				$formatted = substr_replace($formatted, ':', strlen($formatted) - 2, 0);

				// If the timestamp represents a time longer than an hour:
				if (strlen($timestamp) > 4) {
					$formatted = substr_replace($formatted, ':',  strlen($formatted) - 5, 0);
				}
			} else {
				$formatted = '';
			}
			return $formatted;
		}

		foreach ($jokes as $joke) {
			$timestamps = json_decode($joke['JokeTimestamps'], true);

			if (!empty($timestamps)) {
				foreach ($timestamps as $time) {
					array_push($sortedTimes, $time['start']);
					array_push($sortedJokes, ['JokeID' => $joke['JokeID'], 'start' => formatTimestamp($time['start']), 'end' => formatTimestamp($time['end'])]);
				}
			} else {
				array_push($sortedTimes, null);
				array_push($sortedJokes, ['JokeID' => $joke['JokeID']]);
			}
		}

		array_multisort($sortedTimes, $sortedJokes);

		return $sortedJokes;
	}

	/**
	 * Handles the submission of the new rip form.
	 * @return Error|string Returns an Error if an error occurred, or a string of a URI to redirect to upon completion.
	 */
	public function submitRequest(): Error|string
	{
		$result = null;
		// Only submit the form if the form is submitted from the new-rip page.
		if ($this->getPage() == 'new-rip') {
			// Validate data in order of stored procedure parameters.
			$validated = [];
			$validated['RipName'] = $this->validateString($_POST['name'], 'The given rip name is invalid.', 1024);
			$validated['AlternateName'] = $this->validateString($_POST['altName'], 'The given alternate name is invalid.');
			$validated['Description'] = $this->validateString($_POST['description'], 'The given description is invalid.');
			$validated['UploadDate'] = $this->validateDateInput($_POST['date'], 'The given rip date is invalid.');
			$validated['RipLength'] = $this->validateTimestamp($_POST['length']);
			$validated['URL'] = $this->validateString($_POST['url'], 'The given rip URL is invalid.', null, null, '/(?:http[s]?:\/\/.)?(?:www\.)?[-a-zA-Z0-9@%._\+~#=]{2,256}\.[a-z]{2,6}\b(?:[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)/');
			$validated['Game'] = $this->validateFromList($_POST['game'], $this->model->getGames(true));
			$validated['Channel'] = $this->validateFromList($_POST['channel'], $this->model->getChannels(true));
			$validated['Genres'] = $this->validateArray($_POST['genres'], 'validateFromList', [$this->model->getGenres(true)], 'One or more of the given genres do not exist in the database.');

			$jokes = $this->validateArray($_POST['jokes'], 'validateFromList', [$this->model->getJokes(true)], 'One or more of the given jokes do not exist in the database.', false);
			$starts = $this->validateArray($_POST['jokeStart'], 'validateTimestamp', [], 'One of the given timestamps are invalid', false);
			$ends = $this->validateArray($_POST['jokeEnd'], 'validateTimestamp', [], 'One of the given timestamps are invalid', false);
			if ($jokes instanceof Error) {
				$validated['Jokes'] = $jokes;
			} elseif ($starts instanceof Error) {
				$validated['Jokes'] = $starts;
			} elseif ($ends instanceof Error) {
				$validated['Jokes'] = $ends;
			} else {
				$validated['Jokes'] = [];
				for ($i = 0; $i < count($jokes); $i++) {
					$jokeId = $jokes[$i];
					if (!is_array($validated['Jokes'][$jokeId] ?? null)) {
						$validated['Jokes'][$jokeId] = ['timestamps' => [], 'comment' => null];
					}

					array_push($validated['Jokes'][$jokeId]['timestamps'], ['start' => $starts[$i], 'end' => $ends[$i]]);
				}
				$validated['Jokes'] = json_encode($validated['Jokes']);
			}

			$rippers = $this->validateArray($_POST['rippers'], 'validateFromList', [$this->model->getRippers(true)], 'One or more of the given rippers do not exist in the database.', false);
			$aliases = $this->validateArray($_POST['aliases'], 'validateString', [], 'One of the given given alias names is invalid.', false);
			if ($rippers instanceof Error) {
				$validated['Rippers'] = $rippers;
			} elseif ($aliases instanceof Error) {
				$validated['Rippers'] = $aliases;
			} else {
				$validated['Rippers'] = json_encode(array_combine($rippers, $aliases), JSON_NUMERIC_CHECK);
			}

			$submission = $this->model->submitFormData($validated, 'usp_InsertRip');
			if ($submission == true) {
				$result = '/rips';
			} else {
				$result = $submission;
			}
		}

		return $result;
	}
}
