-- RipJokes definition

CREATE TABLE RipJokes (
	RipID INT NOT NULL,
	JokeID INT NOT NULL,
	JokeTimestamps json DEFAULT NULL COMMENT 'An array of start and end timestamps in the rip where the joke is played.',
	JokeComment varchar(1024) DEFAULT NULL,
	CONSTRAINT PK_RipJokes PRIMARY KEY (RipID, JokeID),
	CONSTRAINT RipJokes_Rips_FK FOREIGN KEY (RipID) REFERENCES Rips(RipID),
	CONSTRAINT RipJokes_Jokes_FK FOREIGN KEY (JokeID) REFERENCES Jokes(JokeID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
