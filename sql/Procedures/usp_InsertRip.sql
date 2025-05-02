-- Stored procedure for inserting Rips and their related data into the database easily.
-- This procedure is primarily for testing as it does not provide the means to insert detailed data about a rip's jokes or ripper aliases.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertRip;

CREATE PROCEDURE RipDB.usp_InsertRip(
	IN RipName varchar(1024),
	IN AlternateName varchar(2048),
	IN UploadDate datetime,
	IN RipLength time,
	IN Game int,
	IN RipURL varchar(2048),
	IN Channel int,
	IN RipTypes json,
	IN Rippers json,
	IN Jokes json)
BEGIN
	DECLARE new_RipID int;
	DECLARE Id INT;
	DECLARE i INT DEFAULT 0;

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;
 
	INSERT INTO Rips
		(RipName, RipDate, RipAlternateName, RipLength, RipGame, RipURL, RipChannel)
	VALUES
		(RipName, UploadDate, AlternateName, RipLength, Game, RipURL, Channel);

	SET new_RipID = LAST_INSERT_ID();
	
	-- Create ripper associations
	WHILE i < JSON_LENGTH(Rippers) DO
		SELECT JSON_UNQUOTE(JSON_EXTRACT(Rippers, CONCAT('$[', i ,']'))) INTO Id;
		
		INSERT INTO RipRippers
			(RipID, RipperId)
		VALUES
			(new_RipID, Id);
		
		SET i = i + 1;
	END WHILE;

	SET i = 0;
	-- Create joke associations
	WHILE i < JSON_LENGTH(Jokes) DO
		SELECT JSON_UNQUOTE(JSON_EXTRACT(Jokes, CONCAT('$[', i ,']'))) INTO Id;
		
		INSERT INTO RipJokes
			(RipID, JokeId)
		VALUES
			(new_RipID, Id);
		
		SET i = i + 1;
	END WHILE;

	SET i = 0;
	-- Create Rip Type assocaitions
	WHILE i < JSON_LENGTH(RipTypes) DO
		SELECT JSON_UNQUOTE(JSON_EXTRACT(RipTypes, CONCAT('$[', i ,']'))) INTO Id;
		
		INSERT INTO RipsTypes
			(RipID, RipTypeId)
		VALUES
			(new_RipID, Id);
		
		SET i = i + 1;
	END WHILE;

	COMMIT;

	COMMIT;
END
