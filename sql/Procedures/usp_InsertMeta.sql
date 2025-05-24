-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertMeta;

CREATE PROCEDURE RipDB.usp_InsertMeta(
	IN NewMetaName varchar(128),
	OUT MetaIDOut INT)
BEGIN
	IF (SELECT MetaID FROM Metas WHERE MetaName = NewMetaName) IS NULL THEN
		INSERT INTO Metas
			(MetaName)
		VALUES
			(NewMetaName);

		SET MetaIDOut = LAST_INSERT_ID();
	ELSE
		SELECT MetaID INTO MetaIDOut FROM Metas WHERE MetaName = NewMetaName;
	END IF;
	COMMIT;
END
