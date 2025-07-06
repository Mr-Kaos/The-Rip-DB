-- Stored procedure for selecting a login based on the username and password.
-- If the given username exists, it will attempt to check the hash of the password with the account's salt.

DROP PROCEDURE IF EXISTS RipDB.usp_SelectLogin;

CREATE PROCEDURE RipDB.usp_SelectLogin(
	IN InUsername varchar(32),
	IN InPassword varchar(64),
	OUT LoginID int)
BEGIN
	DECLARE Salt BINARY(64);
	SET Salt = (SELECT PasswordSalt FROM Accounts WHERE Username = InUsername);

	-- Ensure the username exists
	IF (Salt) IS NOT NULL THEN
		-- Check the password	
		IF (SELECT PasswordHash FROM Accounts WHERE Username = InUsername) = SHA2(CONCAT(InPassword, Salt), 256) THEN
			SELECT AccountID INTO LoginID FROM Accounts WHERE Username = InUsername;
		ELSE
			SELECT 'Incorrect username or password.';
		END IF;
	ELSE
		SELECT 'Incorrect username or password.';
	END IF;

	COMMIT;
END
