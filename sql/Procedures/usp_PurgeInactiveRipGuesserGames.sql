-- Stored procedure to purge any inactive rip guesser games.

DROP PROCEDURE IF EXISTS RipDB.usp_PurgeInactiveRipGuesserGames;

CREATE PROCEDURE RipDB.usp_PurgeInactiveRipGuesserGames()
BEGIN
	-- Delete any game sessions that have not been active for more than 30 minutes.
	DELETE FROM RipGuesserGame rgg
	WHERE TIMEDIFF(NOW(), LastInteraction) > '00:30:00';

	COMMIT;
END
