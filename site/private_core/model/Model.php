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
}
