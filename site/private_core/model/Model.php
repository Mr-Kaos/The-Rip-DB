<?php

namespace RipDB;

require("private_core/config/db.php");

use PicoDb\Database;

/**
 * Base model class.
 * 
 * @property \PicoDb\Database The main database connection.
 */
abstract class Model
{
	protected \PicoDb\Database $db;

	public function __construct()
	{
		$this->newConn();
	}

	/**
	 * Creates a new database connection.
	 */
	public function newConn(): void
	{
		$this->db = new Database([
			'driver' => 'mysql',
			'hostname' => constant('SQL_HOST'),
			'username' => constant('SQL_USER'),
			'password' => constant('SQL_PASS'),
			'database' => constant('SQL_DB')
		]);
	}

	/**
	 * Sets the keys of a 2D array to be the value of from within the sub-array.
	 * E.g. If every sub-array in the root array has the key 'ID', by specifying 'ID' as the $subArrayKey parameter, the key of that array value
	 * will be changed into the value of the "ID".
	 * @param array &$resultSetArray A 2D array obtained from a resultset to modify its numeric indexes into keys.
	 * @param string $subArrayKey The key from within the sub-arrays to obtain the value from which will overwrite the array's index.
	 * @param bool $unsetKey If true, unsets the original key from the source array. Set to true by default.
	 */
	protected function setSubArrayValueToKey(array &$resultSet, string $subArrayKey, bool $unsetKey = true)
	{
		$result = [];
		foreach ($resultSet as $data) {
			$id = $data[$subArrayKey];
			if ($unsetKey) {
				unset($data[$subArrayKey]);
			}
			$result[$id] = $data;
		}
		return $result;
	}

	/**
	 * Sets the keys of a 2D array to be the value of from within the sub-array.
	 * E.g. If every sub-array in the root array has the key 'ID', by specifying 'ID' as the $subArrayKey parameter, the key of that array value
	 * will be changed into the value of the "ID".
	 * This function will always return a 2D array with each element being any other items in the original array that have the specified $subArrayKey match.
	 * That is if there are multiple entries in the root array where the "ID" value in each sub-array is the same value, they will be grouped together in the
	 * the same array record.
	 * @param array &$resultSetArray A 2D array obtained from a resultset to modify its numeric indexes into keys.
	 * @param string $subArrayKey The key from within the sub-arrays to obtain the value from which will overwrite the array's index.
	 * @param bool $unsetKey If true, unsets the original key from the source array. Set to true by default.
	 */
	protected function setSubArrayValueToKey2D(array &$resultSet, string $subArrayKey, bool $unsetKey = true)
	{
		$result = [];
		foreach ($resultSet as $data) {
			if (array_key_exists($subArrayKey, $data)) {
				$id = $data[$subArrayKey];
				if ($unsetKey) {
					unset($data[$subArrayKey]);
				}
				if (!array_key_exists($id, $result)) {
					$result[$id] = [];
				}
				array_push($result[$id], $data);
			}
		}
		return $result;
	}
}
