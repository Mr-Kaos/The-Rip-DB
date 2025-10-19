-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertChannel;

CREATE PROCEDURE RipDB.usp_InsertChannel(
	IN InChannel varchar(128),
	IN InDescription text,
	IN InURL varchar(512),
	IN InActive int,
	IN InWikiURL varchar(1024),
	OUT ChannelIDOut INT)
BEGIN
	IF (SELECT ChannelID FROM Channels WHERE ChannelName = InChannel) IS NULL THEN
		INSERT INTO Channels
			(ChannelName, ChannelDescription, ChannelURL, WikiURL, IsActive)
		VALUES
			(InChannel, InDescription, InURL, InWikiURL, InActive);

		SET ChannelIDOut = LAST_INSERT_ID();
	ELSE
		SELECT ChannelID INTO ChannelIDOut FROM Channels WHERE ChannelName = InChannel;
	END IF;
	COMMIT;
END
