-- Stored procedure for updating an account's password.

DROP PROCEDURE IF EXISTS usp_DeleteAccount;

CREATE PROCEDURE usp_DeleteAccount(
	IN InAccountID INT,
	IN InPassword varchar(64))
BEGIN
	DECLARE Salt BINARY(64);
	SET Salt = (SELECT PasswordSalt FROM Accounts WHERE AccountID = InAccountID);

	-- Ensure the account exists
	IF (Salt) IS NOT NULL THEN
		-- Check the old password
		IF (SELECT PasswordHash FROM Accounts WHERE AccountID = InAccountID) = SHA2(CONCAT(InPassword, Salt), 256) THEN
			-- Delete the Account
			DELETE FROM Accounts
			WHERE AccountID = InAccountID;
		ELSE
			SELECT 'The current password is incorrect.';
		END IF;
	ELSE
		SELECT 'This account does not exist.';
	END IF;

	COMMIT;
END
