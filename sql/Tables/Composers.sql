CREATE TABLE Composers (
	ComposerID INT auto_increment NOT NULL,
	ComposerFirstName varchar(128) CHARACTER SET utf8mb4 NOT NULL,
	ComposerLastName varchar(128) CHARACTER SET utf8mb4,
	ComposerFirstNameAlt varchar(256) CHARACTER SET utf8mb4,
	ComposerLastNameAlt varchar(256) CHARACTER SET utf8mb4,
	UniqueNameCompute varchar(768) AS (CONCAT(ComposerFirstName, iFNULL(ComposerLastName, ''), IFNULL(ComposerFirstNameAlt, ''))),
	CONSTRAINT PK_Composers PRIMARY KEY (ComposerID),
	UNIQUE KEY UQ_ComposerFullName (UniqueNameCompute)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
AUTO_INCREMENT=1;
