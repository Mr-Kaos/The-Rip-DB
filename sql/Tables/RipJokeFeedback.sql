-- Table used to store feedback on rips containing the wrong jokes or data.

CREATE TABLE RipJokeFeedback (
	RipID int NOT NULL,
	JokeFeedback varchar(1024) NOT NULL,
	CONSTRAINT PK_RipID PRIMARY KEY (RipID),
	CONSTRAINT FK_RipID FOREIGN KEY (RipId) REFERENCES Rips(RipID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;