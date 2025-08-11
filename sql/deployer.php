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
	'Genres',
	'Rips',
	'RipJokes',
	'RipGenres',
	'RipRippers',
	'RipGuesserGame',
	'RipGuesserUpvotes',
	'RipJokeFeedback',
	'Accounts',
	'Playlists',
	'AnonymousPlaylists'
];
const VIEWS = [
	'vw_RipsDetailed',
	'vw_JokesDetailed',
	'vw_MetaJokesDetailed',
	'vw_MetasDetailed',
	'vw_Playlists'
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
	'usp_InsertRipper',
	'usp_UpdateRipper',
	'usp_PurgeInactiveRipGuesserGames',
	'usp_NewRipGuesserGame',
	'usp_PingRipGuesserGame',
	'usp_InsertRipFeedback',
	'usp_InsertLogin',
	'usp_SelectLogin',
	'usp_DeleteAccount',
	'usp_UpdateAccountPassword',
	'usp_UpdateAccountUsername',
	'usp_DeleteUnclaimedPlaylists',
	'usp_InsertPlaylist',
	'usp_UpdatePlaylist',
	'usp_DeletePlaylist',
	'usp_ClaimPlaylists'
];
const TRIGGERS = [
	'b_ins_EnsureSinglePrimaryTag',
	'b_upd_EnsureSinglePrimaryTag'
];
