CREATE TABLE RipDB.JokeMetas (
	JokeId INT NOT NULL,
	MetaJokeID INT NOT NULL,
	CONSTRAINT PK_JokeMetas PRIMARY KEY (JokeId, MetaJokeID),
	CONSTRAINT JokeMetas_Joke_FK FOREIGN KEY (JokeId) REFERENCES RipDB.Jokes(JokeID),
	CONSTRAINT JokeMetas_Meta_FK FOREIGN KEY (MetaJokeID) REFERENCES RipDB.MetaJokes(MetaJokeID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores meta joke associations.';
