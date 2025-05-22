CREATE TABLE Rips (
	RipID int NOT NULL AUTO_INCREMENT,
	RipName nvarchar(1024) NOT NULL COMMENT 'Name of rip given on YouTube',
	RipDate datetime NOT NULL COMMENT 'Date rip was uploaded',
	RipAlternateName nvarchar(2048) DEFAULT NULL COMMENT 'Alternative name for rip (album release name)',
	RipLength time NOT NULL DEFAULT 0,
	RipGame int NOT NULL,
	RipURL nvarchar(512) NOT NULL COMMENT 'The URL of the rip, accessible online',
	RipYouTubeID nvarchar(64) DEFAULT NULL,
	RipAlternateURL nvarchar(512) DEFAULT NULL COMMENT 'An alternate URL of the rip, typically for the alternate release',
	RipDescription text DEFAULT NULL,
	RipChannel int DEFAULT NULL COMMENT 'The YouTube channel that uploaded the rip',
	CONSTRAINT Channels_RipChannel_FK FOREIGN KEY (RipChannel) REFERENCES Channels(ChannelID),
	CONSTRAINT Games_RipGame_FK FOREIGN KEY (RipGame) REFERENCES Games(GameID),
	CONSTRAINT UQ_RipURL UNIQUE KEY (RipURL),
	PRIMARY KEY (`RipID`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores rips'
AUTO_INCREMENT=1;
