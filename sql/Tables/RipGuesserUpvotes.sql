-- Table used to store upvotes/downvotes for rips played in the Rip Guesser game.

CREATE TABLE RipGuesserUpvotes (
	RipID int NOT NULL,
	Upvotes int NOT NULL DEFAULT 0,
	Downvotes int NOT NULL DEFAULT 0,
	CONSTRAINT PK_RipID PRIMARY KEY (RipID),
	CONSTRAINT FK_RipIDUpvote FOREIGN KEY (RipId) REFERENCES Rips(RipID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;