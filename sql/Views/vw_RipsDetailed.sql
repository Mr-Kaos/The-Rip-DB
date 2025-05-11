DROP VIEW IF EXISTS vw_RipsDetailed;

CREATE VIEW vw_RipsDetailed AS
SELECT r.RipID, r.RipName, r.RipDate, 
r.RipAlternateName, r.RipLength,
r.RipGame, g.GameName, r.RipURL, r.RipDescription, r.RipChannel,
j.JokeID, j.JokeName, ri.RipperID, ri.RipperName, t.TagID, t.TagName,
ge.GenreID, ge.GenreName
FROM Rips r
JOIN RipJokes rj ON rj.RipID = r.RipID
JOIN Jokes j ON j.JokeID = rj.JokeID
JOIN JokeTags jt ON jt.JokeID = j.JokeID
JOIN Tags t ON t.TagID = jt.TagID
JOIN RipRippers rr ON rr.RipID = r.RipID
JOIN Rippers ri ON ri.RipperID = rr.RipperID
JOIN Games g ON g.GameID = r.RipGame
JOIN RipGenres rg ON rg.RipID = r.RipID
JOIN Genres ge ON ge.GenreID = rg.GenreID;