-- Meta Tags
-- These are the most basic/broad tags that are used to group joke metas
INSERT INTO Metas
	(MetaName, MetaDescription)
VALUES
	('Music', 'Anything that is a genre or era of music.'),
	('Video Game', 'Any joke that is from a video game.'),
	('Music Artists', 'Any individual or group who produces and is credited for creating music.'),
	('Film', null),
	('Animation', null),
	('Anime', null),
	('Content Creator', null),
	('Viral Video', null);

-- Standard Tags
-- These are tags that are more specific than meta tags, and are applied directly to jokes
INSERT INTO Tags
	(TagName)
VALUES
	('Theme Song'),
	('VGM'),
	('Animation'),
	('Meme');

-- -- Meta Jokes
-- -- These are jokes that are used to group jokes
INSERT INTO MetaJokes
	(MetaJokeName, MetaJokeDescription, MetaID)
VALUES
	('80s Music', 'Music that was released in the 1980s', 1),
	('90s Music', 'Music that was released in the 1990s', 1),
	('00s Music', 'Music that was released in the 2000s', 1),
	('10s Music', 'Music that was released in the 2010s', 1),
	('Pop Music', 'Music that is classified as "Pop"', 1),
	('Rock Music', 'Music that is classified as "Rock"', 1),
	('Electronic Music', 'Music that is classified as "Electronic"', 1),
	('Classical Music', 'Music that is classified as "Classical"', 1),
	('Disco Music', 'Music that is classified as "Disco"', 1),
	('Video Game Music', 'Music that originated from a video game', 1);