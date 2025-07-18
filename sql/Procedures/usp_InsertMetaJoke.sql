DROP PROCEDURE IF EXISTS RipDB.usp_InsertMetaJoke;

CREATE PROCEDURE RipDB.usp_InsertMetaJoke(
	IN InName varchar(128),
	IN InDescription text,
	IN InMetaID INT,
	OUT MetaJokeIDOut INT)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	IF (SELECT MetaJokeID FROM MetaJokes WHERE MetaJokeName = InName) IS NULL THEN
		INSERT INTO MetaJokes
			(MetaJokeName, MetaJokeDescription, MetaID)
		VALUES
			(InName, InDescription, InMetaID);

		SET MetaJokeIDOut = LAST_INSERT_ID();
	ELSE
		SELECT MetaJokeID INTO MetaJokeIDOut FROM MetaJokes WHERE MetaJokeName = InName;
	END IF;
	COMMIT;
END
