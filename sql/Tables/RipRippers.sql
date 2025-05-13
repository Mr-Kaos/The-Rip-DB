CREATE TABLE RipRippers (
	RipID int NOT NULL,
	RipperId int NOT NULL,
	Alias nvarchar(256) DEFAULT NULL,
	CONSTRAINT RipID_FK FOREIGN KEY (RipID) REFERENCES Rips(RipID),
	CONSTRAINT RipperId_FK FOREIGN KEY (RipperId) REFERENCES Rippers(RipperId),
	CONSTRAINT PK_RipRipper PRIMARY KEY (RipID,RipperID) 
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores associations between rips and rippers.'
AUTO_INCREMENT=1;
