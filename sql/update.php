<?php

/**
 * This is a simple script that should be run in the command line to deploy the database.
 * This is written in PHP to reduce the need for additional dependencies to deploy the app, and so I don't need to write a unix bash and windows batch script that do the same thing...
 * 
 * Please make sure to copy the db-template.php file in 'site/private_core/config/' as 'db.php' and set your database connection details in there.
 */

if (!file_exists('../site/private_core/config/db.php')) {
	echo "Please set up your database connection!\nSee 'site/private_core/config/db-template.php' for a connection template.\n";
} else {
	require_once('../site/private_core/config/db.php');

	// -------------------
	// DEPLOYMENT COMMENCE
	// -------------------

	$pdo = new PDO('mysql:host=' . constant('SQL_HOST') . ';dbname=' . constant('SQL_DB') . ';charset=UTF8', constant('SQL_USER'), constant('SQL_PASS'));

	$in = readline('Updating views and procedures "' . constant('SQL_DB') . '" on "' . constant('SQL_HOST') . '". Is this OK? [Y or Enter to continue]');

	$in = strtoupper($in);
	if ($in == 'Y' || $in == '') {
		if (!$pdo) {
			echo "Database connection failed! Please check the connection details in this file (deploy.php).";
			exit();
		}

		function deployFiles($pdo, string $folder, array $fileNames)
		{
			foreach ($fileNames as $file) {
				$sql = file_get_contents("$folder/$file.sql");
				echo "Udpating: $folder/$file.sql\n";
				$pdo->exec($sql);
			}
		}

		// ------
		// VIEWS
		// ------

		$files = [
			'vw_RipsDetailed',
			'vw_JokesDetailed',
			'vw_MetaJokesDetailed',
			'vw_MetasDetailed'
		];
		deployFiles($pdo, 'Views', $files);

		// ------
		// PROCEDURES
		// ------

		$files = [
			'usp_InsertMeta',
			'usp_UpdateMeta',
			'usp_InsertJoke',
			'usp_UpdateJoke',
			'usp_InsertMetaJoke',
			'usp_UpdateMetaJoke',
			'usp_InsertRip',
			'usp_UpdateRip',
			'usp_DeleteRip',
			'usp_InsertTag',
			'usp_UpdateTag',
			'usp_InsertChannel',
			'usp_UpdateChannel',
			'usp_InsertGame',
			'usp_UpdateGame',
			'usp_InsertRipper',
			'usp_UpdateRipper',
			'usp_PurgeInactiveRipGuesserGames',
			'usp_NewRipGuesserGame',
			'usp_PingRipGuesserGame',
			'usp_SelectRandomRip',
			'usp_InsertRipFeedback',
			'usp_InsertLogin',
			'usp_SelectLogin',
			'usp_UpdateAccountPassword',
			'usp_UpdateAccountUsername'
		];

		deployFiles($pdo, 'Procedures', $files);

		// ------
		// TRIGGERS
		// ------

		$files = [
			'b_ins_EnsureSinglePrimaryTag',
			'b_upd_EnsureSinglePrimaryTag'
		];

		deployFiles($pdo, 'Triggers', $files);

	} else {
		echo 'Aborting.';
	}
}
