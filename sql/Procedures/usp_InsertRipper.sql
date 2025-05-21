-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertRipper;

CREATE PROCEDURE RipDB.usp_InsertRipper(
	IN NewRipperName nvarchar(256),
	OUT RipperIDOut INT)
BEGIN
	IF (SELECT RipperID FROM Rippers WHERE RipperName = NewRipperName) IS NULL THEN
		INSERT INTO Rippers
			(RipperName)
		VALUES
			(NewRipperName);

		SET RipperIDOut = LAST_INSERT_ID();
	ELSE
		SELECT RipperID INTO RipperIDOut FROM Rippers WHERE RipperName = NewRipperName;
	END IF;
	COMMIT;
END
