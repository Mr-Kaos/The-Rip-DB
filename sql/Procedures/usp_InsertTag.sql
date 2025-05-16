-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertTag;

CREATE PROCEDURE RipDB.usp_InsertTag(
	IN TagName nvarchar(128))
BEGIN
	INSERT INTO Tags
		(TagName)
	VALUES
		(TagName);
	COMMIT;
END
