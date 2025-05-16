-- MetaJokes definition

CREATE TABLE MetaJokes (
	MetaJokeID int NOT NULL AUTO_INCREMENT,
	MetaJokeName nvarchar(128) NOT NULL,
	MetaJokeDescription text,
	MetaID int NOT NULL,
	PRIMARY KEY (MetaJokeID),
	CONSTRAINT MetaJokes_Tag_FK FOREIGN KEY (MetaID) REFERENCES Metas(MetaID),
	UNIQUE KEY UQ_MetaJokeName (MetaJokeName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores jokes that aren''t explicitly related to a rip, but rather a joke itself. Helps categorise jokes.';