DROP PROCEDURE IF EXISTS RipDB.usp_InsertMetaJoke;

CREATE PROCEDURE RipDB.usp_InsertMetaJoke(
	IN MetaJokeName nvarchar(128),
	IN MetaDescription text,
	IN MetaID INT)
BEGIN
	DECLARE new_MetaId int;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	INSERT INTO MetaJokes
		(MetaJokeName, MetaJokeDescription, MetaID)
	VALUES
		(MetaName, MetaDescription, MetaID);

	COMMIT;
END
