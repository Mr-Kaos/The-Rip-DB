<?php
namespace RipDB;

// This is a template config file for the database connection.
// Please duplicate this file with the name "db.php" and change the connection values as needed.
// 
// If this file is not duplicated, the default connection settings below will attempt to be used.

/**
 * SQL database settings
 */
define('SQL_HOST', 'localhost');
define('SQL_USER', 'root');
define('SQL_PASS', '');
define("SQL_DB", 'RipDB');

error_log('NOTICE: Using template database connection config file.');