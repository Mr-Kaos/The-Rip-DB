-- This Stored procedure inserts a rip and all related data.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertRip;

-- The jokes parameter should be formatted like so:
-- {
-- 	"JokeID": {
-- 		"timestamps": [
-- 			{
-- 				"start": "--:--:--",
-- 				"end": "--:--:--"
-- 			}
-- 		],
-- 		"comment": null
-- 	}
-- }
-- Where "JokeID" is the ID of the joke.
-- The timestamps key stores an array of start and/or end timestamps of when the joke appears in the rip.
-- Either or both the start and end timestamps should be present when adding a timestamp entry.
-- The comment is a description of the joke and how it is used in the rip. It is optional.

-- The Rippers JSON should be formatted like so:
-- {
-- 	"RipperID": "Alias Name for rip"
-- }
-- Where "RipperID is the ID of the ripper."
-- The value associated to the ripper ID is the alias name of the ripper as they appear in the rip (if one is given).

CREATE PROCEDURE RipDB.usp_InsertRip(
	IN RipName varchar(1024),
	IN AlternateName varchar(2048),
	IN Description text,
	IN UploadDate datetime,
	IN RipLength time,
	IN URL varchar(2048),
	IN YTID varchar(12),
	IN AltURL varchar(2048),
	IN Game int,
	IN Channel int,
	IN Genres json,
	IN Jokes json,
	IN Rippers json)
BEGIN
	DECLARE new_RipID int;
	DECLARE Id int;
	DECLARE extractedValA, extractedValB varchar(256);
	DECLARE i int DEFAULT 0;

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;
 
	INSERT INTO Rips
		(RipName, RipDate, RipAlternateName, RipDescription, RipLength, RipGame, RipURL, RipYouTubeID, RipAlternateURL, RipChannel)
	VALUES
		(RipName, UploadDate, AlternateName, Description, RipLength, Game, URL, YTID, AltURL, Channel);

	SET new_RipID = LAST_INSERT_ID();
	
	-- Create ripper associations
	WHILE i < JSON_LENGTH(Rippers) DO
		SELECT JSON_UNQUOTE(JSON_EXTRACT(
			(SELECT JSON_KEYS(Rippers) a), CONCAT('$[', i ,']'))) INTO Id;
		SELECT JSON_UNQUOTE(JSON_EXTRACT(Rippers, CONCAT('$."', id ,'"'))) INTO extractedValA;
		
		INSERT INTO RipRippers
			(RipID, RipperId, Alias)
		VALUES
			(new_RipID, Id, extractedValA);
		
		SET i = i + 1;
	END WHILE;

	SET i = 0;
	-- Create joke associations
	WHILE i < JSON_LENGTH(Jokes) DO
		SELECT JSON_UNQUOTE(JSON_EXTRACT(
				(SELECT JSON_KEYS(Jokes) a), CONCAT('$[', i ,']'))) INTO Id;
		SELECT JSON_UNQUOTE(JSON_EXTRACT(Jokes, CONCAT('$."', id ,'".timestamps'))) INTO extractedValA;
		SELECT JSON_UNQUOTE(JSON_EXTRACT(Jokes, CONCAT('$."', id ,'".comment'))) INTO extractedValB;
		SET extractedValB = (SELECT NULLIF(extractedValB, 'null')); 
	
		INSERT INTO RipJokes
			(RipID, JokeId, JokeTimestamps, JokeComment)
		VALUES
			(new_RipID, Id, extractedValA, extractedValB);
		
		SET i = i + 1;
	END WHILE;

	SET i = 0;
	-- Create Rip Genre associations
	WHILE i < JSON_LENGTH(Genres) DO
		SELECT JSON_UNQUOTE(JSON_EXTRACT(Genres, CONCAT('$[', i ,']'))) INTO Id;
		
		INSERT INTO RipGenres
			(RipID, GenreID)
		VALUES
			(new_RipID, Id);
		
		SET i = i + 1;
	END WHILE;

	COMMIT;
END
