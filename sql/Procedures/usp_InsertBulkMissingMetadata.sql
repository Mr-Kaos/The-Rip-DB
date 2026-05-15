-- Stored procedure for inserting jokes into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertBulkMissingMetadata;

CREATE PROCEDURE RipDB.usp_InsertBulkMissingMetadata(
	IN JokeNames json,
	IN RipperNames json,
	IN ComposerNames json,
	IN Game text)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	INSERT INTO Jokes (JokeName) 
	SELECT JSON_UNQUOTE(Joke)
	FROM JSON_TABLE (JokeNames, '$[*]' COLUMNS(rn FOR ORDINALITY, Joke JSON PATH '$')) b
	WHERE NOT EXISTS (
		SELECT 1
		FROM Jokes
		WHERE LOWER(JokeName) = LOWER(JSON_UNQUOTE(Joke))
	);

	INSERT INTO Rippers (RipperName) 
	SELECT JSON_UNQUOTE(Ripper)
	FROM JSON_TABLE (RipperNames, '$[*]' COLUMNS(rn FOR ORDINALITY, Ripper JSON PATH '$')) b
	WHERE NOT EXISTS (
		SELECT 1
		FROM Rippers
		WHERE LOWER(RipperName) = LOWER(JSON_UNQUOTE(Ripper))
	);

	INSERT INTO Composers (ComposerFirstName, ComposerLastName) 
	SELECT JSON_UNQUOTE(FirstName), JSON_UNQUOTE(LastName)
	FROM JSON_TABLE (ComposerNames, '$[*]' COLUMNS(FirstName JSON PATH'$.FirstName', LastName JSON PATH '$.LastName')) b
	WHERE NOT EXISTS (
		SELECT 1
		FROM Composers
		WHERE LOWER(UniqueNameCompute) = LOWER(CONCAT(JSON_UNQUOTE(FirstName), JSON_UNQUOTE(IF(LastName IS NULL, '', LastName))))
	);

	IF Game IS NOT NULL AND (SELECT GameID FROM Games WHERE LOWER(GameName) = LOWER(Game)) IS NULL THEN
		INSERT INTO Games
			(GameName)
		VALUES
			(Game);
	END IF;
		
	COMMIT;
END
