-- Stored procedure for inserting channels into the database easily.

DROP PROCEDURE IF EXISTS usp_UpdateChannel;

CREATE PROCEDURE usp_UpdateChannel(
	IN InChannelID int,
	IN InChannelName varchar(256),
	IN InDescription text,
	IN InURL varchar(512),
	IN InActive int,
	IN InWikiURL varchar(1024)
	)
BEGIN
	-- Check to make sure the channel exists.
	IF (SELECT ChannelID FROM Channels WHERE ChannelID = InChannelID) IS NOT NULL THEN
		UPDATE Channels
		SET ChannelName = InChannelName,
			ChannelDescription = InDescription,
			ChannelURL = InURL,
			WikiURL = InWikiURL,
			IsActive = InActive
		WHERE ChannelID = InChannelID;
	ELSE
		SELECT "This channel does not exist!";
	END IF;
	COMMIT;
END
