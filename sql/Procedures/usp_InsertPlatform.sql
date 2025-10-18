-- Stored procedure for inserting game platforms into the database easily.

DROP PROCEDURE IF EXISTS usp_InsertPlatform;

CREATE PROCEDURE usp_InsertPlatform(
	IN InPlatformName varchar(256),
	OUT PlatformIDOut int)
BEGIN
	IF (SELECT PlatformID FROM Platforms WHERE PlatformName = InPlatformName) IS NULL THEN
		INSERT INTO Platforms
			(PlatformName)
		VALUES
			(InPlatformName);

		SET PlatformIDOut = LAST_INSERT_ID();
	ELSE
		SELECT PlatformID INTO PlatformIDOut FROM Platforms WHERE PlatformName = InPlatformName;
	END IF;
	COMMIT;
END
