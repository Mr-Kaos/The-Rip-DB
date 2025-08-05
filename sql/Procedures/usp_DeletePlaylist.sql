-- Stored procedure to delete The given playlist.

DROP PROCEDURE IF EXISTS RipDB.usp_DeletePlaylist;

CREATE PROCEDURE RipDB.usp_DeletePlaylist(
	IN InPlaylistID int,
	IN AccountId int
)
BEGIN
	DELETE FROM Playlists
	WHERE PlaylistID = InPlaylistID AND Creator = AccountId;

	COMMIT;
END
