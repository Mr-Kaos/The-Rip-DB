-- Rip Channels
INSERT INTO Channels
	(ChannelName, ChannelDescription, ChannelURL)
VALUES
	('GiIvaSunner', 'I only upload High Quality rips.', 'https://www.youtube.com/@SiIvaGunner'),
	('SiIvaGunner', 'I only upload High Quality rips.', 'https://www.youtube.com/@SiIvaGunner'),
	('TimmyTurnersGrandDad', 'Earth-shatteringly awesome music and free awesomesauce... What more does one need?', 'https://www.youtube.com/@TimmyTurnersGrandDad');

-- Meta Jokes
CALL usp_InsertMetaJoke('The Flintstones', 'An animated sitcom produced by Hanna-Barbera Productions in the 60s.', 'Animated Series');
CALL usp_InsertMetaJoke('Bootlegs (Video Games)', 'Unofficial black-market video games that often use licensed intellectual property in their games.', 'Video Game');
CALL usp_InsertMetaJoke('Pokémon', 'Japanese media franchise about creatures with special powers that co-exist with humans.', 'Franchise');
CALL usp_InsertMetaJoke('Undertale', 'RPG video game by Toby Fox.', 'Video Game');
CALL usp_InsertMetaJoke('K-Pop', 'Genre of music where music in this genre originates from South Korea.', 'Music'); -- 5
CALL usp_InsertMetaJoke('80s', 'Genre of music where music in this genre was released in the 1980s and is often produced for Western audiences.', 'Music');
CALL usp_InsertMetaJoke('Psy', 'K-Pop artist most well known for his song "Gangnam Style".', 'Artist (Music)');
CALL usp_InsertMetaJoke('Star Wars', 'A sci-fi media franchise created by George Lucas.', 'Franchise');
CALL usp_InsertMetaJoke('Inspector Gadget', '80s kids cartoon television series.', 'Animated Series');
CALL usp_InsertMetaJoke('Smash Mouth', 'Music artist.', 'Artist (Music)'); -- 10
CALL usp_InsertMetaJoke('Disney', 'American multimedia company.', 'Company');
CALL usp_InsertMetaJoke('Anime', 'Animation that originates from Japan.', 'Animation');
CALL usp_InsertMetaJoke('Live Live!', 'Anime series', 'Anime');
CALL usp_InsertMetaJoke('Green Day', 'Music artist', 'Artist (Music)');

-- Jokes
CALL usp_InsertJoke('Grand Dad', 'Funny bootleg Flintstones game popularised by into a meme by Vargskelethor Joel.', 'Meme', '["Video Game"]', '[1,2]');
CALL usp_InsertJoke('Meet the Flintstones', 'Main theme of the cartoon series "The Flintstones"', 'Theme Song', NULL, '[1]');
CALL usp_InsertJoke('Gangam Style', 'Hit K-Pop song from 2012 by Psy', 'Song', NULL, '[5,7]');
CALL usp_InsertJoke('Once Upon a Time', 'Song from Undertale.', 'Song', '["VGM"]', '[4]');
CALL usp_InsertJoke('The Final Countdown', 'Song by Europe.', 'Song', NULL, '[6]'); -- 5
CALL usp_InsertJoke('Megalovania', 'Song from the game Undertale.', 'Song', '["VGM"]', '[4]');
CALL usp_InsertJoke('Blue balls', 'What you expect is about to happen doesn''t happen... for an extended time.', 'Meme', NULL, NULL);
CALL usp_InsertJoke('Maroon 5', 'Pop artist', 'Artist (Music)', NULL, NULL);
CALL usp_InsertJoke('Inspector Gadget', 'Main protagonist of the TV show "Inspector Gadget".', 'Character', '["Cartoon", "TV Show"]', '[9]');
CALL usp_InsertJoke('Star Wars (Main Title)', 'Main title theme of Star Wars by John Williams.', 'Song', '["Series"]', '[8]'); -- 10
CALL usp_InsertJoke('All Star', 'Song by music artist "Smash Mouth".', 'Song', '["Series"]', '[10]');
CALL usp_InsertJoke('Bonetrousle', 'Song from the game Undertale.', 'Song', '["VGM"]', '[4]');
CALL usp_InsertJoke('On The Floor', 'Song by IceJJFish.', 'Song', NULL, NULL);
CALL usp_InsertJoke('Donald Duck', 'Disney character', 'Character', NULL, '[11]');
CALL usp_InsertJoke('Temmie Village', 'Song from the game Undertale.', 'Song', '["VGM"]', '[4]'); -- 15
CALL usp_InsertJoke('Snow halation', 'Song from the Love Live! franchise.', 'Song', '["Anime"]', '[12, 13]');
CALL usp_InsertJoke('YTP4LIFE CRYING', '', 'Meme', NULL, NULL);
CALL usp_InsertJoke('Title Theme & Ending', 'The NES rendition of "Meet the Flintstones" that plays during the title screen and ending of The Flintstones: The Rescue of Dino & Hoppy. The name is unofficial.', 'Song', '["VGM"]', NULL);
CALL usp_InsertJoke('Wake Me Up When September Ends', 'Song by Green Day', 'Song', NULL, '[14]');

-- Ripped "Games"
INSERT INTO Games
	(GameName, GameDescription)
VALUES
	('Pokémon Ruby & Sapphire', 'Pokémon game released on GBA'),
	('The Legend of Zelda: Ocarina of Time', 'Action-adventure video game by Nintendo part of The Legend of Zelda franchise.'),
	('Undertale', 'Indie role-playing video game by Toby Fox.'),
	('Super Mario Sunshine', 'Video game in the Super Mario franchise released on the Nintendo GameCube.'),
	('Mario Kart DS', 'Video game in the Mario Kart series released on the Nintendo DS.'), -- 5
	('Xenoblade Chronicles X', 'JRPG video game on the Wii U.'),
	('Tomodachi Life', 'Life sim video game featuring Mii characters released on the Nintendo 3DS.'),
	('New Super Mario Bros.', '2D platformer in the Super Mario series released on the Nintendo DS.'),
	('Hatsune Miku: Project Mirai DX', 'Rhythm game featuring vocaloid Hatsune Miku released on the Nintendo 3DS.')
	;

-- Rippers
INSERT INTO Rippers
	(RipperName, Aliases)
VALUES
	('Chaze the Chat', NULL),
	('MtH', NULL),
	('dante', NULL),
	('toonlink', '["Chief Keef 2"]'),
	('Albert Softie', NULL), -- 5
	('Sir Spacebar', NULL),
	('Cryptrik', NULL)
	;

INSERT INTO Genres
	(GenreName, GenreDescription)
VALUES
	('Uncategorised', 'Rips in this category need to be categorised!'),
	('Mashup', 'When the rip is mashed up with one or more songs.'),
	('YTP', 'Rips of this type are in the style of YouTube Poops.'),
	('Remix', 'Rips that are remixes of songs.'),
	('Original', 'Rips that are original and are not explicitly based on any existing game or media.'), -- 5
	('Melody Swap', 'Rips where the advertised song''s melody is changed to that of a different song.'),
	('Joke', 'Rips that utilise a specific joke or reference throughout or at some point during the rip, usually with the original song unchanged.'), -- idk a better name for this type.
	('Blue Balls', 'Rips that repeat a certain sequence for an extended period of time.'),
	('Shitpost', 'Rips with the intention of being of poor quality.')
	;

-- Rips
CALL usp_InsertRip_Sample('Battle! (Wild Pokémon) - Pokémon Ruby & Sapphire', 'A Wild Fred Flintstone Appeared!', '2016-01-09', '0102', 1, 'https://www.youtube.com/watch?v=vJsjd8alc8Y', 1, '[1]', '[2]', '[1,2,3]');
CALL usp_InsertRip_Sample('Route 110 - Pokémon Ruby & Sapphire', NULL, '2016-06-09', '0031', 1, 'https://www.youtube.com/watch?v=hRKTKaOtP0I', 1, '[1]', '[2]', '[4]');
CALL usp_InsertRip_Sample('Gerudo Valley - The Legend of Zelda: Ocarina of Time', 'Gerudo Countdown', '2016-01-11', '0146', 2, 'https://www.youtube.com/watch?v=zdFPVzFgl68', 1, '[1]', '[1]', '[5]');
CALL usp_InsertRip_Sample('MEGALOVANIA - Undertale', 'Descending MEGALOVANIA', '2016-01-12', '0137', 3, 'https://www.youtube.com/watch?v=Q9wDLSrLeUE', 1, '[1]', '[2]', '[6,7]');
CALL usp_InsertRip_Sample('Once Upon A Time - Undertale', NULL, '2016-01-13', '0127', 3, 'https://www.youtube.com/watch?v=2_yoDiuwSwE', 1, '[1]', '[2]', '[10]');
CALL usp_InsertRip_Sample('Lost Woods - The Legend of Zelda: Ocarina of Time', NULL, '2016-01-14', '0106', 2, 'https://www.youtube.com/watch?v=da5kSUVbaI4', 1, '[1]', '[1]', '[2]');
CALL usp_InsertRip_Sample('A Secret Course - Super Mario Sunshine', NULL, '2016-01-14', '0131', 3, 'https://www.youtube.com/watch?v=ZTga1rjryhE', 1, '[1]', '[1]', '[11]');
CALL usp_InsertRip_Sample('Secret Course - Super Mario Sunshine', 'Super Mario Shrekshine: All Secret Course', '2016-01-15', '0130', 4, 'https://www.youtube.com/watch?v=ryZvC68xE_s', 1, '[1]', '[1]', '[11]');
CALL usp_InsertRip_Sample('Luigi''s Mansion - Mario Kart DS', NULL, '2016-01-17', '0131', 5, 'https://www.youtube.com/watch?v=p7RsftFX9ak', 1, '[1]', '[2]', '[12]');
CALL usp_InsertRip_Sample('Uncontrollable - Xenoblade Chronicles X', NULL, '2016-01-18', '0349', 6, 'https://www.youtube.com/watch?v=pTeXKobmqWk', 1, '[1]', '[3]', '[3]');
CALL usp_InsertRip_Sample('Map (Day) - Tomodachi Life', 'On the Island', '2016-01-18', '0053', 7, 'https://www.youtube.com/watch?v=XhG9rWtjcGQ', 1, '[1]', '[4]', '[13]');
CALL usp_InsertRip_Sample('Uncontrollable (Alternate Mix) - Xenoblade Chronicles X', NULL, '2016-01-19', '0345', 6, 'https://www.youtube.com/watch?v=6nRC_dlsJ1I', 1, '[1]', '[5]', '[14]');
CALL usp_InsertRip_Sample('Overworld Theme (Original Mix) - New Super Mario Bros.', NULL, '2016-01-19', '0128', 8, 'https://www.youtube.com/watch?v=Ct3Z7LEoOPM', 1, '[1]', '[2]', '[15]');
CALL usp_InsertRip_Sample('Hopes and Dreams - Undertale', NULL, '2016-01-20', '0301', 3, 'https://www.youtube.com/watch?v=Bhs3Q7-kLHs', 1, '[1]', '[6]', '[16]');
CALL usp_InsertRip_Sample('The key we''ve lost - Xenoblade Chronicles X', 'The channel ytp4life lost', '2016-01-20', '0611', 6, 'https://www.youtube.com/watch?v=SezWmzgp6uQ', 1, '[1]', '[3]', '[17]');
CALL usp_InsertRip_Sample('MEGALOVANIA (Beta Mix) - Undertale', 'Grand Dadlovania', '2016-01-28', '0049', 3, 'https://www.youtube.com/watch?v=4wXW_ex5Nvs', 1, '[1]', '[1]', '[2, 6]');
CALL usp_InsertRip_Sample('Last Goodbye (Alternate Mix) - Undertale', 'Everlasting Goodbye', '2016-01-29', '0049', 3, 'https://www.youtube.com/watch?v=rSuYr0dR2gw', 1, '[2]', '[2]', '[18]');
CALL usp_InsertRip_Sample('Last Goodbye (Beta Mix) - Undertale', NULL, '2016-01-29', '0215', 3, 'https://www.youtube.com/watch?v=gIEbix3m68g', 1, '[8]', '[5]', '[7]');
CALL usp_InsertRip_Sample('My Room (Naturale) - Hatsune Miku: Project Mirai DX', NULL, '2016-01-31', '0308', 9, 'https://www.youtube.com/watch?v=yS80Lx9d6ug', 1, '[2]', '[7]', '[19]');
-- CALL usp_InsertRip_Sample('', NULL, '2016-01-20', '', 1, '', 1, '[1]', '[]', '[]');