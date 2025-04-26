DROP TRIGGER IF EXISTS b_upd_EnsureSinglePrimaryTag;

DELIMITER $$
$$
CREATE TRIGGER b_upd_EnsureSinglePrimaryTag
BEFORE UPDATE
ON JokeTags FOR EACH ROW
BEGIN
	DECLARE PrimaryExists bit;

	SELECT IsPrimary
	INTO PrimaryExists
	FROM JokeTags
	WHERE JokeID = NEW.JokeID
	ORDER BY IsPrimary DESC
	LIMIT 1;

	IF PrimaryExists = 1 AND NEW.IsPrimary = 1 THEN
		SET NEW.IsPrimary = 0;
	END IF;
END$$
DELIMITER ;
