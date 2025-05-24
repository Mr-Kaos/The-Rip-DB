-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertGame;

CREATE PROCEDURE RipDB.usp_InsertGame(
	IN NewGame varchar(128),
	IN GameDescription text,
	OUT GameIDOut int)
BEGIN
	IF (SELECT GameID FROM Games WHERE GameName = NewGame) IS NULL THEN
		INSERT INTO Games
			(GameName, GameDescription)
		VALUES
			(NewGame, GameDescription);

		SET GameIDOut = LAST_INSERT_ID();
	ELSE
		SELECT GameID INTO GameIDOut FROM Games WHERE GameName = NewGame;
	END IF;
	COMMIT;
END
