DROP VIEW IF EXISTS vw_RipsDetailed;

CREATE VIEW vw_RipsDetailed AS
SELECT r.RipID, r.RipName, r.RipDate, 
r.RipAlternateName, r.RipLength,
r.RipGame, g.GameName, r.RipURL, r.RipAlternateURL, r.RipYouTubeID, r.RipDescription,
r.RipChannel, c.ChannelName, c.ChannelURL, j.JokeID, j.JokeName, ri.RipperID, ri.RipperName,
t.TagID, t.TagName, ge.GenreID, ge.GenreName, mj.MetaJokeID, m.MetaID
FROM Rips r
LEFT JOIN RipJokes rj ON rj.RipID = r.RipID
LEFT JOIN Jokes j ON j.JokeID = rj.JokeID
LEFT JOIN JokeTags jt ON jt.JokeID = j.JokeID
LEFT JOIN Tags t ON t.TagID = jt.TagID
LEFT JOIN RipRippers rr ON rr.RipID = r.RipID
LEFT JOIN Rippers ri ON ri.RipperID = rr.RipperID
LEFT JOIN Games g ON g.GameID = r.RipGame
LEFT JOIN RipGenres rg ON rg.RipID = r.RipID
LEFT JOIN Genres ge ON ge.GenreID = rg.GenreID
LEFT JOIN Channels c ON c.ChannelID = r.RipChannel
LEFT JOIN JokeMetas jm ON jm.JokeID = j.JokeID
LEFT JOIN MetaJokes mj ON mj.MetaJokeID = jm.MetaJokeID
LEFT JOIN Metas m ON m.MetaID = mj.MetaID;