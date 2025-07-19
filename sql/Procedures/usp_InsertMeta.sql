-- Stored procedure for inserting metas into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertMeta;

CREATE PROCEDURE RipDB.usp_InsertMeta(
	IN InMetaName varchar(128),
	OUT MetaIDOut INT)
BEGIN
	IF (SELECT MetaID FROM Metas WHERE MetaName = InMetaName) IS NULL THEN
		INSERT INTO Metas
			(MetaName)
		VALUES
			(InMetaName);

		SET MetaIDOut = LAST_INSERT_ID();
	ELSE
		SELECT MetaID INTO MetaIDOut FROM Metas WHERE MetaName = InMetaName;
	END IF;
	COMMIT;
END
