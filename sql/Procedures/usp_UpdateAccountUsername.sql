-- Stored procedure for updating an account's username.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdateAccountUsername;

CREATE PROCEDURE RipDB.usp_UpdateAccountUsername(
	IN InAccountID INT,
	IN OldPassword varchar(64),
	IN NewPassword varchar(64),
	IN NewPassword2 varchar(64),
	OUT NewLoginID int)
BEGIN
		DECLARE Salt BINARY(64);
	SET Salt = (SELECT PasswordSalt FROM Accounts WHERE AccountID = InAccountID);

	-- Ensure the account exists
	IF (Salt) IS NOT NULL THEN
		-- Check the old password
		IF (SELECT PasswordHash FROM Accounts WHERE AccountID = InAccountID) = SHA2(CONCAT(OldPassword, Salt), 256) THEN
			-- Change the password
			SET Salt = SHA2(RAND(), 256);
			UPDATE Accounts
			SET PasswordSalt = Salt
				PasswordHash = SHA2(CONCAT(NewPassword, Salt), 256)
			WHERE AccountID = InAccountID;
		ELSE
			SELECT 'The current password is incorrect.';
		END IF;
	ELSE
		SELECT 'This account does not exist.';
	END IF;

	COMMIT;
END
