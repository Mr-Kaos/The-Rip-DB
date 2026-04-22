-- Stored procedure for inserting playlists into the database.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertPlaylist;

CREATE PROCEDURE RipDB.usp_InsertPlaylist(
	IN InPlaylistName varchar(64),
	IN InPlaylistDescription varchar(512),
	IN Rips json,
	IN AccountId INT,
	IN Public INT,
	OUT NewPlaylistID INT)
BEGIN
	DECLARE NewPlaylistCode CHAR(8);
	DECLARE CodeChars CHAR(36);

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		RESIGNAL;
	END;

	SET CodeChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	START TRANSACTION;

	INSERT INTO Playlists
		(PlaylistName, PlaylistDescription, RipIDs, Creator, IsPublic)
	VALUES
		(InPlaylistName, InPlaylistDescription, Rips, AccountID, Public);

	SET NewPlaylistID = LAST_INSERT_ID();
	CALL usp_GenerateUniqueCode(8, 0, NewPlaylistCode);

	UPDATE Playlists SET
		ShareCode = NewPlaylistCode
	WHERE PlaylistID = NewPlaylistID;

	-- If the account given was null, create a unique code for them to save to retrieve the playlist.
	IF AccountId IS NULL THEN
		CALL usp_GenerateUniqueCode(8, NewPlaylistCode);

		INSERT INTO AnonymousPlaylists
			(PlaylistID, ClaimCode)
		VALUES
			(NewPlaylistID, NewPlaylistCode);
	END IF;

	CALL usp_DeleteUnclaimedPlaylists;
	COMMIT;
END