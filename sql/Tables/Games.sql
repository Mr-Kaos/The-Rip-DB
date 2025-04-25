-- RipDB.Games definition

CREATE TABLE Games (
	GameID int NOT NULL AUTO_INCREMENT,
	GameName varchar(128) NOT NULL,
	GameDescription text NOT NULL COMMENT 'Describes information about the game',
	PRIMARY KEY (GameID),
	UNIQUE KEY UQ_GameName (GameName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;