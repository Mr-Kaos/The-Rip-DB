-- Stored procedure for inserting jokes into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertJoke;

CREATE PROCEDURE RipDB.usp_InsertJoke(
	IN NewJokeName varchar(512),
	IN JokeDescription text,
	IN PrimaryTag int,
	IN TagsJSON json,
	IN MetasJSON json,
	OUT JokeIDOut INT)
BEGIN
	DECLARE new_TagId, MetaId int;
	DECLARE Tag varchar(128);
	DECLARE i INT DEFAULT 0;
	
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	IF (SELECT JokeID FROM Jokes WHERE JokeName = NewJokeName) IS NULL THEN
		INSERT INTO Jokes
			(JokeName, JokeDescription)
		VALUES
			(NewJokeName, JokeDescription);

		SET JokeIDOut = LAST_INSERT_ID();

		IF PrimaryTag IS NOT NULL THEN
			INSERT INTO JokeTags
				(JokeID, TagID, IsPrimary)
			VALUES
				(JokeIDOut, PrimaryTag, 1);
		END IF;

		IF TagsJSON IS NOT NULL THEN
			WHILE i < JSON_LENGTH(TagsJSON) DO
				SELECT JSON_UNQUOTE(JSON_EXTRACT(TagsJSON, CONCAT('$[', i ,']'))) INTO Tag;

				INSERT INTO JokeTags
					(JokeID, TagID, IsPrimary)
				VALUES
					(JokeIDOut, Tag, 0);

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
					(JokeIDOut, MetaId);

				SET i = i + 1;
			END WHILE;
		END IF;
	ELSE
		SELECT JokeID INTO JokeIDOut FROM Jokes WHERE JokeName = NewJokeName;
	END IF;

	COMMIT;
END
