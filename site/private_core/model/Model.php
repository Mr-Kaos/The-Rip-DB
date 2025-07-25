<?php

namespace RipDB\Model;

if (!file_exists("private_core/config/db.php")) {
	require_once("private_core/config/db-template.php");
} else {
	require_once("private_core/config/db.php");
}

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
		$this->db->getStatementHandler()->withLogging(true);
	}

	public function __destruct()
	{
		// error_log('<pre>QUERY DEBUG:<br>' . print_r($this->db->getLogMessages(), true) . '</pre>');
	}

	/**
	 * Creates a new database connection.
	 */
	public function newConn(): void
	{
		try {
			$this->db = new Database([
				'driver' => 'mysql',
				'hostname' => constant('SQL_HOST'),
				'username' => constant('SQL_USER'),
				'password' => constant('SQL_PASS'),
				'database' => constant('SQL_DB')
			]);
		} catch (\PDOException $e) {
			error_log($e->getMessage());
			\Flight::redirect('/error/db', 500);
		}
	}

	/**
	 * Sets the keys of a 2D array to be the value of from within the sub-array.
	 * E.g. If every sub-array in the root array has the key 'ID', by specifying 'ID' as the $subArrayKey parameter, the key of that array value
	 * will be changed into the value of the "ID".
	 * @param array &$resultSetArray A 2D array obtained from a resultset to modify its numeric indexes into keys.
	 * @param string $subArrayKey The key from within the sub-arrays to obtain the value from which will overwrite the array's index.
	 * @param bool $unsetKey If true, unsets the original key from the source array. Set to true by default.
	 */
	protected function setSubArrayValueToKey(array $resultSet, string $subArrayKey, bool $unsetKey = true)
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
	protected function setSubArrayValueToKey2D(array $resultSet, string $subArrayKey, bool $unsetKey = true)
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

	protected function resultsetToKeyPair(array $resultset, string $keyColumn, string $valueColumn): array
	{
		$output = [];
		foreach ($resultset as $val) {
			$output[$val[$keyColumn]] = $val[$valueColumn];
		}
		return $output;
	}

	/**
	 * Executes a stored procedure with the given data from the array.
	 * @param array $data The data to send to the stored procedure
	 * @param string $storedProcedure The stored procedure to execute in the database
	 * @param mixed &$output The output value of the parameter, if needed
	 * @return \RipDB\Error[]|true True if the procedure is executed without error. If an error occurs, an array of all errors encountered will be returned.
	 */
	public function submitFormData(array $data, string $storedProcedure, mixed &$output = null): array|true
	{
		// If an output parameter is given, include it
		if ($output !== null) {
			$data['@output'] = '@output';
		}
		// loop through each data item and check that none are of type Error. If they are, get the message and exit.
		$result = true;
		$params = '';
		foreach ($data as $key => $param) {
			if ($param instanceof \RipDB\Error) {
				if (!is_array($result)) {
					$result = array();
				}
				array_push($result, $param);
			} elseif ($key == '@output') {
				$params .= '@output,';
				unset($data[$key]);
			} else {
				$params .= '?,';
			}
		}

		// If no errors, execute procedure.
		if ($result === true) {
			// Remove trailing comma from parameterized query.
			$params = substr($params, 0, strlen($params) - 1);

			try {
				$response = $this->db->execute("CALL $storedProcedure($params)", array_values($data))->fetch();

				// If the procedure SELECTs anything, assume it is an error message.
				if (!is_array($response)) {
					if ($output !== null) {
						$output = $this->db->execute('SELECT @output')->fetch()['@output'];
					}
				} else {
					// Only the first SELECT message will be copied as an error message.
					$result = [new \RipDB\Error($response[array_keys($response)[0]])];
				}
			} catch (\PicoDb\SQLException $error) {
				$result = [new \RipDB\Error("An error occurred when sending the data to the database.")];
				error_log($error);
			}
		}

		return $result;
	}

	/**
	 * Gets a resultset of the specified records from the specified table.
	 * @param string $source The name of the table to retrieve records from. Be sure to omit the "s".
	 * @param null|string|array $ids The IDs to retrieve the values for. If a single Id is needed (e.g. for a single-select searchable dropdown, a string can lso be used.)
	 * @return ?array An array of key-pair values of the records corresponding to the given ids, or null if the given list of IDs is also null.
	 */
	public function getFilterResults(string $source, null|string|array $ids): ?array
	{
		$table = $source . 's';
		$result = [];
		if (is_array($ids)) {
			$result = $this->db->table($table)->in($source . 'ID', $ids)->findAll();
			$result = $this->resultsetToKeyPair($result, $source . 'ID', $source . 'Name');
		} elseif (is_string($ids)) {
			$result = $this->db->table($table)->eq($source . 'ID', $ids)->findAll();
			$result = $this->resultsetToKeyPair($result, $source . 'ID', $source . 'Name');
		} else {
			$result = null;
		}

		return $result;
	}

	/**
	 * Applies sorting to the given query.
	 * @param \PicoDB\Table $qry The table query to sort.
	 * @param string $col The column in the table to sort
	 * @param string $direction The sorting direction. Should be either 'ASC' or 'DESC'. Any other values will be ignored and sorting will not be applied.
	 */
	protected function quickSort(\PicoDb\Table &$qry, string $col, ?string $direction): void
	{
		switch (strtoupper($direction)) {
			case 'ASC':
				$qry->asc($col);
				break;
			case 'DESC':
				$qry->desc($col);
				break;
		}
	}
}

/**
 * This is an interface that is used for models that need to provide results for a search table.
 */
interface ResultsetSearch
{
	/**
	 * Table search function.
	 * The implementation should be responsible for querying the desired table using an offset and row count and returning the results as an array.
	 * Optional parameters can be given in the 4th+ parameters for use in specific column searching.
	 * @param int $count The number of rows to retrieve in the query.
	 * @param int $offset The number of rows to skip.
	 * @param ?array $sort An associative array of columns to sort by, with the value being the sorting direction (ASC or DESC).
	 * @return array The queried results.
	 */
	function search(int $rows, int $offset, ?array $sort): array;

	/**
	 * Table count function
	 * The implementation of this function should return the number of records that exist in the given table.
	 * If a search is made with filters, such as a where clause, the count should also take this into account.
	 */
	function getCount(): int;
}
