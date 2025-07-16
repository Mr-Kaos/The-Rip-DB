-- Stored procedure for inserting rippers into the database easily.

DROP PROCEDURE IF EXISTS usp_UpdateRipper;

CREATE PROCEDURE usp_UpdateRipper(
	IN InRipperID int,
	IN InRipperName varchar(256)
	)
BEGIN
	-- Check to make sure the ripper exists.
	IF (SELECT RipperID FROM Rippers WHERE RipperID = InRipperID) IS NOT NULL THEN
		UPDATE Rippers
		SET RipperName = InRipperName
		WHERE RipperID = InRipperID;
	ELSE
		SELECT "This ripper does not exist!";
	END IF;
	COMMIT;
END
