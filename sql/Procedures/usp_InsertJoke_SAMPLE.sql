-- Stored procedure for inserting jokes into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertJoke_SAMPLE;

CREATE PROCEDURE RipDB.usp_InsertJoke_SAMPLE(
	IN JokeName varchar(128),
	IN JokeDescription text,
	IN PrimaryTag varchar(128),
	IN TagsJSON json,
	IN MetasJSON json)
BEGIN
	DECLARE new_JokeId, new_TagId, MetaId int;
	DECLARE Tag varchar(128);
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

	INSERT INTO Tags
		(TagName)
	VALUES
		(PrimaryTag)
	ON DUPLICATE KEY UPDATE TagName = TagName, TagID = LAST_INSERT_ID(TagID);

	SET new_TagId = LAST_INSERT_ID();

	INSERT INTO JokeTags
		(JokeID, TagID, IsPrimary)
	VALUES
		(new_JokeId, new_TagId, 1);

	IF TagsJSON IS NOT NULL THEN
		-- Insert Sub Tags into Tags table (if not already existing) and associate them to joke.
		WHILE i < JSON_LENGTH(TagsJSON) DO
			SELECT JSON_UNQUOTE(JSON_EXTRACT(TagsJSON, CONCAT('$[', i ,']'))) INTO Tag;

			SET new_TagId = (SELECT TagID FROM Tags WHERE TagName = Tag);
			IF new_TagId IS NULL THEN
				INSERT INTO Tags
					(TagName)
				VALUES
					(Tag)
				ON DUPLICATE KEY UPDATE TagName = TagName, TagID = LAST_INSERT_ID(TagID);

				SET new_TagId = LAST_INSERT_ID();
			END IF;

			INSERT INTO JokeTags
				(JokeID, TagID, IsPrimary)
			VALUES
				(new_JokeId, new_TagId, 0);

			SET new_TagId = NULL;

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
