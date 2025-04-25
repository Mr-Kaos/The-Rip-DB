CREATE TABLE RipDB.Rippers (
	RipperID INT auto_increment NOT NULL,
	RipperName varchar(256) NOT NULL,
	Aliases json DEFAULT NULL,
	CONSTRAINT PK_Rippers PRIMARY KEY (RipperID),
	CONSTRAINT UQ_RipperName UNIQUE KEY (RipperName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores information about rippers (artists) or rips.'
AUTO_INCREMENT=1;
