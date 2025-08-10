-- Stored procedure for inserting playlists into the database.

DROP PROCEDURE IF EXISTS RipDB.usp_UpdatePlaylist;

CREATE PROCEDURE RipDB.usp_UpdatePlaylist(
	IN InPlaylistID int,
	IN InPlaylistName varchar(128),
	IN InPlaylistDescription varchar(512),
	IN Rips json,
	IN AccountId INT,
	IN Public INT)
BEGIN
	-- Ensure the playlist belongs to the given user
	IF (SELECT PlaylistID FROM Playlists WHERE Creator = AccountId AND PlaylistID = InPlaylistID) IS NOT NULL THEN
		UPDATE Playlists SET
			PlaylistName = InPlaylistName,
			PlaylistDescription = InPlaylistName,
			RipIDs = Rips,
			IsPublic = Public
		WHERE PlaylistID = InPlaylistID
		AND Creator = AccountID;
	END IF;
	
	CALL usp_DeleteUnclaimedPlaylists;
	COMMIT;
END