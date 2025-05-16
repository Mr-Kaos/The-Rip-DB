CREATE TABLE DEBUG (
	String NVARCHAR(128),
	Nullable varchar(32),
	NumberVal INT,
	BoolVal BIT
);

DROP PROCEDURE IF EXISTS DEBUG;

DELIMITER $$
$$
CREATE PROCEDURE DEBUG(
	IN String nvarchar(128),
	IN Nullable varchar(32),
	IN NumberVal INT,
	IN BoolVal bit)
BEGIN
	INSERT INTO DEBUG
		(String, Nullable, NumberVal, BoolVal)
	VALUES
		(String, Nullable, NumberVal, BoolVal);
END
$$
DELIMITER ;
