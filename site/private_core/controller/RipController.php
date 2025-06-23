<?php

namespace RipDB\Controller;

use RipDB\Model as m;
use RipDB\Error;

require_once('Controller.php');
require_once('private_core/model/RipModel.php');
require_once('private_core/objects/DataValidators.php');
require_once('private_core/objects/Paginator.php');

/**
 * @property \RipDB\Model\RipModel $model
 */
class RipController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

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
			case 'rips':
				$useAltName = ($_GET['use_secondary'] ?? 0) == 1;
				$ripCount = $this->model->getRipCount(
					$_GET['search'] ?? null,
					$_GET['tags'] ?? [],
					$_GET['jokes'] ?? [],
					$_GET['games'] ?? [],
					$_GET['rippers'] ?? [],
					$_GET['genres'] ?? [],
					$_GET['meta-jokes'] ?? [],
					$_GET['metas'] ?? [],
					$_GET['channel'] ?? null,
					$useAltName
				);
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of rips
				$rips = [];
				$offset = $this->getOffset($ripCount, '/rips');
				$rips = $this->model->searchRips(
					$rowCount,
					$offset,
					$_GET['search'] ?? null,
					$_GET['tags'] ?? [],
					$_GET['jokes'] ?? [],
					$_GET['games'] ?? [],
					$_GET['rippers'] ?? [],
					$_GET['genres'] ?? [],
					$_GET['meta-jokes'] ?? [],
					$_GET['metas'] ?? [],
					$_GET['channel'] ?? null,
					$useAltName
				);
				$this->setData('results', $rips);

				// Get search filters
				$this->setData('tags', $this->model->getFilterResults('Tag', $_GET['tags'] ?? []));
				$this->setData('jokes', $this->model->getFilterResults('Joke', $_GET['jokes'] ?? []));
				$this->setData('games', $this->model->getFilterResults('Game', $_GET['games'] ?? []));
				$this->setData('rippers', $this->model->getFilterResults('Ripper', $_GET['rippers'] ?? []));
				$this->setData('genres', $this->model->getFilterResults('Genre', $_GET['genres'] ?? []));
				$this->setData('metaJokes', $this->model->getFilterResults('MetaJoke', $_GET['meta-jokes'] ?? []));
				$this->setData('metas', $this->model->getFilterResults('Meta', $_GET['metas'] ?? []));
				$this->setData('channel', $this->model->getFilterResults('Channel', $_GET['channel'] ?? null));

				// If any filter is given, make sure the details element is open by setting its "open" attribute
				if (
					!empty($_GET['tags'] ?? null) ||
					!empty($_GET['jokes'] ?? null) ||
					!empty($_GET['games'] ?? null) ||
					!empty($_GET['rippers'] ?? null) ||
					!empty($_GET['genres'] ?? null) ||
					!empty($_GET['meta-jokes'] ?? null) ||
					!empty($_GET['metas'] ?? null) ||
					!empty($_GET['channel'] ?? null)
				) {
					$this->setData('open', 'open');
				} else {
					$this->setData('open', '');
				}

				// Pagination values
				$recordStart = (($page - 1) * $rowCount) + 1;
				$recordEnd = $page * $rowCount;

				if ($recordEnd > $ripCount) {
					$recordEnd = $ripCount;
				}

				$this->setData('RecordStart', $recordStart);
				$this->setData('RecordEnd', $recordEnd);
				$this->setData('Page', $page);
				$this->setData('Count', $rowCount);
				$this->setData('RipCount', $ripCount);
				$this->setData('pagination', $this->buildPagination($ripCount, '/rips'));
				break;
			case 'rip':
				if (array_key_exists('random', $data)) {
					$ripId = $this->model->getRandomRip();
					\Flight::redirect("/rips/$ripId");
					die();
				} else {
					$rip = null;
					if (is_numeric($data['id'])) {
						$rip = $this->model->getRip($data['id']);
						$this->setData('jokes', $this->sortJokesByTimestamp($rip['Jokes'] ?? []));
					}

					$this->setData('rip', $rip);
					if ($rip !== null) {
						$this->setPageTitle($rip['RipName']);
					} else {
						$this->setPageTitle('Rip not found');
					}
				}
				break;
			case 'edit-rip':
				$rip = null;
				if (is_numeric($data['id'])) {
					$rip = $this->model->getRip($data['id']);
					$this->setData('jokes', $this->sortJokesByTimestamp($rip['Jokes'] ?? []));
				}

				// Modify rippers, genres and jokes to only contain necessary data to prefill the input table elements
				$temp = [];
				foreach ($rip['Genres'] as $genre) {
					$temp[$genre['GenreID']] = $genre['GenreName'];
				}
				$rip['Genres'] = $temp;

				$temp = [];
				foreach ($rip['Rippers'] ?? [] as $ripper) {
					array_push($temp, ['Ripper' => [$ripper['RipperID'] => $ripper['RipperName']], 'Alias' => $ripper['Alias'] ?? null]);
				}
				$rip['Rippers'] = $temp;

				$temp = [];
				foreach ($rip['Jokes'] ?? [] as $joke) {
					$timestamps = json_decode($joke['JokeTimestamps'], true);
					if (!empty($timestamps)) {
						foreach ($timestamps as $timestamp) {
							array_push($temp, ['Joke' => [$joke['JokeID'] => $joke['JokeName']], 'Start' => $this->formatTimestamp($timestamp['start'] ?? null), 'End' => $this->formatTimestamp($timestamp['end'] ?? null)]);
						}
					} else {
						array_push($temp, ['Joke' => [$joke['JokeID'] => $joke['JokeName']], 'Start' => null, 'End' => null]);
					}
				}
				$rip['Jokes'] = $temp;
				$this->setData('rip', $rip);
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

		foreach ($jokes as $joke) {
			$timestamps = json_decode($joke['JokeTimestamps'], true);

			if (!empty($timestamps)) {
				foreach ($timestamps as $time) {
					array_push($sortedTimes, $time['start']);
					array_push($sortedJokes, ['JokeID' => $joke['JokeID'], 'start' => $this->formatTimestamp($time['start'] ?? null), 'end' => $this->formatTimestamp($time['end'] ?? null)]);
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
	 * Formats the given string timestamp by inserting colons between every second pair of numbers.
	 * e.g. 00:02:32 --> 02:32 (2 min, 32 seconds)
	 * e.g. 00:00:34 -> 00:34 (34 seconds)
	 * e.g. 01:15:08 ->  01:15:08 (stays the same)
	 * @param string $timestamp The timestamp string to insert colons into.
	 * @return string The formatted timestamp.
	 */
	private function formatTimestamp(?string $timestamp): string
	{
		$formatted = $timestamp;
		if (!empty($timestamp)) {
			$split = explode(':', $timestamp);

			// If the split contains three segments (hours, mins, secs), check if the hours is not "00". If it is, remove it.
			if (count($split) == 3) {
				if ($split[0] == "00") {
					$formatted = substr($timestamp, 3);
				}
			}
		} else {
			$formatted = '';
		}
		return $formatted;
	}

	/**
	 * Handles the submission of the new rip form.
	 * @return Error|string Returns an Error if an error occurred, or a string of a URI to redirect to upon completion.
	 */
	public function submitRequest(?array $extraData = null): array|string
	{
		$result = null;
		$validated = [];

		// Only submit the form if the form is submitted from the new-rip page.
		switch ($this->getPage()) {
			case 'edit-rip':
				// The rip ID is validated in the stored procedure to ensure it exists.
				$validated['RipIDTarget'] = $this->validateNumber($extraData['id'], 'The given Rip ID is invalid.', null, 1);
			case 'new-rip':
				// Validate data in order of stored procedure parameters.
				$validated['RipName'] = $this->validateString($_POST['name'], 'The given rip name is invalid.', 1024);
				$validated['AlternateName'] = $this->validateString($_POST['altName'], 'The given alternate name is invalid.');
				$validated['Description'] = $this->validateString($_POST['description'], 'The given description is invalid.');
				$validated['UploadDate'] = $this->validateDateInput($_POST['date'], 'The given rip date is invalid.');
				$validated['RipLength'] = $this->validateTimestamp($_POST['length']);
				$validated['URL'] = $this->validateString($_POST['url'], 'The given rip URL is invalid.', null, null, '/(?:http[s]?:\/\/.)?(?:www\.)?[-a-zA-Z0-9@%._\+~#=]{2,256}\.[a-z]{2,6}\b(?:[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)/');
				$validated['YTID'] = $this->validateString($_POST['ytId'], 'The given youTube ID is invalid.', null, null, '/[A-Za-z0-9_-]{11}/');;
				$validated['AltURL'] = $this->validateString(null);
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

				if ($this->getPage() == 'new-rip') {
					$submission = $this->model->submitFormData($validated, 'usp_InsertRip');
					var_dump($submission);
					if ($submission == true) {
						$result = '/rips';
					} else {
						$result = $submission;
					}
				} else {
					$submission = $this->model->submitFormData($validated, 'usp_UpdateRip');
					if ($submission == true) {
						$result = '/rips/' . $extraData['id'];
					} else {
						$result = $submission;
					}
				}

				break;
		}

		return $result;
	}
}
