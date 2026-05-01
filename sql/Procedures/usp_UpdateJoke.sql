-- Stored procedure for inserting jokes into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdateJoke;

CREATE PROCEDURE RipDB.usp_UpdateJoke(
	IN InJokeID varchar(512),
	IN InJokeName varchar(512),
	IN InJokeDescription text,
	IN PrimaryTag int,
	IN TagsJSON json,
	IN MetasJSON json,
	IN AlternateNamesJSON json)
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
		IF (PrimaryTag IS NOT NULL) THEN
			INSERT INTO JokeTags (JokeID, TagID, IsPrimary)
			VALUES (InJokeID, PrimaryTag, 1);
		END IF;

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

		-- Alternate joke names.
		IF AlternateNamesJSON IS NOT NULL THEN
		    DELETE FROM AlternateJokeNames
			WHERE JokeID = InJokeID;

            INSERT INTO AlternateJokeNames (JokeID, JokeName)
    		SELECT InJokeID, JSON_UNQUOTE(JokeName)
    		FROM JSON_TABLE (AlternateNamesJSON, '$[*]' COLUMNS(rn FOR ORDINALITY, JokeName JSON PATH '$')) k
            WHERE JokeName IS NOT NULL;
		END IF;
	END IF;

	COMMIT;
END
