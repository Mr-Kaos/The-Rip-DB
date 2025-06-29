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
				$validated = new Error($errorMessage . ' The length of the string must be longer than ' . $minLength . ' characters.');
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

					if (empty($matches)) {
						$validated = new Error($errorMessage . ' The given value does not match the required pattern.');
					} else {
						foreach ($matches as $match) {
							$validated .= $match;
						}

						$validated = htmlspecialchars($validated);

						if ($validated == "") {
							$validated = null;
						}
					}
				} else {
					$validated = htmlspecialchars($validated);
				}
			}
		} elseif ($minLength > 0) {
			$validated = new Error($errorMessage . ' A value must be given and be longer than ' . $minLength . ' characters.');
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
	 * Validates the given string to ensure it is a valid timestamp for usage in MySQL.  
	 * Examples:  
	 * 	`05:23` => `00:05:23` (valid)  
	 * 	`01:12:03` => `01:12:03` (valid)  
	 * 	`03` => `00:00:03` (valid)  
	 * 	`21:2` => `00:21:02` (cleansed, valid)  
	 * 	`2:7` => `00:02:07` (cleansed, valid) - This example is to show that the cleansed value always prepends a zero to segments with one value, i.e. "`7`" doesn't change to "`70`", which would exceed 59 seconds/minutes.  
	 * 	`1:4:5` => `01:04:05` (cleansed, valid)  
	 * 	`12:234` => ERROR (invalid)  
	 * 	`05:0.24` => ERROR (invalid) - Decimals are not allowed.  
	 * @param string $time The timestamp to validate.
	 * @param string $errorMessage The error message to display should validation fail.
	 * @param ?string $maxTimestamp A timestamp dictating the maximum allowed length. (e.g. 01:15:30 for 1hr, 15 mins and 30 seconds.) This is recursively validated in case you accidentally enter an invalid timestamp here.
	 * @param ?string $minTimestamp A timestamp dictating the minimum allowed length. (e.g. 00:20 for 20 seconds.)
	 * @return Error|string|null If valid, the timestamp without colons is returned.
	 */
	protected function validateTimestamp(string $time, string $errorMessage = 'Supplied timestamp is not formatted correctly.', ?string $maxTimestamp = null, ?string $minTimestamp = null): Error|string|null
	{
		$validated = new Error($errorMessage);

		if ($time != "") {
			if ($maxTimestamp !== null) {
				$maxTimestamp = $this->validateTimestamp($maxTimestamp, 'max validate invalid');
			}
			if ($minTimestamp !== null) {
				$minTimestamp = $this->validateTimestamp($minTimestamp, 'min validate invalid');
			}

			// Validate each segment of the timestamp
			$segments = explode(':', $time);
			$error = false;
			for ($i = 0; $i < count($segments); $i++) {
				// If the segment contains a non-numeric character (including a decimal point), fail.
				if (!is_numeric($segments[$i]) || str_contains($segments[$i], '.')) {
					$error = true;
					break;
				}
				// Each segment must be no more than two characters
				elseif (strlen($segments[$i]) > 2) {
					$error = true;
					break;
				}
				// If a segment is one character, prepend a zero
				elseif (strlen($segments[$i]) < 2) {
					$segments[$i] = '0' . $segments[$i];
				}
			}

			// If no errors were encountered, check for max and min length and prepare string timestamp
			if (!$error) {
				$time = implode(':', $segments);
				if (count($segments) < 3) {
					for ($i = 0; $i < 3 - count($segments); $i++) {
						$time = '00:' . $time;
					}
				}

				if ($maxTimestamp != null) {
					if ($time > $maxTimestamp) {
						$validated->setMessage('The timestamp exceeds the maximum allowed length (' . $maxTimestamp . ').');
						$error = true;
					}
				}
				if ($minTimestamp != null) {
					if ($time < $minTimestamp) {
						$validated->setMessage('The timestamp is less than the minimum allowed length (' . $minTimestamp . ').');
						$error = true;
					}
				}

				// Finally, if the timestamp falls within the min and max lengths, return it.
				if (!$error) {
					$validated = $time;
				}
			}
		} else {
			$validated = null;
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
	 * @param string $errorMessage The error message to output if the validation fails.
	 * @param bool $validIfNotExists If set to true, the function will return a valid result if the input DOES NOT exist in the array. Else, only validates if it does (default).
	 * 	E.g. if searching for the word "plan" in a list of words, if the list contains "plant" or "airplane", the keyword is considered found.
	 * @return string|array|false If the value exists in the list, it returns it. If it does not, false is returned. Note that the value from the list is returned, and not the given $value.
	 */
	protected function validateFromList(string|array|null $value, array $list, string $errorMessage = 'The given value does not exist in the list.', bool $validIfNotExists = false): string|array|Error
	{
		$validated = new Error($errorMessage);

		if (is_array($value)) {
			foreach ($value as $val) {
				if (in_array($val, $list) !== $validIfNotExists) {
					$validated = !$validIfNotExists ? $list[array_search($value, $list)] : $value;
					break;
				}
			}
		} else {
			if (in_array($value, $list) !== $validIfNotExists) {
				$validated = !$validIfNotExists ? $list[array_search($value, $list)] : $value;
			}
		}

		return $validated;
	}
}
