DROP VIEW IF EXISTS vw_Composers;

CREATE VIEW vw_Composers AS
SELECT ComposerID, CONCAT(ComposerFirstName, ' ', IFNULL(ComposerLastName, '')) AS ComposerName, CONCAT(IFNULL(ComposerFirstNameAlt, ''), ' ', IFNULL(ComposerLastNameAlt, '')) AS AltName
FROM Composers