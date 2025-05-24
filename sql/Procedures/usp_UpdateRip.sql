-- This Stored procedure inserts a rip and all related data.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdateRip;

CREATE PROCEDURE RipDB.usp_UpdateRip(
	IN RipIDTarget int,
	IN Name varchar(1024),
	IN AlternateName varchar(2048),
	IN Description text,
	IN UploadDate datetime,
	IN Length time,
	IN URL varchar(2048),
	IN Game int,
	IN Channel int,
	IN Genres json,
	IN Jokes json,
	IN Rippers json)
BEGIN
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	START TRANSACTION;

	-- Check to ensure the speified rip actually exists.
	IF (SELECT RipID FROM Rips WHERE RipID = RipIDTarget) IS NOT NULL THEN
 
		-- Update rip record
		UPDATE Rips SET
			RipName = Name,
			RipDate = UploadDate,
			RipAlternateName = AlternateName,
			RipDescription = Description,
			RipLength = Length,
			RipGame = Game,
			RipURL = URL,
			RipChannel = Channel
		WHERE RipID = RipIDTarget;
	
		-- RIPPER DATA
		-- Add new Rippers
		INSERT INTO RipRippers (RipId, RipperID, Alias)
		SELECT RipIDTarget, JSON_UNQUOTE(k.RipperIDJSON), JSON_UNQUOTE(Alias)
		FROM JSON_TABLE (json_keys(Rippers), '$[*]' COLUMNS(rn FOR ORDINALITY, RipperIDJSON JSON PATH '$')) k
		JOIN JSON_TABLE(Rippers, '$.*' COLUMNS (rn FOR ORDINALITY, Alias JSON PATH '$')) v ON v.rn = k.rn
		WHERE JSON_UNQUOTE(k.RipperIDJSON) NOT IN (
			SELECT RipperID
			FROM RipRippers
			WHERE RipID = RipIDTarget
		);
	
		-- Delete any removed rippers
		DELETE FROM RipRippers
		WHERE RipperID NOT IN (
			SELECT JSON_UNQUOTE(t.RipperIDJSON)
			FROM JSON_TABLE (json_keys(Rippers), '$[*]' COLUMNS(RipperIDJSON JSON PATH '$')) t
		)
		AND RipID = RipIDTarget;
	
		-- JOKE DATA
		-- Add new Jokes
		INSERT INTO RipJokes (RipId, JokeID, JokeTimestamps, JokeComment)
		SELECT RipIDTarget, JSON_UNQUOTE(k.JokeIDJSON), JSON_UNQUOTE(Timestamps), JSON_UNQUOTE(Comment)
		FROM JSON_TABLE (json_keys(Jokes), '$[*]' COLUMNS(rn FOR ORDINALITY, JokeIDJSON JSON PATH '$')) k
		JOIN JSON_TABLE(Jokes, '$.*' COLUMNS (rn FOR ORDINALITY, Timestamps JSON PATH '$', Comment JSON PATH '$.comment')) v ON v.rn = k.rn
		WHERE JSON_UNQUOTE(k.JokeIDJSON) NOT IN (
			SELECT JokeID
			FROM RipJokes
			WHERE RipID = RipIDTarget
		);
	
		-- Delete any removed jokes
		DELETE FROM RipJokes 
		WHERE JokeID NOT IN (
			SELECT JSON_UNQUOTE(t.JokeIDJSON)
			FROM JSON_TABLE (json_keys(Jokes), '$[*]' COLUMNS(JokeIDJSON JSON PATH '$')) t
		)
		AND RipID = RipIDTarget;
		
		-- GENRE DATA
		-- Add new Genres
		INSERT INTO RipGenres (RipID, GenreID)
		SELECT RipIDTarget, JSON_UNQUOTE(g.GenreIDJSON)
		FROM JSON_TABLE (Genres, '$[*]' COLUMNS(rn FOR ORDINALITY, GenreIDJSON JSON PATH '$')) g
		WHERE JSON_UNQUOTE(g.GenreIDJSON) NOT IN (
			SELECT GenreID
			FROM RipGenres
			WHERE RipID = RipIDTarget
		);
		
		-- Delete any removed genres
		DELETE FROM RipGenres
		WHERE GenreID NOT IN (
			SELECT JSON_UNQUOTE(g.GenreIDJSON)
			FROM JSON_TABLE (Genres, '$[*]' COLUMNS(rn FOR ORDINALITY, GenreIDJSON JSON PATH '$')) g
		)
		AND RipID = RipIDTarget;
	END IF;

	COMMIT;
END
