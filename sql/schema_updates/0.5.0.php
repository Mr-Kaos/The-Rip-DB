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

	$files = [
		'Composers',
		'RipComposers',
		'Platforms',
		'GamePlatforms'
	];

	deployFiles($pdo, 'Tables', $files);

	// -------------------
	// TABLE MODIFICATIONS
	// -------------------

	// Add new columns
	$pdo->exec('ALTER TABLE RipDB.Rips ADD WikiURL varchar(8192) DEFAULT NULL;');
	$pdo->exec('ALTER TABLE RipDB.Rips ADD MixName varchar(256) DEFAULT NULL;');
	$pdo->exec('ALTER TABLE RipDB.RipJokes ADD GenreID int DEFAULT NULL;');
	$pdo->exec('ALTER TABLE RipDB.Channels ADD WikiURL varchar(1024) DEFAULT NULL;');
	$pdo->exec('ALTER TABLE RipDB.Channels ADD IsActive bit DEFAULT 1;');

	// Transferring genres to new genres relation
	$pdo->exec("UPDATE RipJokes rj
JOIN (
	SELECT r.RipID, g.GenreID, JokeID
	FROM (
		SELECT r.RipID, COUNT(JokeID) AS Jokes
		FROM Rips r
		JOIN RipJokes j ON j.RipID = r.RipID
		GROUP BY r.RipID 
	) r
	JOIN RipGenres g ON g.RipID = r.RipID
	JOIN RipJokes j ON j.RipID = r.RipID
	WHERE Jokes = 1
) a
SET rj.GenreID = a.GenreID 
WHERE rj.RipID = a.RipID AND rj.JokeID = a.JokeID;");


	// Delete the transferred genres
	$pdo->exec("DELETE RipGenres
FROM RipDB.RipGenres
JOIN (SELECT r.RipID as rip, g.GenreID as genre, JokeID
	FROM (
		SELECT r.RipID, COUNT(JokeID) AS Jokes
		FROM Rips r
		JOIN RipJokes j ON j.RipID = r.RipID
		GROUP BY r.RipID 
	) r
	JOIN RipGenres g ON g.RipID = r.RipID
	JOIN RipJokes j ON j.RipID = r.RipID
	WHERE Jokes = 1
) a ON GenreID = a.Genre AND RipID = a.rip;");

	// Move the rest of the genres to the first joke in the associated rip to keep their data
	$data = $pdo->query("SELECT r.RipID, g.GenreID, JokeID, ROW_NUMBER() OVER (PARTITION BY RipID, GenreID ORDER BY RipID) AS row_num -- , COUNT(JokeID)
FROM (
	SELECT r.RipID, COUNT(JokeID) AS Jokes
	FROM Rips r
	JOIN RipJokes j ON j.RipID = r.RipID
	GROUP BY r.RipID
) r
JOIN RipGenres g ON g.RipID = r.RipID
JOIN RipJokes j ON j.RipID = r.RipID
WHERE Jokes > 1
GROUP BY RipID, GenreID, JokeId;", PDO::FETCH_ASSOC);

	$lastId = null;
	$lastGenreId = null;
	$updateData = null;
	$rowNum = 1;
	$parsedGenre = true;
	foreach ($data as $row) {
		// If parsing a new rip
		if ($row["RipID"] != $lastId) {
			$lastId = $row["RipID"];
			$rowNum = 0;
		}
		
		// Parsing a new genre from the same rip
		if ($row["GenreID"] !== $lastGenreId) {
			$rowNum++;
			$parsedGenre = false;
			$lastGenreId = $row["GenreID"];
		}

		// If the genre's joke has not been parsed, update the RipJoke record
		if (!$parsedGenre && $row['row_num'] == $rowNum) {
			$pdo->exec("UPDATE RipJokes SET GenreID = " . $row["GenreID"] . " WHERE RipID = " . $row["RipID"] . " AND JokeID = " . $row['JokeID']);
			$parsedGenre = true;
		}
	}

	// Adding table constraints
	$pdo->exec('ALTER TABLE RipDB.RipJokes ADD CONSTRAINT RipJokes_Genres_FK FOREIGN KEY (GenreID) REFERENCES RipDB.Genres(GenreID);');

	// DROP unused tables
	$pdo->exec('DROP TABLE RipDB.RipGenres;');

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
