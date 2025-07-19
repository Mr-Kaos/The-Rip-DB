-- Metas
-- These are the most basic/broad tags that are used to group meta jokes
CALL usp_InsertMeta('Music', @out);
CALL usp_InsertMeta('Video Game', @out);
CALL usp_InsertMeta('Animated Series', @out);
CALL usp_InsertMeta('TV Series', @out);
CALL usp_InsertMeta('Anime', @out); -- 5
CALL usp_InsertMeta('Artist', @out);
CALL usp_InsertMeta('Film', @out);
CALL usp_InsertMeta('Company', @out);
CALL usp_InsertMeta('Content Creator', @out);

-- Standard Tags
-- These are tags that are more specific than meta tags, and are applied directly to jokes
CALL usp_InsertTag('Song', @out);
CALL usp_InsertTag('Theme Song', @out);
CALL usp_InsertTag('Meme', @out);
CALL usp_InsertTag('Character', @out);
CALL usp_InsertTag('Voiceline', @out); -- 5
CALL usp_InsertTag('Video', @out);
CALL usp_InsertTag('YTP', @out);
CALL usp_InsertTag('Commercial', @out);

-- Meta Jokes
-- These are jokes that are used to group jokes
CALL usp_InsertMetaJoke('1960s Music', 'Music released in between 1960 and 1969', 1, @out);
CALL usp_InsertMetaJoke('1970s Music', 'Music released in between 1970 and 1979', 1, @out);
CALL usp_InsertMetaJoke('1980s Music', 'Music released in between 1980 and 1989', 1, @out);
CALL usp_InsertMetaJoke('1990s Music', 'Music released in between 1990 and 1999', 1, @out);
CALL usp_InsertMetaJoke('2000s Music', 'Music released in between 2000 and 2009', 1, @out); -- 5
CALL usp_InsertMetaJoke('2010s Music', 'Music released in between 2010 and 2019', 1, @out);
CALL usp_InsertMetaJoke('2020s Music', 'Music released in between 2020 and 2029', 1, @out);
CALL usp_InsertMetaJoke('Pop Music', 'Music that is classified as "Pop"', 1, @out);
CALL usp_InsertMetaJoke('Rock Music', 'Music that is classified as "Rock"', 1, @out);
CALL usp_InsertMetaJoke('Electronic Music', 'Music that is classified as "Electronic"', 1, @out); -- 10
CALL usp_InsertMetaJoke('Classical Music', 'Music that is classified as "Classical"', 1, @out);
CALL usp_InsertMetaJoke('Jazz Music', 'Music that is classified as "Jazz"', 1, @out);
CALL usp_InsertMetaJoke('Disco Music', 'Music that is classified as "Disco"', 1, @out);
CALL usp_InsertMetaJoke('Video Game Music', 'Music that originates from a video game', 1, @out);

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