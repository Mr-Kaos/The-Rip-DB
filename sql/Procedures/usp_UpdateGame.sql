-- Stored procedure for inserting games into the database easily.

DROP PROCEDURE IF EXISTS usp_UpdateGame;

CREATE PROCEDURE usp_UpdateGame(
	IN InGameID int,
	IN InGameName varchar(256),
	IN InDescription text,
	IN Platforms json,
	IN FakeGame int)
BEGIN
	-- Check to make sure the game exists.
	IF (SELECT GameID FROM Games WHERE GameID = InGameID) IS NOT NULL THEN
		UPDATE Games
		SET GameName = InGameName,
			GameDescription = InDescription,
			IsFake = FakeGame
		WHERE GameID = InGameID;

		IF (Platforms IS NOT NULL) THEN
			DELETE FROM GamePlatforms
			WHERE GameID = InGameID;

			INSERT INTO GamePlatforms (PlatformID, GameID)
			SELECT PlatformID, InGameID
			FROM JSON_TABLE (Platforms, '$[*]' COLUMNS(rn FOR ORDINALITY, PlatformID JSON PATH '$')) k;
		END IF;
	ELSE
		SELECT "This game does not exist!";
	END IF;
	COMMIT;
END
