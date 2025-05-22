-- Meta Jokes
CALL usp_InsertMetaJoke_SAMPLE('The Flintstones', 'An animated sitcom produced by Hanna-Barbera Productions in the 60s.', 'Animated Series'); -- 11
CALL usp_InsertMetaJoke_SAMPLE('Bootlegs', 'Unofficial black-market video games that often use licensed intellectual property in their games.', 'Video Game');
CALL usp_InsertMetaJoke_SAMPLE('Pokémon', 'Japanese media franchise about creatures with special powers that co-exist with humans.', 'Franchise');
CALL usp_InsertMetaJoke_SAMPLE('Undertale', 'RPG video game by Toby Fox.', 'Video Game');
CALL usp_InsertMetaJoke_SAMPLE('K-Pop', 'Genre of music where music in this genre originates from South Korea.', 'Music'); -- 15
CALL usp_InsertMetaJoke_SAMPLE('Psy', 'K-Pop artist most well known for his song "Gangnam Style".', 'Artist (Music)');
CALL usp_InsertMetaJoke_SAMPLE('Star Wars', 'A sci-fi media franchise created by George Lucas.', 'Franchise');
CALL usp_InsertMetaJoke_SAMPLE('Inspector Gadget', '80s kids cartoon television series.', 'Animated Series');
CALL usp_InsertMetaJoke_SAMPLE('Smash Mouth', 'Music artist.', 'Artist (Music)');
CALL usp_InsertMetaJoke_SAMPLE('Disney', 'American multimedia company.', 'Company'); -- 20
CALL usp_InsertMetaJoke_SAMPLE('Love Live!', 'Anime series', 'Anime');
CALL usp_InsertMetaJoke_SAMPLE('Green Day', 'Music artist', 'Artist (Music)');
CALL usp_InsertMetaJoke_SAMPLE('Yakuza', 'Video game franchise', 'Video Game');
CALL usp_InsertMetaJoke_SAMPLE('Touhou', 'Bullet hell video game series', 'Video Game');
CALL usp_InsertMetaJoke_SAMPLE('Eurobeat', 'Genre of music', 'Music'); -- 25
CALL usp_InsertMetaJoke_SAMPLE('Super Mario', 'Video game franchise', 'Video Game');
CALL usp_InsertMetaJoke_SAMPLE('Classical', 'Musical genre', 'Music');
CALL usp_InsertMetaJoke_SAMPLE('Beatles', 'Music band', 'Artist (Music)');
CALL usp_InsertMetaJoke_SAMPLE('Backstreet Boys', 'Music band', 'Artist (Music)');

-- Jokes
CALL usp_InsertJoke_SAMPLE('Grand Dad', 'Funny bootleg Flintstones game popularised by into a meme by Vargskelethor Joel.', 'Meme', '["Voice line"]', '[11,12]');
CALL usp_InsertJoke_SAMPLE('Meet the Flintstones', 'Main theme of the cartoon series "The Flintstones"', 'Theme Song', NULL, '[11]');
CALL usp_InsertJoke_SAMPLE('Gangam Style', 'Hit K-Pop song from 2012 by Psy', 'Song', NULL, '[15,16]');
CALL usp_InsertJoke_SAMPLE('Once Upon a Time', 'Song from Undertale.', 'Song', NULL, '[14,10]');
CALL usp_InsertJoke_SAMPLE('The Final Countdown', 'Song by Europe.', 'Song', NULL, '[1]'); -- 5
CALL usp_InsertJoke_SAMPLE('Megalovania', 'Song from the game Undertale.', 'Song', NULL, '[14,10]');
CALL usp_InsertJoke_SAMPLE('Blue balls', 'What you expect is about to happen doesn''t happen... for an extended time.', 'Meme', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Maroon 5', 'Pop artist', 'Artist', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Inspector Gadget', 'Theme song of the TV show "Inspector Gadget".', 'Theme Song', '["Cartoon", "TV Show"]', '[18]');
CALL usp_InsertJoke_SAMPLE('Star Wars (Main Title)', 'Main title theme of Star Wars by John Williams.', 'Song', null, '[17,8]'); -- 10
CALL usp_InsertJoke_SAMPLE('All Star', 'Song by music artist "Smash Mouth".', 'Song', null, '[19]');
CALL usp_InsertJoke_SAMPLE('Bonetrousle', 'Song from the game Undertale.', 'Song', NULL, '[14]');
CALL usp_InsertJoke_SAMPLE('On The Floor', 'Song by IceJJFish.', 'Song', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Donald Duck', 'Disney character', 'Character', NULL, '[20]');
CALL usp_InsertJoke_SAMPLE('Temmie Village', 'Song from the game Undertale.', 'Song', NULL, '[14]'); -- 15
CALL usp_InsertJoke_SAMPLE('Snow halation', 'Song from the Love Live! franchise.', 'Song', null, '[21]');
CALL usp_InsertJoke_SAMPLE('YTP4LIFE CRYING', '', 'Meme', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Title Theme & Ending', 'The NES rendition of "Meet the Flintstones" that plays during the title screen and ending of The Flintstones: The Rescue of Dino & Hoppy. The name is unofficial.', 'Song', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Wake Me Up When September Ends', 'Song by Green Day', 'Song', NULL, '[22]');
CALL usp_InsertJoke_SAMPLE('Pledge of Demon', 'Song from Yakuza 0', 'Song', NULL, '[23]'); -- 20
CALL usp_InsertJoke_SAMPLE('Rock My Emotions', '', 'Song', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Futatsuiwa from Sado', '', 'Song', NULL, '[24]');
CALL usp_InsertJoke_SAMPLE('Bad Apple!! feat.nomico', '', 'Song', NULL, '[24]');
CALL usp_InsertJoke_SAMPLE('Bad Apple', '', 'Song', NULL, '[24]');
CALL usp_InsertJoke_SAMPLE('U.N. Owen Was Her?', '', 'Song', NULL, '[24]'); -- 25
CALL usp_InsertJoke_SAMPLE('Running in the 90s', '', 'Song', NULL, '[25]');
CALL usp_InsertJoke_SAMPLE('Tetris - Type A', '', 'Song', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Beware the Forest''s Mushrooms', '', 'Song', NULL, '[26]');
CALL usp_InsertJoke_SAMPLE('In the Hall of the Mountain King', '', 'Song', NULL, '[27]');
CALL usp_InsertJoke_SAMPLE('OMNI FIX YOUR PASSWORD', '', 'Video', NULL, NULL); -- 30
CALL usp_InsertJoke_SAMPLE('Temporary Secretary', '', 'Song', NULL, '[28]');
CALL usp_InsertJoke_SAMPLE('We are Leo', '', 'Band', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Deez Nuts!', '', 'Meme', NULL, NULL);
CALL usp_InsertJoke_SAMPLE('Deez Nuts! [Trap Remix]', '', 'Song', '["Meme"]', NULL);
CALL usp_InsertJoke_SAMPLE('Bonfire', '', 'Song', NULL, NULL); -- 35
CALL usp_InsertJoke_SAMPLE('Harlem Shake', '', 'Song', '["Meme"]', NULL);
CALL usp_InsertJoke_SAMPLE('Ore Ida Pizza Bagel Bites', '', 'Commercial', '["YTP"]', NULL);
CALL usp_InsertJoke_SAMPLE('I Want It That Way', '', 'Song', NULL, '[29]');

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
	(RipperName)
VALUES
	('Chaze the Chat'),
	('MtH'),
	('dante'),
	('toonlink'),
	('Albert Softie'), -- 5
	('Sir Spacebar'),
	('Cryptrik'),
	('eg_9371'),
	('l4ureleye'),
	('Jp'), -- 10
	('Krizis'),
	('Spicy236');

-- Rips
CALL usp_InsertRip('Battle! (Wild Pokémon) - Pokémon Ruby & Sapphire', 'A Wild Fred Flintstone Appeared!', NULL, '2016-01-09', '0102', 'https://www.youtube.com/watch?v=vJsjd8alc8Y', 'vJsjd8alc8Y', NULL, 1, 1, '[1]', '{"2": {"timestamps":[{"start":"0012","end":"0101"}],"comment":null}, "1": {"timestamps":[{"start":"0012","end":"0102"}],"comment":"Visual edit"}, "3": {"timestamps":[{"start":"0101","end":"0102"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Route 110 - Pokémon Ruby & Sapphire', NULL, NULL, '2016-06-09', '0031', 'https://www.youtube.com/watch?v=hRKTKaOtP0I', 'hRKTKaOtP0I', NULL, 1, 1, '[1]', '{"4": {"timestamps":[{"start":"0000","end":"0031"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Gerudo Valley - The Legend of Zelda: Ocarina of Time', 'Gerudo Countdown', NULL, '2016-01-11', '0146', 'https://www.youtube.com/watch?v=zdFPVzFgl68', 'zdFPVzFgl68', NULL, 2, 1, '[1]', '{"5": {"timestamps":[{"start":"0000","end":"0146"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('MEGALOVANIA - Undertale', 'Descending MEGALOVANIA', NULL, '2016-01-12', '0137', 'https://www.youtube.com/watch?v=Q9wDLSrLeUE', 'Q9wDLSrLeUE', NULL, 3, 1, '[7]', '{"6": {"timestamps":[{"start":"0000","end":"0137"}],"comment":null}, "7": {"timestamps":[{"start":"0000","end":"0137"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Once Upon A Time - Undertale', NULL, NULL, '2016-01-13', '0127', 'https://www.youtube.com/watch?v=2_yoDiuwSwE', '2_yoDiuwSwE', NULL, 3, 1, '[1]', '{"10": {"timestamps":[{"start":"0000","end":"0127"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Lost Woods - The Legend of Zelda: Ocarina of Time', NULL, NULL, '2016-01-14', '0106', 'https://www.youtube.com/watch?v=da5kSUVbaI4', 'da5kSUVbaI4', NULL, 2, 1, '[1]', '{"2": {"timestamps":[{"start":"0002","end":"0106"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('A Secret Course - Super Mario Sunshine', NULL, NULL, '2016-01-14', '0131', 'https://www.youtube.com/watch?v=ZTga1rjryhE', 'ZTga1rjryhE', NULL, 3, 1, '[1]', '{"11": {"timestamps":[{"start":"0007","end":"0131"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Secret Course - Super Mario Sunshine', 'Super Mario Shrekshine: All Secret Course', NULL, '2016-01-15', '0130', 'https://www.youtube.com/watch?v=ryZvC68xE_s', 'ryZvC68xE_s', NULL, 4, 1, '[1]', '{"11": {"timestamps":[{"start":"0006","end":"0130"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Luigi''s Mansion - Mario Kart DS', NULL, NULL, '2016-01-17', '0131', 'https://www.youtube.com/watch?v=p7RsftFX9ak', 'p7RsftFX9ak', NULL, 5, 1, '[1]', '{"12": {"timestamps":[{"start":"0000","end":"0131"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Uncontrollable - Xenoblade Chronicles X', NULL, NULL, '2016-01-18', '0349', 'https://www.youtube.com/watch?v=pTeXKobmqWk', 'pTeXKobmqWk', NULL, 6, 1, '[1]', '{"3": {"timestamps":[{"start":"0028","end":"0349"}],"comment":null}}', '{"3":"null"}');
CALL usp_InsertRip('Map (Day) - Tomodachi Life', 'On the Island', NULL, '2016-01-18', '0053', 'https://www.youtube.com/watch?v=XhG9rWtjcGQ', 'XhG9rWtjcGQ', NULL, 7, 1, '[1]', '{"13": {"timestamps":[{"start":"0000","end":"0053"}],"comment":null}}', '{"4":"Chief Keef 2"}');
CALL usp_InsertRip('Uncontrollable (Alternate Mix) - Xenoblade Chronicles X', NULL, NULL, '2016-01-19', '0345', 'https://www.youtube.com/watch?v=6nRC_dlsJ1I', '6nRC_dlsJ1I', NULL, 6, 1, '[1]', '{"14": {"timestamps":[{"start":"0000","end":"0345"}],"comment":null}}', '{"5":null}');
CALL usp_InsertRip('Overworld Theme (Original Mix) - New Super Mario Bros.', NULL, NULL, '2016-01-19', '0128', 'https://www.youtube.com/watch?v=Ct3Z7LEoOPM', 'Ct3Z7LEoOPM', NULL, 8, 1, '[1]', '{"15": {"timestamps":[{"start":"0012","end":"0128"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Hopes and Dreams - Undertale', NULL, NULL, '2016-01-20', '0301', 'https://www.youtube.com/watch?v=Bhs3Q7-kLHs', 'Bhs3Q7-kLHs', NULL, 3, 1, '[1]', '{"16": {"timestamps":[{"start":"0011","end":"0301"}],"comment":null}}', '{"6":null}');
CALL usp_InsertRip('The key we''ve lost - Xenoblade Chronicles X', 'The channel ytp4life lost', NULL, '2016-01-20', '0611', 'https://www.youtube.com/watch?v=SezWmzgp6uQ', 'SezWmzgp6uQ', NULL, 6, 1, '[1]', '{"17": {"timestamps":[{"start":"0000","end":"0611"}],"comment":null}}', '{"3":null}');
CALL usp_InsertRip('MEGALOVANIA (Beta Mix) - Undertale', 'Grand Dadlovania', NULL, '2016-01-28', '0049', 'https://www.youtube.com/watch?v=4wXW_ex5Nvs', '4wXW_ex5Nvs', NULL, 3, 1, '[1]', '{"2": {"timestamps":[{"start":"0000","end":"0049"}],"comment":null},"6": {"timestamps":[{"start":"0007","end":"0039"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Last Goodbye (Alternate Mix) - Undertale', 'Everlasting Goodbye', NULL, '2016-01-29', '0049', 'https://www.youtube.com/watch?v=rSuYr0dR2gw', 'rSuYr0dR2gw', NULL, 3, 1, '[2]', '{"18": {"timestamps":[{"start":"0005","end":"0049"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Last Goodbye (Beta Mix) - Undertale', NULL, NULL, '2016-01-29', '0215', 'https://www.youtube.com/watch?v=gIEbix3m68g', 'gIEbix3m68g', NULL, 3, 1, '[7]', '{"7": {"timestamps":[{"start":"0000","end":"0215"}],"comment":null}}', '{"5":null}');
CALL usp_InsertRip('My Room (Naturale) - Hatsune Miku: Project Mirai DX', NULL, NULL, '2016-01-31', '0308', 'https://www.youtube.com/watch?v=yS80Lx9d6ug', 'yS80Lx9d6ug', NULL, 9, 1, '[2]', '{"19": {"timestamps":[{"start":"0000","end":"0308"}],"comment":null}}', '{"7":null}');
CALL usp_InsertRip('Mini-Porky''s Entrance - MOTHER 3', NULL, NULL, '2020-05-01', '0007', 'https://www.youtube.com/watch?v=34frOL0BvlM', '34frOL0BvlM', NULL, 10, 2, '[6]', '{"2": {"timestamps":[{"start":"0000","end":"0007"}],"comment":null}}', '{}');
CALL usp_InsertRip('Triage at Dawn - Half-Life 2', 'Pledge of Combine', NULL, '2020-05-01', '0048', 'https://www.youtube.com/watch?v=KYhfEHui8-w', 'KYhfEHui8-w', NULL, 11, 2, '[6]', '{"2": {"timestamps":[{"start":"0006","end":"0048"}],"comment":null}}', '{"8":null}');
CALL usp_InsertRip('Final Boss - Drawn to Life', NULL, NULL, '2020-05-01', '0245', 'https://www.youtube.com/watch?v=SazKKPNJ1mw', 'SazKKPNJ1mw', NULL, 12, 2, '[2,6]', '{"21": {"timestamps":[{"start":"0000","end":"0011"},{"start":"0057","end":"0110"}],"comment":null},"22": {"timestamps":[{"start":"0011","end":"0028"}],"comment":null},"23": {"timestamps":[{"start":"0011","end":"0028"},{"start":"0057","end":"0120"}],"comment":null},"25": {"timestamps":[{"start":"0028","end":"0040"}],"comment":null},"2": {"timestamps":[{"start":"0040","end":"0051"}],"comment":null},"26": {"timestamps":[{"start":"0051","end":"0057"}],"comment":null}}', '{}');
CALL usp_InsertRip('Password - Mega Man 7', 'SiIvagunner Gets Fucking HACKED (Real)', NULL, '2020-05-01', '0024', 'https://www.youtube.com/watch?v=_4eCEPjKP14', '_4eCEPjKP14', NULL, 13, 2, '[14]', '{"30": {"timestamps":[{"start":"0000","end":"0024"}],"comment":null},"31": {"timestamps":[{"start":"009","end":"0016"}],"comment":null}}', '{"9":null}');
CALL usp_InsertRip('AUDlO_lNTRONOlSE (Beta Mix) - Deltarune', NULL, NULL, '2020-05-01', '0012', 'https://www.youtube.com/watch?v=sUNlS5Olh5c', 'sUNlS5Olh5c', NULL, 14, 2, '[1]', '{"32": {"timestamps":[{"start":"0000","end":"0012"}],"comment":null}}', '{"10":null, "11":null}');
CALL usp_InsertRip('Slider (DN Version) - Super Mario 64', NULL, NULL, '2020-05-01', '0337', 'https://www.youtube.com/watch?v=zvSnueCaCjg', 'zvSnueCaCjg', NULL, 15, 2, '[2,13,14]', '{"34": {"timestamps":[{"start":"0000","end":"0333"}],"comment":null},"33": {"timestamps":[{"start":"0333","end":"0337"}],"comment":null},"35": {"timestamps":[{"start":"0112","end":"0135"}],"comment":"Vocals are used in place of those from Deez Nuts! [Trap Remix]."},"36": {"timestamps":[{"start":"0228","end":"0333"}],"comment":null},"37": {"timestamps":[{"start":"0311","end":"0333"}],"comment":"Pitch shifted"}}', '{"12":null}');
CALL usp_InsertRip('White - Cave Story', NULL, NULL, '2020-05-01', '0325', 'https://www.youtube.com/watch?v=6s4nh5cpk_0', '6s4nh5cpk_0', NULL, 16, 2, '[6]', '{"38": {"timestamps":[{"start":"0000","end":"0325"}],"comment":null}}', '{"8":null}');
