-- Stored procedure for selecting a random rip from the database with thin the given criteria. Mainly used for the rip guesser game.

DROP PROCEDURE IF EXISTS RipDB.usp_SelectRandomRip;

CREATE PROCEDURE RipDB.usp_SelectRandomRip(
	IN MinJokes int,
	IN MaxJokes int,
	IN MinRipLength time,
	IN MaxRipLength time,
	IN MetaJokes json,
	IN Metas json,
	IN ExcludedRips json
)
BEGIN
	-- Ensure that all the JSON parameters are valid JSON.
	IF (JSON_LENGTH(MEtaJokes) = 0) THEN
		SET MetaJokes = NULL;
	END IF;

	IF (JSON_LENGTH(Metas) = 0) THEN
		SET Metas = NULL;
	END IF;

	IF (JSON_LENGTH(ExcludedRips) = 0) THEN
		SET ExcludedRips = '[]';
	END IF;

	IF (MinRipLength IS NULL) THEN
		SET MinRipLength = '00:00:03';
	END IF;

	IF (MaxRipLength IS NULL) THEN
		SET MaxRipLength = '00:10:00';
	END IF;

	-- If the joke range is invalid, always prioritise settings the maximum to be the minimum.
	-- E.g. if the min given was 5 and the max was 2, set the max to 5.
	IF (MinJokes > MaxJokes) THEN
		SET MaxJokes = MinJokes;
	END IF;

	-- Since joining an empty json table will yield no results, they need to be checked for being empty, hence the multiple IF conditions below.

	-- If there are no meta jokes or metas given, do not include them in the query.
	IF (MetaJokes IS NULL AND Metas IS NULL) THEN
		SELECT RipId
		FROM vw_RipsDetailed vrd
		WHERE RipLength >= MinRipLength
		AND RipLength  <= MaxRipLength
		AND RipID NOT IN (
			SELECT RipID
			FROM JSON_TABLE (ExcludedRips, '$[*]' COLUMNS(RipID JSON PATH '$')) r
		)
		AND RipID IN (
			SELECT RipID
			FROM RipJokes
			GROUP BY RipID
			HAVING COUNT(JokeID) >= MinJokes AND COUNT(JokeID) <= MaxJokes
		)
		GROUP BY RipID
		ORDER BY RAND() LIMIT 1;

	-- If only Metas are given, filter the results with them.
	ELSEIF (MetaJokes IS NULL) THEN
		SELECT RipId
		FROM vw_RipsDetailed vrd
		WHERE RipLength >= MinRipLength
		AND RipLength  <= MaxRipLength
		AND RipID NOT IN (
			SELECT RipID
			FROM JSON_TABLE (ExcludedRips, '$[*]' COLUMNS(RipID JSON PATH '$')) r
		)
		AND MetaID IN (
			SELECT MetaID
			FROM JSON_TABLE (Metas, '$[*]' COLUMNS(MetaID JSON PATH '$')) mj
		)
		AND RipID IN (
			SELECT RipID
			FROM RipJokes
			GROUP BY RipID
			HAVING COUNT(JokeID) >= MinJokes AND COUNT(JokeID) <= MaxJokes
		)
		GROUP BY RipID
		ORDER BY RAND() LIMIT 1;

	-- If only Meta Jokes are given, filter the results with them.
	ELSEIF (Metas IS NULL) THEN
		SELECT RipId
		FROM vw_RipsDetailed vrd
		WHERE RipLength >= MinRipLength
		AND RipLength  <= MaxRipLength
		AND RipID NOT IN (
			SELECT RipID
			FROM JSON_TABLE (ExcludedRips, '$[*]' COLUMNS(RipID JSON PATH '$')) r
		)
		AND MetaJokeID IN (
			SELECT MetaJokeID
			FROM JSON_TABLE (MetaJokes, '$[*]' COLUMNS(MetaJokeID JSON PATH '$')) mj
		)
		AND RipID IN (
			SELECT RipID
			FROM RipJokes
			GROUP BY RipID
			HAVING COUNT(JokeID) >= MinJokes AND COUNT(JokeID) <= MaxJokes
		)
		GROUP BY RipID
		ORDER BY RAND() LIMIT 1;
	
	-- If both metas and meta jokes are given, filter the results with them.
	ELSE
		SELECT RipId
		FROM vw_RipsDetailed vrd
		WHERE RipLength >= MinRipLength
		AND RipLength  <= MaxRipLength
		AND RipID NOT IN (
			SELECT RipID
			FROM JSON_TABLE (ExcludedRips, '$[*]' COLUMNS(RipID JSON PATH '$')) r
		)
		AND MetaID IN (
			SELECT MetaID
			FROM JSON_TABLE (Metas, '$[*]' COLUMNS(MetaID JSON PATH '$')) mj
		)
		AND MetaJokeID IN (
			SELECT MetaJokeID
			FROM JSON_TABLE (MetaJokes, '$[*]' COLUMNS(MetaJokeID JSON PATH '$')) mj
		)
		AND RipID IN (
			SELECT RipID
			FROM RipJokes
			GROUP BY RipID
			HAVING COUNT(JokeID) >= MinJokes AND COUNT(JokeID) <= MaxJokes
		)
		GROUP BY RipID
		ORDER BY RAND() LIMIT 1;
	END IF;
END
