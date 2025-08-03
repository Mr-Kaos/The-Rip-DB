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

	DELETE FROM Playlists
	WHERE DATEDIFF(NOW(), Created) > 30;

	COMMIT;
END
