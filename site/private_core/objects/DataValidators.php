<?php

namespace RipDB;

/**
 * Error class. Used when validation fails - an instance of this object is returned.
 */
class Error
{
	private string $message;
	private string $inputName;

	public function __construct(string $message)
	{
		$this->message = $message;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	public function setInput(string $inputName): void
	{
		$this->inputName = $inputName;
	}
}

/**
 * This trait defines various data validation functions to prevent bad data from being sent to the database.
 */
trait DataValidator
{
	/**
	 * Validates a string input. Ensures no invalid HTML characters exist.
	 * @param ?string $input The string to validate. If empty or null, null is returned.
	 * @param ?int $minLength The minimum length allowed for the string. Optional. If the string is shorter than this length, False is returned.
	 * @param ?int $maxLength The maximum length allowed for the string. Optional. If the string is longer than this length, it is trimmed.
	 * @param ?string $regex A regular expression to validate the string against. Optional. If any match(es) are found, it will omit any part of the string that is not matched.
	 * @return string|Error|null The validated string, or if the validation fails, an Error. If an empty or null input is given, it is returned.
	 */
	protected function validateString(?string $input, string $errorMessage = 'The given value is not valid.', ?int $maxLength = null, ?int $minLength = null, ?string $regex = null): Error|string|null
	{
		$validated = $input;

		if (!empty($input)) {
			// Set validation to false if the input is less than the minimum length
			if (!is_null($minLength) && strlen($input) < $minLength) {
				$validated = new Error($errorMessage . ' The length of the string is too short.');
			} elseif (!$validated instanceof Error) {
				if (!is_null($maxLength) && strlen($input) > $maxLength) {
					$validated = substr($input, 0, $maxLength);
				}

				if (!is_null($regex)) {
					preg_match_all($regex, $input, $matches);
					$validated = '';

					if (is_array($matches[0])) {
						$matches = $matches[0];
					}

					foreach ($matches as $match) {
						$validated .= $match;
					}

					$validated = htmlspecialchars($validated);

					if ($validated == "") {
						$validated = null;
					}
				} else {
					$validated = htmlspecialchars($validated);
				}
			}
		} else {
			$validated = null;
		}

		return $validated;
	}

	/**
	 * Validates a numeric input. If given, will check that the value is between the given range. Will always check to ensure the value given is numeric.
	 * @param mixed $input The input to validate as numeric. If the value is not numeric, it will return false.
	 * @param int $min The minimum allowed value for the input, optional. If the value of $input is less than this value, it will be validated to this value.
	 * @param int $max The maximum allowed value for the input, optional. If the value of $input is greater than this value, it will be validated to this value.
	 * @return false|int Error if the given value is not numeric, an int if the value is numeric.
	 */
	protected function validateNumber(mixed $input, string $errorMessage = 'The given value is not numeric.', ?int $max = null, ?int $min = null): Error|int|float|null
	{
		$validated = new Error($errorMessage);

		if (!empty($input) || $input === 0 || $input === '0') {
			if (is_numeric($input)) {
				$validated = $input;
				if (!is_null($min) && $input < $min) {
					$validated = $min;
				}

				if ($validated !== false) {
					if (!is_null($max) && $input > $max) {
						$validated = $max;
					}
				}
			}
		} else {
			$validated = null;
		}

		return $validated;
	}

	/**
	 * Validates a boolean input, typically given from a checkbox. The value returned will be a 1 or 0 to conform the the SQL BIT data type. If a boolean is required to be returned,
	 * pass true in the second parameter.
	 * @param ?string $input The input to validate for boolean.
	 */
	protected function validateBool(?string $input, string $errorMessage = 'Supplied value is not a boolean.', bool $returnAsBool = false): Error|bool|int
	{
		$validated = new Error($errorMessage);
		if ($input === 'on' || $input == 1 || $input === 'true') {
			$validated = 1;
		} else {
			$validated = 0;
		}

		if ($returnAsBool) {
			$validated = ($validated == 1) ? true : false;
		}

		return $validated;
	}

	/**
	 * Validates a date input. Takes in a date input and converts it to UTC time before inserting it into the database. 
	 * @param ?string $input The input to validate for date.
	 * 
	 */
	protected function validateDateInput(?string $input, string $errorMessage = 'Given value is not a date.'): Error|string
	{
		$validated = new Error($errorMessage);;
		if ($input) {
			$dateTime = new \DateTime($input);
			$validated = $dateTime->format('Y-m-d H:i:s');
		}
		return $validated;
	}

	/**
	 * Validates the given string to ensure it is a valid timestamp
	 */
	protected function validateTimestamp(string $time, string $errorMessage = 'Supplied timestamp is not formatted correctly.'): Error|string
	{
		$validated = new Error($errorMessage);
		error_log("REMINDER: ADD NUMERIC VALIDATION TO TIMES!!!");

		// Ensure that colons are placed in the correct positions
		if (strlen($time) > 2) {
			if ($time[strlen($time) - 3] == ':') {
				// If the string is longer than 5 characters, i.e. specifies hours, check that the hours colon is correctly placed.
				if (strlen($time) > 5) {
					if ($time[strlen($time) - 6] == ':') {
						$validated = str_replace(':', '', $time);

						// If the hours are just zeros, remove them.
						if (str_starts_with($validated, '00')) {
							$validated = substr($validated, 2);
						}
					}
				} else {
					$validated = str_replace(':', '', $time);
				}

				// If the validated timestamp is odd, add a leading 0.
				if (strlen($validated) % 2 == 1) {
					$validated = '0' . $validated;
				}
			}
		}

		return $validated;
	}

	/**
	 * Validates the data in the given array by validating each value with the given function.
	 * @param array|string|null $data The array with the to validate. Must be a 1-dimensional array. If a string or null is passed, it is assumed that there are no values in the array (i.e. an empty string)
	 * @param $func The name of a function within the DataValidators trait to use in validating each value in the array. This function must be within the DataValidators trait to work.
	 * @param array $params An array of parameters to pass into the validator function specified by $func. By default, the $data given is passed as the first parameter in this function.
	 * @param bool $outputAsJSONArray Determines if the function should return a JSON array of the validated array or just return an array. By default a JSON array is returned for use in Stored Procedures.
	 * @return array The validated array values.
	 * @return string The validated array as a JSON array. Only returns as string if $outputAsJSONArray is set to true.
	 * @return false If validation fails in any of the given values, Error is returned.
	 */
	protected function validateArray(array|string|null $data, $func, array $params = [], string $errorMessage = 'The given list contains an invalid value.', bool $outputAsJSONArray = true): array|string|Error
	{
		$result = [];

		if (!empty($data)) {
			foreach ($data as $val) {
				$funcParams = array_merge([$val], $params);
				$out = call_user_func_array("self::$func", $funcParams);
				if (!$out instanceof Error) {
					array_push($result, $out);
				} else {
					$out->setMessage($errorMessage);
					$result = $out;
					break;
				}
			}
		}

		if ($outputAsJSONArray && !($result instanceof Error)) {
			$result = json_encode($result, JSON_NUMERIC_CHECK);
		}

		return $result;
	}

	/**
	 * Validates the given value is in the list of given options.
	 * @param string|array $value The value to search for in the list. If an array, all values in the array must exist in the $options.
	 * @param array $list The list containing items, where $value should be one of them.
	 * @param bool $looseCheck If true, as long as the value is partially contained in the list, it is valid. 
	 * 	E.g. if searching for the word "plan" in a list of words, if the list contains "plant" or "airplane", the keyword is considered found.
	 * @return string|array|false If the value exists in the list, it returns it. If it does not, false is returned. Note that the value from the list is returned, and not the given $value.
	 */
	protected function validateFromList(string|array|null $value, array $list, string $errorMessage = 'The given value does not exist in the list.', bool $looseCheck = false): string|array|Error
	{
		$validated = new Error($errorMessage);
		if (is_array($value)) {
			$allMatch = true;
			$matches = [];
			foreach ($value as $val) {
				if ($looseCheck) {
					// Loop through the given list until all values in the given array are found.
					foreach ($list as $option) {
						if (strpos((string)$option, (string)$val) !== false) {
							array_push($matches, $option);
							break;
						}
					}
				} elseif (in_array($val, $list)) {
					$validated = $list[array_search($val, $list)];
					break;
				}
			}

			if ($looseCheck) {
				if (count($matches) == count($value)) {
					$validated = $matches;
				}
			} else {
				$validated = $allMatch ? $value : $validated;
			}
		} else {
			if ($looseCheck) {
				// Loop through the given list until a match is found.
				foreach ($list as $option) {
					if (strpos((string)$option, (string)$value) !== false) {
						$validated = $option;
						break;
					}
				}
			} elseif (in_array($value, $list)) {
				$validated = $list[array_search($value, $list)];
			}
		}

		return $validated;
	}

	protected function checkForErrors(array $input)
	{
		$safe = true;
		foreach ($input as $value) {
			if ($value instanceof Error) {
				$safe = false;
				break;
			}
		}

		return $dafe;
	}
}
