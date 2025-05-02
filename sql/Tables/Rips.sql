CREATE TABLE Rips (
	RipID int NOT NULL AUTO_INCREMENT,
	RipName varchar(1024) NOT NULL COMMENT 'Name of rip given on YouTube',
	RipDate datetime NOT NULL COMMENT 'Date rip was uploaded',
	RipAlternateName varchar(2048) DEFAULT NULL COMMENT 'Alternative (album) name for rip',
	RipLength time NOT NULL DEFAULT 0,
	RipGame int NOT NULL,
	RipURL varchar(512) DEFAULT NULL COMMENT 'The URL of the rip, accessible online',
	RipDescription text DEFAULT NULL,
	RipChannel int DEFAULT NULL COMMENT 'The YouTube channel that uploaded the rip',
	CONSTRAINT Channels_RipChannel_FK FOREIGN KEY (RipChannel) REFERENCES Channels(ChannelID),
	CONSTRAINT Games_RipGame_FK FOREIGN KEY (RipGame) REFERENCES Games(GameID),
	PRIMARY KEY (`RipID`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores rips'
AUTO_INCREMENT=1;
