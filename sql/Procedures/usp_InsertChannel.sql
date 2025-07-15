-- Stored procedure for inserting tags into the database easily.

DROP PROCEDURE IF EXISTS RipDB.usp_InsertChannel;

CREATE PROCEDURE RipDB.usp_InsertChannel(
	IN NewChannel varchar(128),
	IN NewDescription text,
	IN NewURL varchar(512),
	OUT ChannelIDOut INT)
BEGIN
	IF (SELECT ChannelID FROM Channels WHERE ChannelName = NewChannel) IS NULL THEN
		INSERT INTO Channels
			(ChannelName, ChannelDescription, ChannelURL)
		VALUES
			(NewChannel, NewDescription, NewURL);

		SET ChannelIDOut = LAST_INSERT_ID();
	ELSE
		SELECT ChannelID INTO ChannelIDOut FROM Channels WHERE ChannelName = NewChannel;
	END IF;
	COMMIT;
END
