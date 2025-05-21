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

-- Rip Channels
INSERT INTO Channels
	(ChannelName, ChannelDescription, ChannelURL)
VALUES
	('GiIvaSunner', 'I only upload High Quality rips.', 'https://www.youtube.com/@SiIvaGunner'),
	('SiIvaGunner', 'I only upload High Quality rips.', 'https://www.youtube.com/@SiIvaGunner'),
	('TimmyTurnersGrandDad', 'Earth-shatteringly awesome music and free awesomesauce... What more does one need?', 'https://www.youtube.com/@TimmyTurnersGrandDad');

-- Genres
INSERT INTO Genres
	(GenreName, GenreDescription)
VALUES
	('Uncategorised', 'Rips in this category need to be categorised!'),
	('Mashup', 'When the rip is mashed up with one or more songs.'),
	('YTP', 'Rips of this type are in the style of YouTube Poops.'),
	('Remix', 'Rips that change up the style and sound of a song using audio mixing techniques.'),
	('Original', 'Rips that are original and are not explicitly based on any existing game or media.'), -- 5
	('Melody Swap', 'Rips where the advertised song''s melody is changed to that of a different song.'),
	('Blue Balls', 'Rips that repeat a certain sequence for an extended period of time.'),
	('Arrangement', 'Rips which feature a song remade in a different style and instrumentation.'),
	('MIDI Swap', 'Rips that where the soundfont of one game is used in another game''s sequenced track in place of its original soundfont.'),
	('Medley', 'Rips that utilise five our more unique sources.'), -- 10
	('Melody Addition', 'Rips that layer instruments over another piece of music that sound similar to the original track.'),
	('Cover', 'Rips that are a new performance or recording of an existing song by another performer or composer.'),
	('Sentence Mixing', 'Rips that utilise samples of voice lines or lyrics of a song to form new sentences or words, typically for comedic effect.'),
	('Pitch Shifting', 'Rips where the pitch of a sample is changed to match the key of the instrumental.'), -- 15
	('Shitpost', 'Rips with the intention of being of poor quality and unenjoyable to listen to.');