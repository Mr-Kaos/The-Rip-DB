CREATE TABLE RipDB.Rips (
  RipID int NOT NULL AUTO_INCREMENT,
  RipName varchar(1024) NOT NULL COMMENT 'Name of rip given on YouTube',
  RipDate datetime NOT NULL COMMENT 'Date rip was uploaded',
  RipAlternateName varchar(2048) DEFAULT NULL COMMENT 'Alternative (album) name for rip',
  PRIMARY KEY (`RipID`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Stores rips'
AUTO_INCREMENT=1;
