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
CALL usp_InsertMetaJoke('Yakuza', 'Video game franchise', 'Video Game'); -- 15
CALL usp_InsertMetaJoke('Touhou', 'Bullet hell video game series', 'Video Game');
CALL usp_InsertMetaJoke('Eurobeat', 'Genre of music', 'Music');
CALL usp_InsertMetaJoke('Super Mario', 'Video game franchise', 'Video Game');
CALL usp_InsertMetaJoke('Classical', 'Musical genre', 'Music');
CALL usp_InsertMetaJoke('Beatles', 'Music band', 'Artist (Music)'); -- 20
CALL usp_InsertMetaJoke('Backstreet Boys', 'Music band', 'Artist (Music)');

-- Jokes
CALL usp_InsertJoke_TESTING('Grand Dad', 'Funny bootleg Flintstones game popularised by into a meme by Vargskelethor Joel.', 'Meme', '["Video Game"]', '[1,2]');
CALL usp_InsertJoke_TESTING('Meet the Flintstones', 'Main theme of the cartoon series "The Flintstones"', 'Theme Song', NULL, '[1]');
CALL usp_InsertJoke_TESTING('Gangam Style', 'Hit K-Pop song from 2012 by Psy', 'Song', NULL, '[5,7]');
CALL usp_InsertJoke_TESTING('Once Upon a Time', 'Song from Undertale.', 'Song', '["VGM"]', '[4]');
CALL usp_InsertJoke_TESTING('The Final Countdown', 'Song by Europe.', 'Song', NULL, '[6]'); -- 5
CALL usp_InsertJoke_TESTING('Megalovania', 'Song from the game Undertale.', 'Song', '["VGM"]', '[4]');
CALL usp_InsertJoke_TESTING('Blue balls', 'What you expect is about to happen doesn''t happen... for an extended time.', 'Meme', NULL, NULL);
CALL usp_InsertJoke_TESTING('Maroon 5', 'Pop artist', 'Artist (Music)', NULL, NULL);
CALL usp_InsertJoke_TESTING('Inspector Gadget', 'Themse song of the TV show "Inspector Gadget".', 'Theme Song', '["Cartoon", "TV Show"]', '[9]');
CALL usp_InsertJoke_TESTING('Star Wars (Main Title)', 'Main title theme of Star Wars by John Williams.', 'Song', '["Series"]', '[8]'); -- 10
CALL usp_InsertJoke_TESTING('All Star', 'Song by music artist "Smash Mouth".', 'Song', '["Series"]', '[10]');
CALL usp_InsertJoke_TESTING('Bonetrousle', 'Song from the game Undertale.', 'Song', '["VGM"]', '[4]');
CALL usp_InsertJoke_TESTING('On The Floor', 'Song by IceJJFish.', 'Song', NULL, NULL);
CALL usp_InsertJoke_TESTING('Donald Duck', 'Disney character', 'Character', NULL, '[11]');
CALL usp_InsertJoke_TESTING('Temmie Village', 'Song from the game Undertale.', 'Song', '["VGM"]', '[4]'); -- 15
CALL usp_InsertJoke_TESTING('Snow halation', 'Song from the Love Live! franchise.', 'Song', '["Anime"]', '[12, 13]');
CALL usp_InsertJoke_TESTING('YTP4LIFE CRYING', '', 'Meme', NULL, NULL);
CALL usp_InsertJoke_TESTING('Title Theme & Ending', 'The NES rendition of "Meet the Flintstones" that plays during the title screen and ending of The Flintstones: The Rescue of Dino & Hoppy. The name is unofficial.', 'Song', '["VGM"]', NULL);
CALL usp_InsertJoke_TESTING('Wake Me Up When September Ends', 'Song by Green Day', 'Song', NULL, '[14]');
CALL usp_InsertJoke_TESTING('Pledge of Demon', 'Song from Yakuza 0', 'Song', '["VGM"]', '[15]'); -- 20
CALL usp_InsertJoke_TESTING('Rock My Emotions', '', 'Song', NULL, NULL);
CALL usp_InsertJoke_TESTING('Futatsuiwa from Sado', '', 'Song', NULL, '[16]');
CALL usp_InsertJoke_TESTING('Bad Apple!! feat.nomico', '', 'Song', NULL, '[16]');
CALL usp_InsertJoke_TESTING('Bad Apple', 'Song', '', '["VGM"]', '[16]');
CALL usp_InsertJoke_TESTING('U.N. Owen Was Her?', '', 'Song', NULL, '[16]'); -- 25
CALL usp_InsertJoke_TESTING('Running in the 90s', '', 'Song', NULL, '[17]');
CALL usp_InsertJoke_TESTING('Tetris - Type A', '', 'Song', NULL, NULL);
CALL usp_InsertJoke_TESTING('Beware the Forest''s Mushrooms', '', 'Song', '["VGM"]', '[18]');
CALL usp_InsertJoke_TESTING('In the Hall of the Mountain King', '', 'Song', NULL, '[19]');
CALL usp_InsertJoke_TESTING('OMNI FIX YOUR PASSWORD', '', 'Video', NULL, NULL); -- 30
CALL usp_InsertJoke_TESTING('Temporary Secretary', '', 'Song', NULL, '[20]');
CALL usp_InsertJoke_TESTING('We are Leo', '', 'Band', NULL, NULL);
CALL usp_InsertJoke_TESTING('Deez Nuts!', '', 'Meme', NULL, NULL);
CALL usp_InsertJoke_TESTING('Deez Nuts! [Trap Remix]', '', 'Song', '["Meme"]', NULL);
CALL usp_InsertJoke_TESTING('Bonfire', '', 'Song', NULL, NULL); -- 35
CALL usp_InsertJoke_TESTING('Harlem Shake', '', 'Song', '["Meme"]', NULL);
CALL usp_InsertJoke_TESTING('Ore Ida Pizza Bagel Bites', '', 'Commercial', '["YTP"]', NULL);
CALL usp_InsertJoke_TESTING('I Want It That Way', '', 'Song', NULL, '[21]');

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
	('Hatsune Miku: Project Mirai DX', 'Rhythm game featuring vocaloid Hatsune Miku released on the Nintendo 3DS.'),
	('MOTHER 3', ''), -- 10
	('Half-Life 2', ''),
	('Drawn ro Life', ''),
	('Mega Man 7', ''),
	('Deltarune', ''),
	('Super Mario 64', ''), -- 15
	('Cave Story', '')
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
	('Cryptrik', NULL),
	('eg_9371', NULL),
	('l4ureleye', NULL),
	('Jp', NULL), -- 10
	('Krizis', NULL),
	('Spicy236', NULL)
	;

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

-- Rips
CALL usp_InsertRip('Battle! (Wild Pokémon) - Pokémon Ruby & Sapphire', 'A Wild Fred Flintstone Appeared!', NULL, '2016-01-09', '0102', 'https://www.youtube.com/watch?v=vJsjd8alc8Y', 1, 1, '[1]', '{"2": {"timestamps":[{"start":"0012","end":"0101"}],"comment":null}, "1": {"timestamps":[{"start":"0012","end":"0102"}],"comment":"Visual edit"}, "3": {"timestamps":[{"start":"0101","end":"0102"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Route 110 - Pokémon Ruby & Sapphire', NULL, NULL, '2016-06-09', '0031', 'https://www.youtube.com/watch?v=hRKTKaOtP0I', 1, 1, '[1]', '{"4": {"timestamps":[{"start":"0000","end":"0031"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Gerudo Valley - The Legend of Zelda: Ocarina of Time', 'Gerudo Countdown', NULL, '2016-01-11', '0146', 'https://www.youtube.com/watch?v=zdFPVzFgl68', 2, 1, '[1]', '{"5": {"timestamps":[{"start":"0000","end":"0146"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('MEGALOVANIA - Undertale', 'Descending MEGALOVANIA', NULL, '2016-01-12', '0137', 'https://www.youtube.com/watch?v=Q9wDLSrLeUE', 3, 1, '[7]', '{"6": {"timestamps":[{"start":"0000","end":"0137"}],"comment":null}, "7": {"timestamps":[{"start":"0000","end":"0137"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Once Upon A Time - Undertale', NULL, NULL, '2016-01-13', '0127', 'https://www.youtube.com/watch?v=2_yoDiuwSwE', 3, 1, '[1]', '{"10": {"timestamps":[{"start":"0000","end":"0127"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Lost Woods - The Legend of Zelda: Ocarina of Time', NULL, NULL, '2016-01-14', '0106', 'https://www.youtube.com/watch?v=da5kSUVbaI4', 2, 1, '[1]', '{"2": {"timestamps":[{"start":"0002","end":"0106"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('A Secret Course - Super Mario Sunshine', NULL, NULL, '2016-01-14', '0131', 'https://www.youtube.com/watch?v=ZTga1rjryhE', 3, 1, '[1]', '{"11": {"timestamps":[{"start":"0007","end":"0131"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Secret Course - Super Mario Sunshine', 'Super Mario Shrekshine: All Secret Course', NULL, '2016-01-15', '0130', 'https://www.youtube.com/watch?v=ryZvC68xE_s', 4, 1, '[1]', '{"11": {"timestamps":[{"start":"0006","end":"0130"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Luigi''s Mansion - Mario Kart DS', NULL, NULL, '2016-01-17', '0131', 'https://www.youtube.com/watch?v=p7RsftFX9ak', 5, 1, '[1]', '{"12": {"timestamps":[{"start":"0000","end":"0131"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Uncontrollable - Xenoblade Chronicles X', NULL, NULL, '2016-01-18', '0349', 'https://www.youtube.com/watch?v=pTeXKobmqWk', 6, 1, '[1]', '{"3": {"timestamps":[{"start":"0028","end":"0349"}],"comment":null}}', '{"3":null}');
CALL usp_InsertRip('Map (Day) - Tomodachi Life', 'On the Island', NULL, '2016-01-18', '0053', 'https://www.youtube.com/watch?v=XhG9rWtjcGQ', 7, 1, '[1]', '{"13": {"timestamps":[{"start":"0000","end":"0053"}],"comment":null}}', '{"4":null}');
CALL usp_InsertRip('Uncontrollable (Alternate Mix) - Xenoblade Chronicles X', NULL, NULL, '2016-01-19', '0345', 'https://www.youtube.com/watch?v=6nRC_dlsJ1I', 6, 1, '[1]', '{"14": {"timestamps":[{"start":"0000","end":"0345"}],"comment":null}}', '{"5":null}');
CALL usp_InsertRip('Overworld Theme (Original Mix) - New Super Mario Bros.', NULL, NULL, '2016-01-19', '0128', 'https://www.youtube.com/watch?v=Ct3Z7LEoOPM', 8, 1, '[1]', '{"15": {"timestamps":[{"start":"0012","end":"0128"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Hopes and Dreams - Undertale', NULL, NULL, '2016-01-20', '0301', 'https://www.youtube.com/watch?v=Bhs3Q7-kLHs', 3, 1, '[1]', '{"16": {"timestamps":[{"start":"0011","end":"0301"}],"comment":null}}', '{"6":null}');
CALL usp_InsertRip('The key we''ve lost - Xenoblade Chronicles X', 'The channel ytp4life lost', NULL, '2016-01-20', '0611', 'https://www.youtube.com/watch?v=SezWmzgp6uQ', 6, 1, '[1]', '{"17": {"timestamps":[{"start":"0000","end":"0611"}],"comment":null}}', '{"3":null}');
CALL usp_InsertRip('MEGALOVANIA (Beta Mix) - Undertale', 'Grand Dadlovania', NULL, '2016-01-28', '0049', 'https://www.youtube.com/watch?v=4wXW_ex5Nvs', 3, 1, '[1]', '{"2": {"timestamps":[{"start":"0000","end":"0049"}],"comment":null},"6": {"timestamps":[{"start":"0007","end":"0039"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Last Goodbye (Alternate Mix) - Undertale', 'Everlasting Goodbye', NULL, '2016-01-29', '0049', 'https://www.youtube.com/watch?v=rSuYr0dR2gw', 3, 1, '[2]', '{"18": {"timestamps":[{"start":"0005","end":"0049"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Last Goodbye (Beta Mix) - Undertale', NULL, NULL, '2016-01-29', '0215', 'https://www.youtube.com/watch?v=gIEbix3m68g', 3, 1, '[7]', '{"7": {"timestamps":[{"start":"0000","end":"0215"}],"comment":null}}', '{"5":null}');
CALL usp_InsertRip('My Room (Naturale) - Hatsune Miku: Project Mirai DX', NULL, NULL, '2016-01-31', '0308', 'https://www.youtube.com/watch?v=yS80Lx9d6ug', 9, 1, '[2]', '{"19": {"timestamps":[{"start":"0000","end":"0308"}],"comment":null}}', '{"7":null}');
CALL usp_InsertRip('Mini-Porky''s Entrance - MOTHER 3', NULL, NULL, '2020-05-01', '0007', 'https://www.youtube.com/watch?v=34frOL0BvlM', 10, 2, '[6]', '{"2": {"timestamps":[{"start":"0000","end":"0007"}],"comment":null}}', '{}');
CALL usp_InsertRip('Triage at Dawn - Half-Life 2', 'Pledge of Combine', NULL, '2020-05-01', '0048', 'https://www.youtube.com/watch?v=KYhfEHui8-w', 11, 2, '[6]', '{"2": {"timestamps":[{"start":"0006","end":"0048"}],"comment":null}}', '{"8":null}');
CALL usp_InsertRip('Final Boss - Drawn to Life', NULL, NULL, '2020-05-01', '0245', 'https://www.youtube.com/watch?v=SazKKPNJ1mw', 12, 2, '[2,6]', '{"21": {"timestamps":[{"start":"0000","end":"0011"},{"start":"0057","end":"0110"}],"comment":null},"22": {"timestamps":[{"start":"0011","end":"0028"}],"comment":null},"23": {"timestamps":[{"start":"0011","end":"0028"},{"start":"0057","end":"0120"}],"comment":null},"25": {"timestamps":[{"start":"0028","end":"0040"}],"comment":null},"2": {"timestamps":[{"start":"0040","end":"0051"}],"comment":null},"26": {"timestamps":[{"start":"0051","end":"0057"}],"comment":null}}', '{}');
CALL usp_InsertRip('Password - Mega Man 7', 'SiIvagunner Gets Fucking HACKED (Real)', NULL, '2020-05-01', '0024', 'https://www.youtube.com/watch?v=_4eCEPjKP14', 13, 2, '[14]', '{"30": {"timestamps":[{"start":"0000","end":"0024"}],"comment":null},"31": {"timestamps":[{"start":"009","end":"0016"}],"comment":null}}', '{"9":null}');
CALL usp_InsertRip('AUDlO_lNTRONOlSE (Beta Mix) - Deltarune', NULL, NULL, '2020-05-01', '0012', 'https://www.youtube.com/watch?v=sUNlS5Olh5c', 14, 2, '[1]', '{"32": {"timestamps":[{"start":"0000","end":"0012"}],"comment":null}}', '{"10":null, "11":null}');
CALL usp_InsertRip('Slider (DN Version) - Super Mario 64', NULL, NULL, '2020-05-01', '0337', 'https://www.youtube.com/watch?v=zvSnueCaCjg', 15, 2, '[2,13,14]', '{"34": {"timestamps":[{"start":"0000","end":"0333"}],"comment":null},"33": {"timestamps":[{"start":"0333","end":"0337"}],"comment":null},"35": {"timestamps":[{"start":"0112","end":"0135"}],"comment":"Vocals are used in place of those from Deez Nuts! [Trap Remix]."},"36": {"timestamps":[{"start":"0228","end":"0333"}],"comment":null},"37": {"timestamps":[{"start":"0311","end":"0333"}],"comment":"Pitch shifted"}}', '{"12":null}');
CALL usp_InsertRip('White - Cave Story', NULL, NULL, '2020-05-01', '0325', 'https://www.youtube.com/watch?v=6s4nh5cpk_0', 16, 2, '[6]', '{"38": {"timestamps":[{"start":"0000","end":"0325"}],"comment":null}}', '{"8":null}');
