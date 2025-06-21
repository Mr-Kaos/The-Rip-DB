-- Table used to store active sessions of Rip Guesser games.

CREATE TABLE RipGuesserGame (
	SessionID char(24) NOT NULL,
	Settings varchar(8192) NOT NULL,
	CurrentRound int NOT NULL DEFAULT 0,
	LastInteraction timestamp DEFAULT NOW(),
	CONSTRAINT PK_RipGuesserGame PRIMARY KEY (SessionID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;