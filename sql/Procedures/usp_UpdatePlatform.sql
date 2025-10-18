-- Stored procedure for updating platforms in the database easily.

DROP PROCEDURE IF EXISTS usp_UpdatePlatform;

CREATE PROCEDURE usp_UpdatePlatform(
	IN InPlatformID int,
	IN InPlatformName varchar(256))
BEGIN
	-- Check to make sure the game exists.
	IF (SELECT PlatformID FROM Platforms WHERE PlatformID = InPlatformID) IS NOT NULL THEN
		UPDATE Platforms
		SET PlatformName = InPlatformName
		WHERE PlatformID = InPlatformID;
	ELSE
		SELECT "This game does not exist!";
	END IF;
	COMMIT;
END
