<?php

/**
 * This is a simple script that should be run in the command line to deploy the database.
 * This is written in PHP to reduce the need for additional dependencies to deploy the app, and so I don't need to write a unix bash and windows batch script to do the same thing...
 * 
 * Set up the database connection below to deploy:
 */

// ---------------
// DATABASE CONFIG
// ---------------

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';

// -------------------
// DEPLOYMENT COMMENCE
// -------------------

$pdo = new PDO('mysql:host=' . DB_HOST . ';charset=UTF8', DB_USER, DB_PASS);

const DB_NAME = 'RipDB';


$in = readline("Deploying to " . DB_HOST . '. Is this OK? [Y or Enter to continue]');

$in = strtoupper($in);
if ($in == 'Y' || $in == '') {
	// if ($mysqli->connect_errno) {
	if (!$pdo) {
		echo "Database connection failed! Please check the connection details in this file (deploy.php).";
		exit();
	} else {
		$pdo->exec('DROP DATABASE IF EXISTS ' . DB_NAME . ';');
		$pdo->exec('CREATE DATABASE ' . DB_NAME . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;');
		$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=UTF8', DB_USER, DB_PASS);
	}

	function deployFiles($pdo, string $folder, array $fileNames)
	{
		foreach ($fileNames as $file) {
			$sql = file_get_contents("$folder/$file.sql");
			echo "Deploying: $folder/$file.sql\n";
			$pdo->exec($sql);
			// $mysqli->query($sql);
		}
	}

	// ------
	// TABLES
	// ------

	$files = [
		'Metas',
		'Tags',
		'Channels',
		'Jokes',
		'Games',
		'Rippers',
		'MetaJokes',
		'JokeMetas',
		'JokeTags',
		'Genres',
		'Rips',
		'RipJokes',
		'RipGenres',
		'RipRippers'
	];
	deployFiles($pdo, 'Tables', $files);

	// ------
	// VIEWS
	// ------

	$files = [
		'vw_RipsDetailed',
		'vw_JokesDetailed'
	];
	deployFiles($pdo, 'Views', $files);

	// ------
	// PROCEDURES
	// ------

	$files = [
		'usp_InsertJoke',
		'usp_InsertJoke_SAMPLE',
		'usp_InsertMetaJoke_SAMPLE',
		'usp_InsertMetaJoke',
		'usp_InsertRip',
		'usp_UpdateRip',
		'usp_DeleteRip',
		'usp_InsertTag',
		'usp_InsertChannel',
		'usp_InsertGame'
	];
	deployFiles($pdo, 'Procedures', $files);

	// ------
	// Base Data
	// ------

	deployFiles($pdo, 'Scripts', ['BaseData']);

	// ----------------------
	// Sample Data (optional)
	// ----------------------

	$in = readline("Database deployment successful! Would you like to deploy some sample data? [y/N]");

	$in = strtoupper($in);
	if ($in == 'Y') {
		deployFiles($pdo, 'Scripts', ['Sample_data']);
	}
} else {
	echo 'Aborting.';
}
