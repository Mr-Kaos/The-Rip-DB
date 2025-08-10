-- Table used to store playlists of rips. Mainly used for RipGuesser games.
-- Playlists without an creator (account) associated to them are deleted after 30 days.

CREATE TABLE Playlists (
	PlaylistID INT NOT NULL AUTO_INCREMENT,
	ShareCode char(8),
	PlaylistName varchar(64),
	PlaylistDescription varchar(512),
	RipIDs json NOT NULL,
	Creator int,
	IsPublic bit NOT NULL DEFAULT 0 COMMENT 'Determines if this playlist is searchable and visible in the RipGuesser playlists search, or if it is only accessible by its code.',
	Created datetime NOT NULL DEFAULT NOW(),
	CONSTRAINT PK_PlaylistID PRIMARY KEY (PlaylistID),
	CONSTRAINT FK_Creator FOREIGN KEY (Creator) REFERENCES Accounts(AccountID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;