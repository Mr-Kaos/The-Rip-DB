-- RipJokes definition

CREATE TABLE RipJokes (
	RipID INT NOT NULL,
	JokeID INT NOT NULL,
	JokeTimestamps json DEFAULT NULL COMMENT 'An array of objects defining timestamps of the start and end of the joke in the rip. Timestamps should be strings with the colons removed.',
	JokeComment varchar(1024) CHARACTER SET utf8mb4 DEFAULT NULL,
	CONSTRAINT PK_RipJokes PRIMARY KEY (RipID, JokeID),
	CONSTRAINT RipJokes_Rips_FK FOREIGN KEY (RipID) REFERENCES Rips(RipID),
	CONSTRAINT RipJokes_Jokes_FK FOREIGN KEY (JokeID) REFERENCES Jokes(JokeID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
