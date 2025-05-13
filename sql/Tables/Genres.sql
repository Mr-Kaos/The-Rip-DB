CREATE TABLE Genres (
	GenreID INT auto_increment NOT NULL,
	GenreName nvarchar(128) NOT NULL,
	GenreDescription nvarchar(1024) DEFAULT NULL,
	CONSTRAINT PK_Types PRIMARY KEY (GenreID),
	CONSTRAINT UQ_GenreName UNIQUE KEY (GenreName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
AUTO_INCREMENT=1;
