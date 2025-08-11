<?php

/**
 * This is a simple script that should be run in the command line to update the database.
 * This is written in PHP to reduce the need for additional dependencies to deploy the app, and so I don't need to write a unix bash and windows batch script that do the same thing...
 * 
 * Please make sure to copy the db-template.php file in 'site/private_core/config/' as 'db.php' and set your database connection details in there.
 */

require_once('deployer.php');

// -------------------
// DEPLOYMENT COMMENCE
// -------------------

$pdo = new PDO('mysql:host=' . constant('SQL_HOST') . ';dbname=' . constant('SQL_DB') . ';charset=UTF8', constant('SQL_USER'), constant('SQL_PASS'));

$in = readline('Updating views and procedures "' . constant('SQL_DB') . '" on "' . constant('SQL_HOST') . '". Is this OK? [y/N]');

$in = strtoupper($in);
if ($in == 'Y') {
	if (!$pdo) {
		echo "Database connection failed! Please check the connection details in this file (deploy.php).";
		exit();
	}

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
} else {
	echo 'Aborting.';
}
