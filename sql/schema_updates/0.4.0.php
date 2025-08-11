<?php
/**
 * This script updates the database schema from version 0.3.0 -> 0.4.0
 */

require_once('../deployer.php');

$pdo = new PDO('mysql:host=' . constant('SQL_HOST') . ';dbname=' . constant('SQL_DB') . ';charset=UTF8', constant('SQL_USER'), constant('SQL_PASS'));

$in = readline('Updating database "' . constant('SQL_DB') . '" on "' . constant('SQL_HOST') . '" from v0.3.0 -> v0.4.0 . Is this OK? [Y or Enter to continue. N to cancel]');

$in = strtoupper($in);
if ($in == 'Y' || $in == '') {
	if (!$pdo) {
		echo "Database connection failed! Please check the connection details in this file (deploy.php).";
		exit();
	}

	// ----------
	// NEW TABLES
	// ----------

	$files = [
		'Playlists',
		'AnonymousPlaylists'
	];

	deployFiles($pdo, 'Tables', $files);

	// -------------------
	// TABLE MODIFICATIONS
	// -------------------

	// None made for this version.

	// ---------------
	// DROPPED OBJECTS
	// ---------------

	$pdo->exec('DROP PROCEDURE IF EXISTS usp_SelectRandomRip;');

	// Update all views, procedures and triggers.
	require_once('../update.php');
}
