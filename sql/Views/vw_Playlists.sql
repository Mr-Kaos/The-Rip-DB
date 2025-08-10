DROP VIEW IF EXISTS vw_Playlists;

CREATE VIEW vw_Playlists AS
SELECT PlaylistID, PlaylistName, PlaylistDescription, ShareCode, IsPublic, p.Created, Creator, Username, COUNT(Rips) AS RipCount
FROM Playlists p
LEFT JOIN Accounts ON AccountId = Creator
JOIN JSON_TABLE(RipIDs, '$[*]' COLUMNS(Rips JSON PATH '$')) k
GROUP BY PlaylistID, PlaylistName, ShareCode;