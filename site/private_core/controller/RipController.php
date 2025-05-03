<?php

namespace RipDB;

require_once('Controller.php');
require_once('private_core/model/RipModel.php');

class RipController extends Controller
{
	public function __construct()
	{
		parent::__construct(new RipModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$rip = null;
		if (is_numeric($data['id'])) {
			$rip = $this->model->getRip($data['id']);
			$this->setData('jokes', $this->sortJokesByTimestamp($rip['Jokes']));
		}

		$this->setData('rip', $rip);
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
		function formatTimestamp(string $timestamp): string
		{
			$formatted = $timestamp;
			$formatted = substr_replace($formatted, ':', strlen($formatted) - 2, 0);

			// If the timestamp represents a time longer than an hour:
			if (strlen($timestamp) > 4) {
				$formatted = substr_replace($formatted, ':',  strlen($formatted) - 5, 0);
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
}
