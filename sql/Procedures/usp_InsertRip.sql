DROP PROCEDURE IF EXISTS RipDB.usp_InsertRip;

DELIMITER $$
$$
CREATE PROCEDURE RipDB.usp_InsertRip(
	IN RipName varchar(1024),
	IN UploadDate datetime,
	IN AlternateName varchar(2048),
	IN Game int,
	IN RipURL varchar(2048),
	IN Channel int,
	IN Rippers json,
	IN Jokes json)
BEGIN
	DECLARE RipID int;
 	DECLARE Id INT;
 	DECLARE i INT DEFAULT 0;
 
	INSERT INTO Rips
		(RipName, RipDate, RipAlternateName, RipGame, RipURL, RipChannel)
	VALUES
		(RipName, UploadDate, AlternateName, Game, RipURL, Channel);

	SET RipID = LAST_INSERT_ID();
	
	WHILE i < JSON_LENGTH(Rippers) DO
	    SELECT JSON_EXTRACT(Rippers, CONCAT('$[', i ,']')) INTO Id;
	    
	   	INSERT INTO RipRippers
	   		(RipID, RipperId)
	   	VALUES
	   		(RipID, Id);
	    
	    SET i = i + 1;
	END WHILE;

	SET i = 0;

	WHILE i < JSON_LENGTH(Jokes) DO
	    SELECT JSON_EXTRACT(Jokes, CONCAT('$[', i ,']')) INTO Id;
	    
	   	INSERT INTO RipJokes
	   		(RipID, JokeId)
	   	VALUES
	   		(RipID, Id);
	    
	    SET i = i + 1;
	END WHILE;	
END
$$
DELIMITER ;
