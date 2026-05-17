<?php

/**
 * This script provides definitions and functions to aid in the deployment/updating of the database.
 */

$dbConfig = __DIR__ . '/../site/private_core/config/db.php';

if (!file_exists($dbConfig)) {
	echo "Please set up your database connection!\nSee 'site/private_core/config/db-template.php' for a connection template.\n";
	die();
} else {
	require_once($dbConfig);
}

function verifyConnectionDetails(string &$host, string &$port): bool
{
	$ready = false;
	while (!$ready) {
		$in = strtoupper(readline("Connecting to database " . constant("SQL_DB") . " on $host:$port. Is this OK? [Y/N, 0 to abort]"));
		if ($in == 'N') {
			print("Enter destination host: [default: 'localhost']\n");
			$host = 'localhost';
			$in = readline();
			$host = empty($in) ? $host : trim($in);

			print("Enter server port: [default: '$port']\n");
			$in = readline();
			$port = empty($in) ? $port : trim($in);
		} elseif ($in == 'Y') {
			$ready = true;
		} elseif ($in == '0') {
			break;
		}
	}

	return $ready;
}

function createConnection()
{
	$host = constant('SQL_HOST');
	$port = constant('SQL_PORT');
	$pdo = null;

	if (verifyConnectionDetails($host, $port)) {
		$pdo = new PDO("mysql:host=$host;port=$port;dbname=" . constant('SQL_DB') . ';charset=UTF8mb4', constant('SQL_USER'), constant('SQL_PASS'));
		if (!$pdo) {
			echo "Database connection failed! Please check the connection details in this file (deploy.php).";
		}
	}
	return $pdo;
}

function deployFiles($pdo, string $folder, array $fileNames)
{
	foreach ($fileNames as $file) {
		echo __DIR__;
		$sql = file_get_contents(__DIR__ . "/$folder/$file.sql");
		echo "Deploying: $folder/$file.sql\n";
		$pdo->exec($sql);
	}
}

const TABLES = [
	'Metas',
	'Tags',
	'Channels',
	'Jokes',
	'Games',
	'Rippers',
	'MetaJokes',
	'JokeMetas',
	'JokeTags',
	'AlternateJokeNames',
	'Composers',
	'Platforms',
	'GamePlatforms',
	'Genres',
	'Rips',
	'RipJokes',
	'RipRippers',
	'RipComposers',
	'Accounts',
	'Playlists',
	'AnonymousPlaylists'
];
const VIEWS = [
	'vw_RipsDetailed',
	'vw_JokesDetailed',
	'vw_MetaJokesDetailed',
	'vw_MetasDetailed',
	'vw_Playlists',
	'vw_Composers',
	'vw_GamesDetailed'
];
const PROCEDURES = [
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
	'usp_InsertPlatform',
	'usp_UpdatePlatform',
	'usp_InsertRipper',
	'usp_UpdateRipper',
	'usp_InsertComposer',
	'usp_UpdateComposer',
	'usp_InsertLogin',
	'usp_SelectLogin',
	'usp_DeleteAccount',
	'usp_UpdateAccountPassword',
	'usp_UpdateAccountUsername',
	'usp_DeleteUnclaimedPlaylists',
	'usp_InsertPlaylist',
	'usp_UpdatePlaylist',
	'usp_DeletePlaylist',
	'usp_ClaimPlaylists',
	'usp_GenerateUniqueCode',
	'usp_InsertBulkMissingMetadata'
];
const TRIGGERS = [
	'b_ins_EnsureSinglePrimaryTag',
	'b_upd_EnsureSinglePrimaryTag'
];
