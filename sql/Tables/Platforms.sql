CREATE TABLE Platforms (
	PlatformID INT auto_increment NOT NULL,
	PlatformName varchar(128) CHARACTER SET utf8mb4 NOT NULL,
	CONSTRAINT PK_Platforms PRIMARY KEY (PlatformID),
	CONSTRAINT UQ_PlatformName UNIQUE KEY (PlatformName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
AUTO_INCREMENT=1;
