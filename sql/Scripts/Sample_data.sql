INSERT INTO Tags
	(TagName)
VALUES
	('Meme'),
	('Song'),
	('Video Game'),
	('Rip'),
	('Person'),
	('Event'),
	('Video'),
	('Location'),
	('Character'),
	('Music Artist'),
	('TV Show');
	
INSERT INTO Jokes
	(JokeName, JokeDescription)
VALUES
	('Grand Dad', 'Funny 7'),
	('Loud Nigra', ''),
	('Gegadegigedagedago', ''),
	('Maroon 5', ''),
	('Inspector Gadget', '');

INSERT INTO JokeTags
	(JokeID, TagID)
VALUES
	(1, 1), (1, 3), (1, 9),
	(2, 1), (2, 5), (2, 7),
	(3, 1), (3, 7), (3, 9),
	(4, 5), (4, 10),
	(5, 9), (5, 11);
