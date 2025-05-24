-- Jokes definition

CREATE TABLE Jokes (
	JokeID int NOT NULL AUTO_INCREMENT,
	JokeName varchar(512) CHARACTER SET utf8mb4 NOT NULL,
	JokeDescription text DEFAULT NULL COMMENT 'Describes information about the joke',
	PRIMARY KEY (JokeID),
	UNIQUE KEY UQ_JokeName (JokeName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores information about jokes that can be contained within a rip';