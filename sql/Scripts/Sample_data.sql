-- This is a simple script that just inserts a bunch of sample data into the database.

-- Meta Jokes
-- The IDs here will start at 15 due to the meta jokes defined in the basic data.
-- Params: Meta Joke name, Description, Meta ID, Output (always 0 for examples)
CALL usp_InsertMetaJoke('The Flintstones', 'An animated sitcom produced by Hanna-Barbera Productions in the 60s.', 3, @out); -- 15
CALL usp_InsertMetaJoke('Bootlegs', 'Unofficial black-market video games that often use licensed intellectual property in their games.', 2, @out);
CALL usp_InsertMetaJoke('Pokémon', 'Japanese media franchise about creatures with special powers that co-exist with humans.', 2, @out);
CALL usp_InsertMetaJoke('Undertale', 'RPG video game by Toby Fox.', 2, @out);
CALL usp_InsertMetaJoke('K-Pop', 'Genre of music where music in this genre originates from South Korea.', 1, @out);
CALL usp_InsertMetaJoke('Psy', 'K-Pop artist most well known for his song "Gangnam Style".', 6, @out); -- 20
CALL usp_InsertMetaJoke('Star Wars', 'A sci-fi media franchise created by George Lucas.', 7, @out);
CALL usp_InsertMetaJoke('Inspector Gadget', '80s kids cartoon television series.', 3, @out);
CALL usp_InsertMetaJoke('Smash Mouth', 'Music artist.', 6, @out);
CALL usp_InsertMetaJoke('Disney', 'American multimedia company.', 8, @out);
CALL usp_InsertMetaJoke('Love Live!', 'Anime series', 5, @out); -- 25
CALL usp_InsertMetaJoke('Green Day', 'Music artist', 6, @out);
CALL usp_InsertMetaJoke('Yakuza', 'Video game franchise', 2, @out);
CALL usp_InsertMetaJoke('Touhou', 'Bullet hell video game series', 2, @out);
CALL usp_InsertMetaJoke('Eurobeat', 'Genre of music', 1, @out);
CALL usp_InsertMetaJoke('Super Mario', 'Video game franchise', 2, @out); -- 30
CALL usp_InsertMetaJoke('Beatles', 'Music band', 6, @out);
CALL usp_InsertMetaJoke('Backstreet Boys', 'Music band', 6, @out);
CALL usp_InsertMetaJoke('Vargskelethor Joel', 'Streamer part of the Vinesauce group.', 9, @out);
CALL usp_InsertMetaJoke('Toby Fox', 'Music Composer and video game developer, best known for his work on the game "Undertale".', 6, @out);

-- Jokes
-- Params: Joke Name, Description, Primary Tag ID, Tag IDs, Meta Joke IDs
CALL usp_InsertJoke('Grand Dad', 'Funny bootleg Flintstones game popularised by into a meme by Vargskelethor Joel.', 3, '[5, 4]', '[15, 33]', @out);
CALL usp_InsertJoke('Meet the Flintstones', 'Main theme of the cartoon series "The Flintstones"', 2, null, '[15]', @out);
CALL usp_InsertJoke('Gangam Style', 'Hit K-Pop song from 2012 by Psy', 1, null, '[14,18,34]', @out);
CALL usp_InsertJoke('Once Upon a Time', 'Song from Undertale.', 1, null, '[14,18,34]', @out);
CALL usp_InsertJoke('The Final Countdown', 'Song by Europe.', 1, null, '[3]', @out); -- 5
CALL usp_InsertJoke('Megalovania', 'Song from the game Undertale.', 1, null, '[14,18,34]', @out);
CALL usp_InsertJoke('Inspector Gadget', 'Theme song of the TV show "Inspector Gadget".', 2, '[4]', '[22]', @out);
CALL usp_InsertJoke('Star Wars (Main Title)', 'Main title theme of Star Wars by John Williams.', 1, null, '[21,24]', @out);
CALL usp_InsertJoke('All Star', 'Song by music artist "Smash Mouth".', 1, null, '[23]', @out);
CALL usp_InsertJoke('Bonetrousle', 'Song from the game Undertale.', 1, null, '[14,18,34]', @out); -- 10
CALL usp_InsertJoke('On The Floor', 'Song by IceJJFish.', 1, null, '[6]', @out);
CALL usp_InsertJoke('Donald Duck', 'Disney character', 4, null, '[24]', @out);
CALL usp_InsertJoke('Temmie Village', 'Song from the game Undertale.', 1, null, '[14,18,34]', @out);
CALL usp_InsertJoke('Snow halation', 'Song from the Love Live! franchise.', 1, null, '[25]', @out);
CALL usp_InsertJoke('YTP4LIFE CRYING', '', 3, null, null, @out); -- 15
CALL usp_InsertJoke('Title Theme & Ending', 'The NES rendition of "Meet the Flintstones" that plays during the title screen and ending of The Flintstones: The Rescue of Dino & Hoppy. The name is unofficial.', 1, '[2]', '[14,15]', @out);
CALL usp_InsertJoke('Wake Me Up When September Ends', 'Song by Green Day', 1, null, '[26,5]', @out);
CALL usp_InsertJoke('Pledge of Demon', 'Song from Yakuza 0', 1, null, '[14]', @out);
CALL usp_InsertJoke('Rock My Emotions', '', 1, '[3]', null, @out);
CALL usp_InsertJoke('Futatsuiwa from Sado', '', 1, null, '[14]', @out); -- 20
CALL usp_InsertJoke('Bad Apple!! feat.nomico', '', 1, null, '[14]', @out);
CALL usp_InsertJoke('Bad Apple', '', 1, null, '[14]', @out);
CALL usp_InsertJoke('U.N. Owen Was Her?', '', 1, null, '[14]', @out);
CALL usp_InsertJoke('Running in the 90s', '', 1, null, '[29]', @out);
CALL usp_InsertJoke('Tetris - Type A', '', 1, null, null, @out); -- 25
CALL usp_InsertJoke('Beware the Forest''s Mushrooms', '', 1, null, '[14]', @out);
CALL usp_InsertJoke('In the Hall of the Mountain King', '', 1, null, '[11]', @out);
CALL usp_InsertJoke('OMNI FIX YOUR PASSWORD', '', 6, null, null, @out);
CALL usp_InsertJoke('Temporary Secretary', '', 1, null, '[31]', @out);
CALL usp_InsertJoke('Deez Nuts!', '', 3, '[6]', null, @out); -- 30
CALL usp_InsertJoke('Deez Nuts! [Trap Remix]', '', 1, '[3]', null, @out);
CALL usp_InsertJoke('Bonfire', '', 1, null, null, @out);
CALL usp_InsertJoke('Harlem Shake', '', 1, '[3,6]', null, @out);
CALL usp_InsertJoke('Ore Ida Pizza Bagel Bites', '', 8, null, null, @out);
CALL usp_InsertJoke('I Want It That Way', '', 1, null, '[32,4]', @out); -- 35

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
	('Drawn to Life', ''),
	('Mega Man 7', ''),
	('Deltarune', ''),
	('Super Mario 64', ''), -- 15
	('Cave Story', '');

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
-- Parameters: Rip Name, Alt Name, Description, upload date, length, URL, YouTube ID, alternate (album) URL, game, channel, genres, jokes, rippers 
CALL usp_InsertRip('Battle! (Wild Pokémon)', 'A Wild Fred Flintstone Appeared!', null, '2016-01-09', '0102', 'https://www.youtube.com/watch?v=vJsjd8alc8Y', 'vJsjd8alc8Y', null, 1, 1, '[1]',
 '{"2": {"timestamps":[{"start":"00:00:12","end":"00:01:01"}],"comment":null}, "1": {"timestamps":[{"start":"00:00:12","end":"00:01:02"}],"comment":"Visual edit"}, "3": {"timestamps":[{"start":"00:01:01","end":"00:01:02"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Route 110', null, null, '2016-06-09', '0031', 'https://www.youtube.com/watch?v=hRKTKaOtP0I', 'hRKTKaOtP0I', null, 1, 1, '[1]',
 '{"4": {"timestamps":[{"start":"00:00:00","end":"00:00:31"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Gerudo Valley', 'Gerudo Countdown', null, '2016-01-11', '0146', 'https://www.youtube.com/watch?v=zdFPVzFgl68', 'zdFPVzFgl68', null, 2, 1, '[1]',
 '{"5": {"timestamps":[{"start":"00:00:00","end":"00:01:46"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('MEGALOVANIA', 'Descending MEGALOVANIA', null, '2016-01-12', '0137', 'https://www.youtube.com/watch?v=Q9wDLSrLeUE', 'Q9wDLSrLeUE', null, 3, 1, '[7]',
 '{"6": {"timestamps":[{"start":"00:00:00","end":"00:01:37"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Once Upon A Time', null, null, '2016-01-13', '0127', 'https://www.youtube.com/watch?v=2_yoDiuwSwE', '2_yoDiuwSwE', null, 3, 1, '[1]',
 '{"8": {"timestamps":[{"start":"00:00:00","end":"00:01:27"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Lost Woods', null, null, '2016-01-14', '0106', 'https://www.youtube.com/watch?v=da5kSUVbaI4', 'da5kSUVbaI4', null, 2, 1, '[1]',
 '{"2": {"timestamps":[{"start":"00:00:02","end":"00:01:06"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('A Secret Course', null, null, '2016-01-14', '0131', 'https://www.youtube.com/watch?v=ZTga1rjryhE', 'ZTga1rjryhE', null, 3, 1, '[1]',
 '{"9": {"timestamps":[{"start":"00:00:07","end":"00:01:31"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Secret Course', 'Super Mario Shrekshine: All Secret Course', null, '2016-01-15', '0130', 'https://www.youtube.com/watch?v=ryZvC68xE_s', 'ryZvC68xE_s', null, 4, 1, '[1]',
 '{"9": {"timestamps":[{"start":"00:00:06","end":"00:01:30"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Luigi''s Mansion', null, null, '2016-01-17', '0131', 'https://www.youtube.com/watch?v=p7RsftFX9ak', 'p7RsftFX9ak', null, 5, 1, '[1]',
 '{"10": {"timestamps":[{"start":"00:00:00","end":"00:01:31"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Uncontrollable', null, null, '2016-01-18', '0349', 'https://www.youtube.com/watch?v=pTeXKobmqWk', 'pTeXKobmqWk', null, 6, 1, '[1]',
 '{"3": {"timestamps":[{"start":"00:00:28","end":"00:03:49"}],"comment":null}}', '{"3":"null"}');
CALL usp_InsertRip('Map (Day)', 'On the Island', null, '2016-01-18', '0053', 'https://www.youtube.com/watch?v=XhG9rWtjcGQ', 'XhG9rWtjcGQ', null, 7, 1, '[1]',
 '{"11": {"timestamps":[{"start":"00:00:00","end":"00:00:53"}],"comment":null}}', '{"4":"Chief Keef 2"}');
CALL usp_InsertRip('Uncontrollable (Alternate Mix)', null, null, '2016-01-19', '0345', 'https://www.youtube.com/watch?v=6nRC_dlsJ1I', '6nRC_dlsJ1I', null, 6, 1, '[1]',
 '{"12": {"timestamps":[{"start":"00:00:00","end":"00:03:45"}],"comment":null}}', '{"5":null}');
CALL usp_InsertRip('Overworld Theme (Original Mix)', null, null, '2016-01-19', '0128', 'https://www.youtube.com/watch?v=Ct3Z7LEoOPM', 'Ct3Z7LEoOPM', null, 8, 1, '[1]',
 '{"13": {"timestamps":[{"start":"00:00:12","end":"00:01:28"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Hopes and Dreams', null, null, '2016-01-20', '0301', 'https://www.youtube.com/watch?v=Bhs3Q7-kLHs', 'Bhs3Q7-kLHs', null, 3, 1, '[1]',
 '{"14": {"timestamps":[{"start":"00:00:11","end":"00:03:01"}],"comment":null}}', '{"6":null}');
CALL usp_InsertRip('The key we''ve lost', 'The channel ytp4life lost', null, '2016-01-20', '0611', 'https://www.youtube.com/watch?v=SezWmzgp6uQ', 'SezWmzgp6uQ', null, 6, 1, '[1]',
 '{"15": {"timestamps":[{"start":"00:00:00","end":"00:06:11"}],"comment":null}}', '{"3":null}');
CALL usp_InsertRip('MEGALOVANIA (Beta Mix)', 'Grand Dadlovania', null, '2016-01-28', '0049', 'https://www.youtube.com/watch?v=4wXW_ex5Nvs', '4wXW_ex5Nvs', null, 3, 1, '[1]',
 '{"6": {"timestamps":[{"start":"00:00:07","end":"00:00:39"}],"comment":null}}', '{"1":null}');
CALL usp_InsertRip('Last Goodbye (Alternate Mix)', 'Everlasting Goodbye', null, '2016-01-29', '0049', 'https://www.youtube.com/watch?v=rSuYr0dR2gw', 'rSuYr0dR2gw', null, 3, 1, '[2]',
 '{"16": {"timestamps":[{"start":"00:00:05","end":"00:00:49"}],"comment":null}}', '{"2":null}');
CALL usp_InsertRip('Last Goodbye (Beta Mix)', null, null, '2016-01-29', '0215', 'https://www.youtube.com/watch?v=gIEbix3m68g', 'gIEbix3m68g', null, 3, 1, '[7]',
 '{"7": {"timestamps":[{"start":"00:00:00","end":"00:02:15"}],"comment":null}}', '{"5":null}');
CALL usp_InsertRip('My Room (Naturale)', null, null, '2016-01-31', '0308', 'https://www.youtube.com/watch?v=yS80Lx9d6ug', 'yS80Lx9d6ug', null, 9, 1, '[2]',
 '{"17": {"timestamps":[{"start":"00:00:00","end":"00:03:08"}],"comment":null}}', '{"7":null}');
CALL usp_InsertRip('Mini-Porky''s Entrance', null, null, '2020-05-01', '0007', 'https://www.youtube.com/watch?v=34frOL0BvlM', '34frOL0BvlM', null, 10, 2, '[6]',
 '{"2": {"timestamps":[{"start":"00:00:00","end":"00:00:07"}],"comment":null}}', '{}');
CALL usp_InsertRip('Triage at Dawn', 'Pledge of Combine', null, '2020-05-01', '0048', 'https://www.youtube.com/watch?v=KYhfEHui8-w', 'KYhfEHui8-w', null, 11, 2, '[6]',
 '{"2": {"timestamps":[{"start":"00:00:06","end":"00:00:48"}],"comment":null}}', '{"8":null}');
CALL usp_InsertRip('Final Boss', null, null, '2020-05-01', '0245', 'https://www.youtube.com/watch?v=SazKKPNJ1mw', 'SazKKPNJ1mw', null, 12, 2, '[2,6]',
 '{"19": {"timestamps":[{"start":"00:00:00","end":"00:00:11"},{"start":"00:00:57","end":"00:01:10"}],"comment":null},"20": {"timestamps":[{"start":"00:00:11","end":"00:00:28"}],"comment":null},"21": {"timestamps":[{"start":"00:00:11","end":"00:00:28"},{"start":"00:00:57","end":"00:01:20"}],"comment":null},"23": {"timestamps":[{"start":"00:00:28","end":"00:00:40"}],"comment":null},"2": {"timestamps":[{"start":"00:00:40","end":"00:00:51"}],"comment":null},"24": {"timestamps":[{"start":"00:00:51","end":"00:00:57"}],"comment":null}}', '{}');
CALL usp_InsertRip('Password', 'SiIvagunner Gets Fucking HACKED (Real)', null, '2020-05-01', '0024', 'https://www.youtube.com/watch?v=_4eCEPjKP14', '_4eCEPjKP14', null, 13, 2, '[14]',
 '{"28": {"timestamps":[{"start":"00:00:00","end":"00:00:24"}],"comment":null},"29": {"timestamps":[{"start":"00:00:09","end":"00:00:16"}],"comment":null}}', '{"9":null}');
CALL usp_InsertRip('AUDlO_lNTRONOlSE (Beta Mix)', null, null, '2020-05-01', '0012', 'https://www.youtube.com/watch?v=sUNlS5Olh5c', 'sUNlS5Olh5c', null, 14, 2, '[1]',
 '{"30": {"timestamps":[{"start":"00:00:00","end":"00:00:12"}],"comment":null}}', '{"10":null, "11":null}');
CALL usp_InsertRip('Slider (DN Version)', null, null, '2020-05-01', '0337', 'https://www.youtube.com/watch?v=zvSnueCaCjg', 'zvSnueCaCjg', null, 15, 2, '[2,13,14]',
 '{"29": {"timestamps":[{"start":"00:00:00","end":"00:03:33"}],"comment":null},"30": {"timestamps":[{"start":"00:03:33","end":"00:03:37"}],"comment":null},"32": {"timestamps":[{"start":"00:01:12","end":"00:01:35"}],"comment":"Vocals are used in place of those from Deez Nuts! [Trap Remix]."},"33": {"timestamps":[{"start":"00:02:28","end":"00:03:33"}],"comment":null},"34": {"timestamps":[{"start":"00:03:11","end":"00:03:33"}],"comment":"Pitch shifted"}}', '{"12":null}');
CALL usp_InsertRip('White', null, null, '2020-05-01', '0325', 'https://www.youtube.com/watch?v=6s4nh5cpk_0', '6s4nh5cpk_0', null, 16, 2, '[6]',
 '{"35": {"timestamps":[{"start":"00:00:00","end":"00:03:25"}],"comment":null}}', '{"8":null}');
