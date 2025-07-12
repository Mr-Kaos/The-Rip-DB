-- Stored procedure for updating an account's username.

DROP PROCEDURE IF EXISTS usp_UpdateAccountUsername;

CREATE PROCEDURE usp_UpdateAccountUsername(
	IN InAccountID INT,
	IN NewUsername varchar(32),
	IN InPassword varchar(64)
	)
BEGIN
	DECLARE Salt BINARY(64);

	-- Ensure the new username is not already taken
	IF (SELECT Username FROM Accounts WHERE Username = NewUsername AND AccountID <> InAccountID) IS NULL THEN
		SET Salt = (SELECT PasswordSalt FROM Accounts WHERE AccountID = InAccountID);
		-- Ensure the account exists
		IF (Salt) IS NOT NULL THEN
			-- Check the old password
			IF (SELECT PasswordHash FROM Accounts WHERE AccountID = InAccountID) = SHA2(CONCAT(InPassword, Salt), 256) THEN
				-- Change the username
				SET Salt = SHA2(RAND(), 256);
				UPDATE Accounts
				SET Username = NewUsername
				WHERE AccountID = InAccountID;
			ELSE
				SELECT 'The current password is incorrect.';
			END IF;
		ELSE
			SELECT 'This account does not exist.';
		END IF;
	ELSE
		SELECT 'This username is already taken.';
	END IF;

	COMMIT;
END
