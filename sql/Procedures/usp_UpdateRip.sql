-- This Stored procedure inserts a rip and all related data.

DROP PROCEDURE IF EXISTS usp_UpdateRip;

CREATE PROCEDURE usp_UpdateRip(
	IN RipIDTarget int,
	IN Name varchar(1024),
	IN AlternateName varchar(2048),
	IN Description text,
	IN UploadDate datetime,
	IN Length time,
	IN URL varchar(512),
	IN YTID varchar(12),
	IN AltURL varchar(512),
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
			RipYouTubeID = YTID,
			RipAlternateURL = AltURL,
			RipChannel = Channel
		WHERE RipID = RipIDTarget;
	
		-- RIPPER DATA

		-- Delete all rippers so they can be re-inserted again.
		DELETE FROM RipRippers
		WHERE RipId = RipIDTarget;

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
	
		-- JOKE DATA

		-- Delete all jokes so they can be re-inserted again.
		DELETE FROM RipJokes
		WHERE RipId = RipIDTarget;
		
		-- Add new Jokes
		INSERT INTO RipJokes (RipId, JokeID, JokeTimestamps, JokeComment)
		SELECT RipIDTarget, JSON_UNQUOTE(k.JokeIDJSON), JSON_UNQUOTE(Timestamps), JSON_UNQUOTE(Comment)
		FROM JSON_TABLE (json_keys(Jokes), '$[*]' COLUMNS(rn FOR ORDINALITY, JokeIDJSON JSON PATH '$')) k
		JOIN JSON_TABLE(Jokes, '$.*' COLUMNS (rn FOR ORDINALITY, Timestamps JSON PATH '$.timestamps', Comment JSON PATH '$.comment')) v ON v.rn = k.rn
		WHERE JSON_UNQUOTE(k.JokeIDJSON) NOT IN (
			SELECT JokeID
			FROM RipJokes
			WHERE RipID = RipIDTarget
		);
	
		-- GENRE DATA
		
		-- Delete all genres so they can be re-inserted again.
		DELETE FROM RipGenres
		WHERE RipId = RipIDTarget;
	
		-- Add new Genres
		INSERT INTO RipGenres (RipID, GenreID)
		SELECT RipIDTarget, JSON_UNQUOTE(g.GenreIDJSON)
		FROM JSON_TABLE (Genres, '$[*]' COLUMNS(rn FOR ORDINALITY, GenreIDJSON JSON PATH '$')) g
		WHERE JSON_UNQUOTE(g.GenreIDJSON) NOT IN (
			SELECT GenreID
			FROM RipGenres
			WHERE RipID = RipIDTarget
		);
	END IF;

	COMMIT;
END
