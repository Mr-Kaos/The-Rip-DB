-- Stored procedure for inserting meta into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertMetaJoke_SAMPLE;

CREATE PROCEDURE RipDB.usp_InsertMetaJoke_SAMPLE(
	IN MetaJokeName nvarchar(128),
	IN MetaJokeDescription text,
	IN NewMetaName nvarchar(128))
BEGIN
	DECLARE new_MetaId int;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	IF (SELECT MetaName FROM Metas WHERE MetaName = NewMetaName) IS NULL THEN
		INSERT INTO Metas
			(MetaName)
		VALUES
			(NewMetaName)
		ON DUPLICATE KEY UPDATE MetaName = MetaName, MetaID = LAST_INSERT_ID(MetaID);

		SET new_MetaId = LAST_INSERT_ID();
	ELSE
		SET new_MetaId = (SELECT MetaID FROM Metas WHERE MetaName = NewMetaName);
	END IF;

		INSERT INTO MetaJokes
			(MetaJokeName, MetaJokeDescription, MetaID)
		VALUES
			(MetaJokeName, MetaJokeDescription, new_MetaId);

	COMMIT;
END
