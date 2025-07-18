DROP PROCEDURE IF EXISTS RipDB.usp_UpdateMetaJoke;

CREATE PROCEDURE RipDB.usp_UpdateMetaJoke(
	IN InMetaJokeID varchar(128),
	IN InName varchar(128),
	IN InDescription text,
	IN InMetaID INT)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	IF (SELECT MetaJokeID FROM MetaJokes WHERE MetaJokeID = InMetaJokeID) IS NOT NULL THEN
		UPDATE MetaJokes SET
			MetaJokeName = InName,
			MetaJokeDescription = InDescription,
			MetaID = InMetaID
		WHERE MetaJokeID = InMetaJokeID;
	END IF;
	COMMIT;
END
