-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS usp_InsertGame;

CREATE PROCEDURE usp_InsertGame(
	IN NewGame varchar(256),
	IN NewDescription text,
	IN FakeGame int,
	OUT GameIDOut int)
BEGIN
	IF (SELECT GameID FROM Games WHERE GameName = NewGame) IS NULL THEN
		INSERT INTO Games
			(GameName, GameDescription, IsFake)
		VALUES
			(NewGame, NewDescription, FakeGame);

		SET GameIDOut = LAST_INSERT_ID();
	ELSE
		SELECT GameID INTO GameIDOut FROM Games WHERE GameName = NewGame;
	END IF;
	COMMIT;
END
