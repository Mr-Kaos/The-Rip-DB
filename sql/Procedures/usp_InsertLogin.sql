-- Stored procedure for inserting a new suer account into the database.
-- This procedure does not validate the password. This must be done in the application level.

DROP PROCEDURE IF EXISTS usp_InsertLogin;

CREATE PROCEDURE usp_InsertLogin(
	IN NewUsername varchar(32),
	IN NewPassword varchar(64),
	OUT NewLoginID int)
BEGIN
	DECLARE Salt BINARY(64);

	-- Ensure the username is not already taken
	IF (SELECT Username FROM Accounts WHERE Username = NewUsername) IS NULL THEN
		SET Salt = SHA2(RAND(), 256);

		INSERT INTO Accounts
			(Username, PasswordSalt, PasswordHash)
		VALUES
			(NewUsername, Salt, SHA2(CONCAT(NewPassword, Salt), 256));

		SELECT AccountID INTO NewLoginID FROM Accounts WHERE Username = NewUsername;
	ELSE
		SELECT 'This username is already taken!';
	END IF;

	COMMIT;
END
