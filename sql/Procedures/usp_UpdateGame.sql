-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS usp_UpdateGame;

CREATE PROCEDURE usp_UpdateGame(
	IN InGameID int,
	IN InGameName varchar(256),
	IN InDescription text,
	IN FakeGame int)
BEGIN
	-- Check to make sure the game exists.
	IF (SELECT GameID FROM Games WHERE GameID = InGameID) IS NOT NULL THEN
		UPDATE Games
		SET GameName = InGameName,
			GameDescription = InDescription,
			IsFake = FakeGame
		WHERE GameID = InGameID;
	ELSE
		SELECT "This game does not exist!";
	END IF;
	COMMIT;
END
