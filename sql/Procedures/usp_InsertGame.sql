-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertGame;

CREATE PROCEDURE RipDB.usp_InsertGame(
	IN NewGame nvarchar(128),
	IN GameDescription text)
BEGIN
	IF (SELECT GameID FROM Games WHERE GameName = NewGame) IS NULL THEN
		INSERT INTO Games
			(GameName, GameDescription)
		VALUES
			(NewGame, GameDescription);

		SELECT LAST_INSERT_ID();
	ELSE
		SELECT GameID FROM Games WHERE GameName = NewGame;
	END IF;
	COMMIT;
END
