-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertTag;

CREATE PROCEDURE RipDB.usp_InsertTag(
	IN InTagName varchar(128),
	OUT TagIDOut INT)
BEGIN
	IF (SELECT TagID FROM Tags WHERE TagName = InTagName) IS NULL THEN
		INSERT INTO Tags
			(TagName)
		VALUES
			(InTagName);

		SET TagIDOut = LAST_INSERT_ID();
	ELSE
		SELECT TagID INTO TagIDOut FROM Tags WHERE TagName = InTagName;
	END IF;
	COMMIT;
END
