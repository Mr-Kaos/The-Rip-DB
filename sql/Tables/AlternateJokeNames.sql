-- Stores alternative names that a joke may have.

CREATE TABLE AlternateJokeNames (
	JokeID int NOT NULL,
	JokeName varchar(512) CHARACTER SET utf8mb4 NOT NULL,
	CONSTRAINT AlternateJokeNames_Jokes_FK FOREIGN KEY (JokeId) REFERENCES Jokes(JokeID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
