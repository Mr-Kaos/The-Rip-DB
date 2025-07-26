-- Table used to store playlists of rips. Mainly used for RipGuesser games.

CREATE TABLE Playlists (
	PlaylistID char(24) NOT NULL,
	PlaylistName varchar(128),
	RipIDs json NOT NULL,
	Creator int NOT NULL,
	CONSTRAINT PK_PlaylistID PRIMARY KEY (PlaylistID),
	CONSTRAINT FK_Creator FOREIGN KEY (Creator) REFERENCES Accounts(AccountID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;