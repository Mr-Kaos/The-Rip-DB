-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertComposer;

CREATE PROCEDURE RipDB.usp_InsertComposer(
	IN FirstName varchar(256),
	IN LastName varchar(256),
	IN FirstNameAlt varchar(256),
	IN LastNameAlt varchar(256),
	OUT ComposerIDOut INT)
BEGIN
	INSERT INTO Composers
		(ComposerFirstName, ComposerLastName, ComposerFirstNameAlt, ComposerLastNameAlt)
	VALUES
		(FirstName, LastName, FirstNameAlt, LastNameAlt);

	SET ComposerIDOut = LAST_INSERT_ID();
	COMMIT;
END
