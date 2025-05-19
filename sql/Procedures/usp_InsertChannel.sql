-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertChannel;

CREATE PROCEDURE RipDB.usp_InsertChannel(
	IN NewChannel nvarchar(128),
	IN ChannelDescription text,
	IN URL nvarchar(512))
BEGIN
	IF (SELECT ChannelID FROM Channels WHERE ChannelName = NewChannel) IS NULL THEN
		INSERT INTO Channels
			(ChannelName, ChannelDescription, ChannelURL)
		VALUES
			(NewChannel, ChannelDescription, URL);

		SELECT LAST_INSERT_ID();
	ELSE
		SELECT ChannelID FROM Channels WHERE ChannelName = NewChannel;
	END IF;
	COMMIT;
END
