-- Stored procedure for updating metas.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdateMeta;

CREATE PROCEDURE RipDB.usp_UpdateMeta(
	IN InMetaID int,
	IN InMetaName varchar(128))
BEGIN
	IF (SELECT MetaID FROM Metas WHERE MetaID = InMetaID) IS NOT NULL THEN
		UPDATE Metas SET
			MetaName = InMetaName
		WHERE MetaID = InMetaID;
	END IF;
	COMMIT;
END
