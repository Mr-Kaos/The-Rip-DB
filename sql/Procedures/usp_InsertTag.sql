-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertTag;

CREATE PROCEDURE RipDB.usp_InsertTag(
	IN TagName nvarchar(128),
	IN IsMeta bit)
BEGIN
	INSERT INTO Tags
		(TagName, MetaOnly)
	VALUES
		(TagName, IsMeta);
	COMMIT;
END
