DROP VIEW IF EXISTS vw_GamesDetailed;

CREATE VIEW vw_GamesDetailed AS
SELECT g.GameID, g.GameName, g.GameDescription, g.IsFake, p.PlatformID, p.PlatformName
FROM Games g
LEFT JOIN GamePlatforms gp ON gp.GameID = g.GameID
LEFT JOIN Platforms p ON p.PlatformID = gp.PlatformID;
