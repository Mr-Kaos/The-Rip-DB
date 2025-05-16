DROP VIEW IF EXISTS vw_JokesDetailed;

CREATE VIEW vw_JokesDetailed AS
SELECT j.*, t.*, jt.IsPrimary, mj.MetaJokeID, mj.MetaJokeName, mj.MetaID, m.MetaName
FROM Jokes j
JOIN JokeTags jt  ON jt.JokeID = j.JokeID 
JOIN Tags t ON jt.TagID  = t.TagID 
JOIN JokeMetas jm ON jm.JokeId  = j.JokeID 
JOIN MetaJokes mj ON mj.MetaJokeID  = jm.MetaJokeID
JOIN Metas m ON m.MetaID  = mj.MetaID 