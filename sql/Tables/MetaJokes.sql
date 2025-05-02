-- MetaJokes definition

CREATE TABLE MetaJokes (
	MetaJokeID int NOT NULL AUTO_INCREMENT,
	MetaJokeName varchar(128) NOT NULL,
	MetaJokeDescription text NOT NULL,
	MetaTag int NOT NULL,
	PRIMARY KEY (MetaJokeID),
	CONSTRAINT MetaJokes_Tag_FK FOREIGN KEY (MetaTag) REFERENCES Tags(TagID),
	UNIQUE KEY UQ_MetaJokeName (MetaJokeName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores jokes that aren''t explicitly related to a rip, but rather a joke itself. Helps categorise jokes.';