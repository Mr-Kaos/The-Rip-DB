-- Games definition

CREATE TABLE Games (
	GameID int NOT NULL AUTO_INCREMENT,
	GameName nvarchar(128) NOT NULL,
	GameDescription text COMMENT 'Basic information about the game',
	PRIMARY KEY (GameID),
	UNIQUE KEY UQ_GameName (GameName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;