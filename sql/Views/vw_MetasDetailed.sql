DROP VIEW IF EXISTS vw_MetasDetailed;

CREATE VIEW vw_MetasDetailed AS
SELECT m.MetaID, m.MetaName, m.MetaDescription, mj.MetaJokeID, mj.MetaJokeName, COUNT(rj.RipID) AssociatedRips
FROM Metas m
LEFT JOIN MetaJokes mj ON mj.MetaID = m.MetaID
LEFT JOIN JokeMetas jm ON jm.MetaJokeID = mj.MetaJokeID
LEFT JOIN RipJokes rj ON rj.JokeID = jm.JokeId
GROUP BY m.MetaID, m.MetaName, m.MetaDescription, mj.MetaJokeID, mj.MetaJokeName;