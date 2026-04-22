/*
Creates a unique code of the specified length from alphanumeric characters.
The minimum length of a code is 4 characters.
The maximum Length is 10 characters.
If AlphaCharsOnly is set to 1, only alphabetical characters will be used, no numbers.
If AlphaCharsOnly is set to 0, alphanumeric characters will be used, except for "I" and "O".
*/
DROP PROCEDURE IF EXISTS usp_GenerateUniqueCode;

CREATE PROCEDURE usp_GenerateUniqueCode(
	IN CodeLength INT,
	IN AlphaCharsOnly INT,
	OUT UniqueCode VARCHAR(10))
BEGIN
	DECLARE CodeChars CHAR(36);
	DECLARE i INT;

	IF AlphaCharsOnly = 1 THEN
		SET CodeChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	ELSE
		SET CodeChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
	END IF;
	SET i = 0;

	IF CodeLength < 4 THEN
		SET CodeLength = 4;
	ELSEIF CodeLength > 10 THEN
		SET CodeLength = 10;
	END IF;

	SET UniqueCode = CONCAT(substring(CodeChars, rand(@seed:=round(rand()*4294967296))*LENGTH(CodeChars)+1, 1));

	WHILE i < CodeLength - 2 DO
		SET UniqueCode = CONCAT(UniqueCode, substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*LENGTH(CodeChars)+1, 1));
		SET i = i + 1;
	END WHILE;
	
	SET UniqueCode = CONCAT(UniqueCode, substring(CodeChars, rand(@seed)*LENGTH(CodeChars)+1, 1));
END
