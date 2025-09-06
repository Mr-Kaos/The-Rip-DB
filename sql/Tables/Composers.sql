CREATE TABLE Composers (
	ComposerID INT auto_increment NOT NULL,
	ComposerFirstName varchar(256) CHARACTER SET utf8mb4 NOT NULL,
	ComposerLastName varchar(256) CHARACTER SET utf8mb4,
	ComposerFirstNameAlt varchar(256) CHARACTER SET utf8mb4,
	ComposerLastNameAlt varchar(256) CHARACTER SET utf8mb4,
	CONSTRAINT PK_Composers PRIMARY KEY (ComposerID)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
AUTO_INCREMENT=1;
