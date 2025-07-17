-- Stored procedure for inserting jokes into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdateJoke;

CREATE PROCEDURE RipDB.usp_UpdateJoke(
	IN InJokeID varchar(512),
	IN InJokeName varchar(512),
	IN InJokeDescription text,
	IN PrimaryTag int,
	IN TagsJSON json,
	IN MetasJSON json)
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

	IF (SELECT JokeID FROM Jokes WHERE JokeID = InJokeID) IS NOT NULL THEN
		-- Update Joke Record
		UPDATE Jokes SET
			JokeName = InJokeName,
			JokeDescription = InJokeDescription
		WHERE JokeID = InJokeID;

		-- ASSOCIATED TAGS
		-- Delete all tags so they can be re-inserted again.
		DELETE FROM JokeTags
		WHERE JokeID = InJokeID;

		-- Insert the tags again.
		INSERT INTO JokeTags (JokeID, TagID)
		SELECT InJokeID, TagID
		FROM JSON_TABLE (TagsJSON, '$[*]' COLUMNS(rn FOR ORDINALITY, TagID JSON PATH '$')) k
		WHERE JSON_UNQUOTE(k.TagID) IN (
			SELECT TagID
			FROM Tags
		);

		-- Insert primary tag
		INSERT INTO JokeTags (JokeID, TagID, IsPrimary)
		VALUES (InJokeID, PrimaryTag, 1);

		-- ASSOCIATED META JOKES
		-- Delete all meta jokes so they can be re-inserted again.
		DELETE FROM JokeMetas
		WHERE JokeID = InJokeID;

		-- Insert the tags again.
		INSERT INTO JokeMetas (JokeID, MetaJokeID)
		SELECT InJokeID, MetaJokeID
		FROM JSON_TABLE (MetasJSON, '$[*]' COLUMNS(rn FOR ORDINALITY, MetaJokeID JSON PATH '$')) k
		WHERE JSON_UNQUOTE(k.MetaJokeID) IN (
			SELECT MetaJokeID
			FROM MetaJokes
		);		
	END IF;

	COMMIT;
END
