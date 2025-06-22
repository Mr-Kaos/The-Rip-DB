-- Stored procedure to keep an existing rip guesser game alive.

DROP PROCEDURE IF EXISTS RipDB.usp_PingRipGuesserGame;

CREATE PROCEDURE RipDB.usp_PingRipGuesserGame(
	IN GameID char(24),
	OUT Success BIT)
BEGIN
	CALL usp_PurgeInactiveRipGuesserGames();

	-- Check that the gam actually exists.
	IF (SELECT SessionID FROM RipGuesserGame WHERE SessionID = GameID IS NOT NULL) THEN
		-- Update The LastInteraction timestamp to the current time.
		UPDATE RipGuesserGame
		SET LastInteraction = NOW()
		WHERE SessionID = GameID;
		SET Success = 1;
	ELSE
		SET Success = 0;
	END IF;

	COMMIT;
END
