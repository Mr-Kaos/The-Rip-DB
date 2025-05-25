DROP PROCEDURE IF EXISTS RipDB.usp_InsertMetaJoke;

CREATE PROCEDURE RipDB.usp_InsertMetaJoke(
	IN NewMetaJokeName varchar(128),
	IN MetaDescription text,
	IN MetaID INT,
	OUT MetaJokeIDOut INT)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	IF (SELECT MetaJokeID FROM MetaJokes WHERE MetaJokeName = NewMetaJokeName) IS NULL THEN
		INSERT INTO MetaJokes
			(MetaJokeName, MetaJokeDescription, MetaID)
		VALUES
			(NewMetaJokeName, MetaDescription, MetaID);

		SET MetaJokeIDOut = LAST_INSERT_ID();
	ELSE
		SELECT MetaJokeID INTO MetaJokeIDOut FROM MetaJokes WHERE MetaJokeName = NewMetaJokeName;
	END IF;
	COMMIT;
END
