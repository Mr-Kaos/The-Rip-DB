CREATE TABLE Metas (
	MetaID INT auto_increment NOT NULL,
	MetaName varchar(128) CHARACTER SET utf8mb4 NOT NULL,
	MetaDescription varchar(1024) CHARACTER SET utf8mb4 DEFAULT NULL,
	CONSTRAINT PK_Metas PRIMARY KEY (MetaID),
	CONSTRAINT UQ_MetaName UNIQUE KEY (MetaName)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
AUTO_INCREMENT=1;
