DROP VIEW IF EXISTS vw_MetaJokesDetailed;

CREATE VIEW vw_MetaJokesDetailed AS
SELECT mj.MetaJokeID, MetaJokeName, MetaJokeDescription, m.MetaID, MetaName, j.JokeID, j.JokeName, COUNT(j.JokeID) AS AssociatedJokes
FROM MetaJokes mj
JOIN Metas m ON m.MetaID = mj.MetaID
LEFT JOIN JokeMetas jm ON jm.MetaJokeID = mj.MetaJokeID
LEFT JOIN Jokes j ON j.JokeID = jm.JokeID
GROUP BY mj.MetaJokeID, MetaJokeName, MetaJokeDescription, m.MetaID, MetaName, j.JokeID, j.JokeName;