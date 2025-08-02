-- Table used to store playlists that have been saved but are not associated to any user.
-- It contains a unique ID that only the creator should know, so that if they decide to create an account, they can restore it to their account.

CREATE TABLE AnonymousPlaylists (
	PlaylistID INT NOT NULL,
	ClaimCode char(8) UNIQUE,
	CONSTRAINT PK_PlaylistID PRIMARY KEY (PlaylistID),
	CONSTRAINT FK_PlaylistID FOREIGN KEY (PlaylistID) REFERENCES Playlists(PlaylistID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;