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
	SET CodeChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	INSERT INTO Playlists
		(PlaylistName, PlaylistDescription, RipIDs, Creator, IsPublic)
	VALUES
		(InPlaylistName, InPlaylistDescription, Rips, AccountID, Public);

	SET NewPlaylistID = LAST_INSERT_ID();
	SET NewPlaylistCode = CONCAT(
			substring(CodeChars, rand(@seed:=round(rand()*4294967296))*36+1, 1),
			substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
			substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
			substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
			substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
			substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
			substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
			substring(CodeChars, rand(@seed)*36+1, 1)
		);

	UPDATE Playlists SET
		ShareCode = NewPlaylistCode
	WHERE PlaylistID = NewPlaylistID;

	-- If the account given was null, create a unique code for them to save to retrieve the playlist.
	IF AccountId IS NULL THEN
		INSERT INTO AnonymousPlaylists
			(PlaylistID, ClaimCode)
		VALUES
			(NewPlaylistID, CONCAT(
				substring(CodeChars, rand(@seed:=round(rand()*4294967296))*36+1, 1),
				substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
				substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
				substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
				substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
				substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
				substring(CodeChars, rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
				substring(CodeChars, rand(@seed)*36+1, 1)
			));
	END IF;

	CALL usp_DeleteUnclaimedPlaylists;
	COMMIT;
END