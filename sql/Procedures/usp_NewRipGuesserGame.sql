-- Stored procedure for creating a new rip guesser game session.

DROP PROCEDURE IF EXISTS RipDB.usp_NewRipGuesserGame;

CREATE PROCEDURE RipDB.usp_NewRipGuesserGame(
	IN GameID char(24),
	IN GameSettings varchar(8192))
BEGIN
	CALL usp_PurgeInactiveRipGuesserGames();

	-- Insert the new game.
	INSERT INTO RipGuesserGame
		(SessionID, Settings)
	VALUES
		(GameID, GameSettings);
	COMMIT;
END
