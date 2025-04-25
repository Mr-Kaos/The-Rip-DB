DROP PROCEDURE IF EXISTS RipDB.usp_InsertJoke;

DELIMITER $$
$$
CREATE PROCEDURE RipDB.usp_InsertJoke(
	IN JokeName varchar(128),
	IN JokeDescription datetime,
	IN JokeTags json)
BEGIN
	DECLARE JokeId, TagId int;
 	DECLARE Tag varchar(128);
 	DECLARE i INT DEFAULT 0;
 
	INSERT INTO Jokes
		(JokeName, JokeDescription)
	VALUES
		(JokeName, JokeDescription);

	SET JokeId = LAST_INSERT_ID();
	
	WHILE i < JSON_LENGTH(JokeTags) DO
	    SELECT JSON_EXTRACT(JokeTags, CONCAT('$[', i ,']')) INTO Tag;
	    
	   	INSERT INTO Tags
	   		(TagName)
	   	VALUES
	   		(Tag)
		ON DUPLICATE KEY UPDATE Tag = Tag;

		SET TagId = LAST_INSERT_ID();

		INSERT INTO JokeTags
			(JokeID, TagID)
		VALUES
			(JokeId, TagId);

	    SET i = i + 1;
	END WHILE;
END
$$
DELIMITER ;
