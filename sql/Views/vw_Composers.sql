DROP VIEW IF EXISTS vw_Composers;

CREATE VIEW vw_Composers AS
SELECT ComposerID, CONCAT(ComposerFirstName, ' ', ComposerLastName) AS ComposerName, CONCAT(ComposerFirstNameAlt, ', ', ComposerLastNameAlt) AS AltName
FROM Composers