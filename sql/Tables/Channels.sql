-- Channels definition

CREATE TABLE Channels (
	ChannelID int NOT NULL AUTO_INCREMENT,
	ChannelName nvarchar(256) NOT NULL,
	ChannelDescription text NOT NULL COMMENT 'Describes information about the channel',
	ChannelURL nvarchar(512),
	PRIMARY KEY (ChannelID),
	UNIQUE KEY UQ_ChannelName (ChannelName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores information about ripping channels';