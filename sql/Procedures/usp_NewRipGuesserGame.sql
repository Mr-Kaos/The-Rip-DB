-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_NewRipGuesserGame;

CREATE PROCEDURE RipDB.usp_NewRipGuesserGame(
	IN GameID char(24),
	IN GameSettings varchar(8192))
BEGIN
	INSERT INTO RipGuesserGame
		(SessionID, Settings)
	VALUES
		(GameID, GameSettings);
	COMMIT;
END
