CREATE TABLE JokeMetas (
	JokeId INT NOT NULL,
	MetaJokeID INT NOT NULL,
	CONSTRAINT PK_JokeMetas PRIMARY KEY (JokeId, MetaJokeID),
	CONSTRAINT JokeMetas_Joke_FK FOREIGN KEY (JokeId) REFERENCES Jokes(JokeID),
	CONSTRAINT JokeMetas_Meta_FK FOREIGN KEY (MetaJokeID) REFERENCES MetaJokes(MetaJokeID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores meta joke associations.';
