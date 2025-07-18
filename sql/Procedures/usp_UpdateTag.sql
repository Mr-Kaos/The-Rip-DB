-- Stored procedure for updating tags.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdateTag;

CREATE PROCEDURE RipDB.usp_UpdateTag(
	IN InTagID INT,
	IN InTagName varchar(128))
BEGIN
	IF (SELECT TagID FROM Tags WHERE TagID = InTagID) IS NOT NULL THEN
		UPDATE Tags
		SET TagName = InTagName
		WHERE TagID = InTagID;
	END IF;

	COMMIT;
END
