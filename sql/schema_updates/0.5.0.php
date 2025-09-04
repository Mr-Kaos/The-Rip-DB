<?php

/**
 * This script updates the database schema from version 0.3.0 -> 0.4.0
 */

require_once(__DIR__ . '/../deployer.php');

$pdo = new PDO('mysql:host=' . constant('SQL_HOST') . ';dbname=' . constant('SQL_DB') . ';charset=UTF8', constant('SQL_USER'), constant('SQL_PASS'));

$in = readline('Updating database "' . constant('SQL_DB') . '" on "' . constant('SQL_HOST') . '" from v0.4.X -> v0.5.0 . Is this OK? [Y or Enter to continue. N to cancel]');

$in = strtoupper($in);
if ($in == 'Y' || $in == '') {
	if (!$pdo) {
		echo "Database connection failed! Please check the connection details in this file (deploy.php).";
		exit();
	}

	// ----------
	// NEW TABLES
	// ----------

	// None made for this version.

	// -------------------
	// TABLE MODIFICATIONS
	// -------------------

	// None made for this version.

	// --------------------------
	// DROPPED OBJECTS AND ALTERS
	// --------------------------

	$pdo->exec('ALTER TABLE RipDB.Rips ADD WikiURL varchar(8192) DEFAULT NULL NULL;');
	$pdo->exec('ALTER TABLE RipDB.Rips ADD MixName varchar(256) DEFAULT NULL NULL;');

	// Update all rips that contain a mix name to move them into the designated column. THis may not be 100% accurate, but should grab the majority of mix names.
	$pdo->exec("UPDATE Rips SET
MixName = REGEXP_SUBSTR(RipName, '\\\\([^)]*\\\\)[^(]*$'),
RipName = IFNULL(TRIM(REPLACE(RipName, REGEXP_SUBSTR(RipName, '\\\\([^)]*\\\\)[^(]*$'), '')), RipName)
WHERE RipName LIKE '%mix%)'
OR RipName LIKE '%version%)'
OR RipName LIKE '%edition%)'
OR RipName LIKE '%release%)'
OR RipName LIKE '%ver%)';

UPDATE Rips
SET MixName = SUBSTRING(MixName, 2, LENGTH(MixName) - 2)
WHERE MixName IS NOT NULL");

	// Update all views, procedures and triggers.
	require_once(__DIR__ . '/../update.php');
}
