-- Stored procedure for claiming anonymous playlists to a specified user account.

DROP PROCEDURE IF EXISTS RipDB.usp_ClaimPlaylists;

CREATE PROCEDURE RipDB.usp_ClaimPlaylists(
	IN ClaimCodes json,
	IN AccountID int)
BEGIN
	-- Associate Playlists
	UPDATE Playlists
	SET Creator = AccountID
	WHERE PlaylistID IN (
		SELECT PlaylistID
		FROM JSON_TABLE (ClaimCodes, '$[*]' COLUMNS(Code JSON PATH '$')) k
		JOIN AnonymousPlaylists ON ClaimCode = k.Code
	);

	-- Remove them from the anonymous playlists list.
	DELETE FROM AnonymousPlaylists
	WHERE ClaimCode IN (
		SELECT Code
		FROM JSON_TABLE (ClaimCodes, '$[*]' COLUMNS(Code JSON PATH '$')) k
	);

	CALL usp_DeleteUnclaimedPlaylists;
	COMMIT;
END