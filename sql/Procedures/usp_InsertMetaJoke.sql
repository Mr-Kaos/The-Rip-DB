-- Stored procedure for inserting meta into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertMetaJoke;

CREATE PROCEDURE RipDB.usp_InsertMetaJoke(
	IN MetaName varchar(128),
	IN MetaDescription text,
	IN MetaTag varchar(128))
BEGIN
	DECLARE new_TagId int;
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	INSERT INTO Tags
		(TagName, MetaOnly)
	VALUES
		(MetaTag, 1)
	ON DUPLICATE KEY UPDATE TagName = TagName, TagID = LAST_INSERT_ID(TagID);

	SET new_TagId = LAST_INSERT_ID();

	INSERT INTO MetaJokes
		(MetaJokeName, MetaJokeDescription, MetaTag)
	VALUES
		(MetaName, MetaDescription, new_TagId);

	COMMIT;
END
