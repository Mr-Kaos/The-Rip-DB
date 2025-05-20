-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertTag;

CREATE PROCEDURE RipDB.usp_InsertTag(
	IN NewTagName nvarchar(128),
	OUT TagIDOut INT)
BEGIN
	IF (SELECT TagID FROM Tags WHERE TagName = NewTagName) IS NULL THEN
		INSERT INTO Tags
			(TagName)
		VALUES
			(NewTagName);

		SET TagIDOut = LAST_INSERT_ID();
	ELSE
		SELECT TagID INTO TagIDOut FROM Tags WHERE TagName = NewTagName;
	END IF;
	COMMIT;
END
