DROP VIEW IF EXISTS vw_JokesDetailed;

CREATE VIEW vw_JokesDetailed AS
SELECT j.*, t.*, jt.IsPrimary, mj.MetaJokeID, mj.MetaJokeName, mj.MetaID, m.MetaName
FROM Jokes j
LEFT JOIN JokeTags jt  ON jt.JokeID = j.JokeID 
LEFT JOIN Tags t ON jt.TagID  = t.TagID 
LEFT JOIN JokeMetas jm ON jm.JokeId  = j.JokeID 
LEFT JOIN MetaJokes mj ON mj.MetaJokeID  = jm.MetaJokeID
LEFT JOIN Metas m ON m.MetaID  = mj.MetaID 