-- This Stored procedure inserts a rip and all related data.

DROP PROCEDURE IF EXISTS RipDB.usp_DeleteRip;

CREATE PROCEDURE RipDB.usp_DeleteRip(
	IN RipID int)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	-- Delete associations to the rip first

	DELETE FROM RipGenres
	WHERE RipID = RipID;

	DELETE FROM RipJokes
	WHERE RipID = RipID;
 
	DELETE FROM RipRippers
	WHERE RipID = RipID;
	
	DELETE FROM Rips
	WHERE RipID = RipID;

	COMMIT;
END
