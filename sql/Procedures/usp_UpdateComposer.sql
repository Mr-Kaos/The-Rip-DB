-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdateComposer;

CREATE PROCEDURE RipDB.usp_UpdateComposer(
	IN InComposerID int,
	IN FirstName varchar(256),
	IN LastName varchar(256),
	IN FirstNameAlt varchar(256),
	IN LastNameAlt varchar(256))
BEGIN
	UPDATE Composers SET
		ComposerFirstName = FirstName,
		ComposerLastName = LastName,
		ComposerFirstNameAlt = FirstNameAlt,
		ComposerLastNameAlt = LastNameAlt
	WHERE ComposerID = InComposerID;
	
	COMMIT;
END
