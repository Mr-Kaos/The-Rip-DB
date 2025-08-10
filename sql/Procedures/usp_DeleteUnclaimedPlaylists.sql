-- Stored procedure to delete any anonymous playlists that have not been claimed for more than 30 days.

DROP PROCEDURE IF EXISTS RipDB.usp_DeleteUnclaimedPlaylists;

CREATE PROCEDURE RipDB.usp_DeleteUnclaimedPlaylists()
BEGIN
	-- Delete any playlists that have not been claimed for more than 30 days.
	DELETE FROM AnonymousPlaylists ap
	WHERE PlaylistID IN (
		SELECT PlaylistID
		FROM Playlists
		WHERE DATEDIFF(NOW(), Created) > 30
	);

	-- Only delete playlists that are not in the anonymous playlists table and have no creator. (i.e. delete the playlists that were deleted above.)
	DELETE FROM Playlists
	WHERE PlaylistID NOT IN (
		SELECT PlaylistID 
		FROM AnonymousPlaylists
	) AND Creator IS NULL;

	COMMIT;
END
