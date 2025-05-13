-- Stored procedure for inserting jokes into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertJoke;

CREATE PROCEDURE RipDB.usp_InsertJoke(
	IN JokeName nvarchar(128),
	IN JokeDescription text,
	IN PrimaryTag int,
	IN TagsJSON json,
	IN MetasJSON json)
BEGIN
	DECLARE new_JokeId, new_TagId, MetaId int;
	DECLARE Tag nvarchar(128);
	DECLARE i INT DEFAULT 0;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	INSERT INTO Jokes
		(JokeName, JokeDescription)
	VALUES
		(JokeName, JokeDescription);

	SET new_JokeId = LAST_INSERT_ID();

	INSERT INTO JokeTags
		(JokeID, TagID, IsPrimary)
	VALUES
		(new_JokeId, PrimaryTag, 1);

	IF TagsJSON IS NOT NULL THEN
		WHILE i < JSON_LENGTH(TagsJSON) DO
			SELECT JSON_UNQUOTE(JSON_EXTRACT(TagsJSON, CONCAT('$[', i ,']'))) INTO Tag;

			INSERT INTO JokeTags
				(JokeID, TagID, IsPrimary)
			VALUES
				(new_JokeId, Tag, 0);

			SET i = i + 1;
		END WHILE;
	END IF;

	IF MetasJSON IS NOT NULL THEN
		SET i = 0;
		-- Associate MetasJSON to the joke.
		WHILE i < JSON_LENGTH(MetasJSON) DO
			SELECT JSON_UNQUOTE(JSON_EXTRACT(MetasJSON, CONCAT('$[', i ,']'))) INTO MetaId;
		
			INSERT INTO JokeMetas
				(JokeID, MetaJokeID)
			VALUES
				(new_JokeId, MetaId);

			SET i = i + 1;
		END WHILE;
	END IF;

	COMMIT;
END
