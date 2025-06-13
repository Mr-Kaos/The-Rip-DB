-- Games definition

CREATE TABLE Games (
	GameID int NOT NULL AUTO_INCREMENT,
	GameName varchar(256) CHARACTER SET utf8mb4 NOT NULL,
	GameDescription text COMMENT 'Basic information about the game',
	PRIMARY KEY (GameID),
	UNIQUE KEY UQ_GameName (GameName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;