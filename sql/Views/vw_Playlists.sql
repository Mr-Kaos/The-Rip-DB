DROP VIEW IF EXISTS vw_Playlists;

CREATE VIEW vw_Playlists AS
SELECT PlaylistID, PlaylistName, ShareCode, IsPublic, Created, Creator, COUNT(Rips) AS RipCount
FROM Playlists
JOIN JSON_TABLE(RipIDs, '$[*]' COLUMNS(Rips JSON PATH '$')) k
GROUP BY PlaylistID, PlaylistName, ShareCode;