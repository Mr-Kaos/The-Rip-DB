<?php

/**
 * This is a simple script that should be run in the command line to deploy a fresh database.
 * This is written in PHP to reduce the need for additional dependencies to deploy the app, and so I don't need to write a unix bash and windows batch script that do the same thing...
 * 
 * Please make sure to copy the db-template.php file in 'site/private_core/config/' as 'db.php' and set your database connection details in there.
 */

require_once('deployer.php');

// -------------------
// DEPLOYMENT COMMENCE
// -------------------

$pdo = new PDO('mysql:host=' . constant('SQL_HOST') . ';charset=UTF8mb4', constant('SQL_USER'), constant('SQL_PASS'));

$in = readline("Deploying to " . constant('SQL_HOST') . '. Is this OK? [Y or Enter to continue]');

$in = strtoupper($in);
if ($in == 'Y' || $in == '') {
	if (!$pdo) {
		echo "Database connection failed! Please check the connection details in this file (deploy.php).";
		exit();
	} else {
		$pdo->exec('DROP DATABASE IF EXISTS ' . constant('SQL_DB') . ';');
		$pdo->exec('CREATE DATABASE ' . constant('SQL_DB') . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;');
		$pdo = new PDO('mysql:host=' . constant('SQL_HOST') . ';dbname=' . constant('SQL_DB') . ';charset=UTF8', constant('SQL_USER'), constant('SQL_PASS'));
	}

	// ------
	// TABLES
	// ------
	deployFiles($pdo, 'Tables', TABLES);

	// ------
	// VIEWS
	// ------
	deployFiles($pdo, 'Views', VIEWS);

	// ------
	// PROCEDURES
	// ------
	deployFiles($pdo, 'Procedures', PROCEDURES);

	// ------
	// TRIGGERS
	// ------
	deployFiles($pdo, 'Triggers', TRIGGERS);

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
