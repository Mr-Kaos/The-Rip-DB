<?php

/**
 * This script updates the database schema from version 0.5.0 -> 0.5.1
 */

require_once(__DIR__ . '/../deployer.php');

$pdo = new PDO('mysql:host=' . constant('SQL_HOST') . ';dbname=' . constant('SQL_DB') . ';charset=UTF8', constant('SQL_USER'), constant('SQL_PASS'));

$in = readline('Updating database "' . constant('SQL_DB') . '" on "' . constant('SQL_HOST') . '" from v0.5.0 -> v0.5.1 . Is this OK? [Y or Enter to continue. N to cancel]');

$in = strtoupper($in);
if ($in == 'Y' || $in == '') {
	if (!$pdo) {
		echo "Database connection failed! Please check the connection details in this file (deploy.php).";
		exit();
	}

	// ----------
	// Table Changes
	// ----------

	$pdo->exec("ALTER TABLE RipDB.JokeTags DROP FOREIGN KEY JokeTags_Joke_FK;");
	$pdo->exec("ALTER TABLE RipDB.JokeTags ADD CONSTRAINT JokeTags_Joke_FK FOREIGN KEY (JokeID) REFERENCES RipDB.Jokes(JokeID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.JokeTags DROP FOREIGN KEY JokeTags_Tag_FK;");
	$pdo->exec("ALTER TABLE RipDB.JokeTags ADD CONSTRAINT JokeTags_Tag_FK FOREIGN KEY (TagID) REFERENCES RipDB.Tags(TagID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.MetaJokes DROP FOREIGN KEY MetaJokes_Tag_FK;");
	$pdo->exec("ALTER TABLE RipDB.MetaJokes ADD CONSTRAINT MetaJokes_Tag_FK FOREIGN KEY (MetaID) REFERENCES RipDB.Metas(MetaID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.JokeMetas DROP FOREIGN KEY JokeMetas_Meta_FK;");
	$pdo->exec("ALTER TABLE RipDB.JokeMetas ADD CONSTRAINT JokeMetas_Meta_FK FOREIGN KEY (MetaJokeID) REFERENCES RipDB.MetaJokes(MetaJokeID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.JokeMetas DROP FOREIGN KEY JokeMetas_Joke_FK;");
	$pdo->exec("ALTER TABLE RipDB.JokeMetas ADD CONSTRAINT JokeMetas_Joke_FK FOREIGN KEY (JokeId) REFERENCES RipDB.Jokes(JokeID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.AlternateJokeNames DROP FOREIGN KEY AlternateJokeNames_Jokes_FK;");
	$pdo->exec("ALTER TABLE RipDB.AlternateJokeNames ADD CONSTRAINT AlternateJokeNames_Jokes_FK FOREIGN KEY (JokeID) REFERENCES RipDB.Jokes(JokeID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.GamePlatforms DROP FOREIGN KEY GamePlatforms_Game_FK;");
	$pdo->exec("ALTER TABLE RipDB.GamePlatforms ADD CONSTRAINT GamePlatforms_Game_FK FOREIGN KEY (GameID) REFERENCES RipDB.Games(GameID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.GamePlatforms DROP FOREIGN KEY GamePlatforms_Platform_FK;");
	$pdo->exec("ALTER TABLE RipDB.GamePlatforms ADD CONSTRAINT GamePlatforms_Platform_FK FOREIGN KEY (PlatformID) REFERENCES RipDB.Platforms(PlatformID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.RipComposers DROP FOREIGN KEY RipComposers_Rip_FK;");
	$pdo->exec("ALTER TABLE RipDB.RipComposers ADD CONSTRAINT RipComposers_Rip_FK FOREIGN KEY (ComposerID) REFERENCES RipDB.Composers(ComposerID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.RipComposers DROP FOREIGN KEY RipComposers_Composer_FK;");
	$pdo->exec("ALTER TABLE RipDB.RipComposers ADD CONSTRAINT RipComposers_Composer_FK FOREIGN KEY (RipID) REFERENCES RipDB.Rips(RipID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.RipJokes DROP FOREIGN KEY RipJokes_Genres_FK;");
	$pdo->exec("ALTER TABLE RipDB.RipJokes ADD CONSTRAINT RipJokes_Genres_FK FOREIGN KEY (GenreID) REFERENCES RipDB.Genres(GenreID) ON DELETE SET NULL ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.RipJokes DROP FOREIGN KEY RipJokes_Jokes_FK;");
	$pdo->exec("ALTER TABLE RipDB.RipJokes ADD CONSTRAINT RipJokes_Jokes_FK FOREIGN KEY (JokeID) REFERENCES RipDB.Jokes(JokeID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.RipJokes DROP FOREIGN KEY RipJokes_Rips_FK;");
	$pdo->exec("ALTER TABLE RipDB.RipJokes ADD CONSTRAINT RipJokes_Rips_FK FOREIGN KEY (RipID) REFERENCES RipDB.Rips(RipID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.RipRippers DROP FOREIGN KEY RipID_FK;");
	$pdo->exec("ALTER TABLE RipDB.RipRippers ADD CONSTRAINT RipID_FK FOREIGN KEY (RipID) REFERENCES RipDB.Rips(RipID) ON DELETE CASCADE ON UPDATE RESTRICT;");
	$pdo->exec("ALTER TABLE RipDB.RipRippers DROP FOREIGN KEY RipperId_FK;");
	$pdo->exec("ALTER TABLE RipDB.RipRippers ADD CONSTRAINT RipperId_FK FOREIGN KEY (RipperId) REFERENCES RipDB.Rippers(RipperID) ON DELETE CASCADE ON UPDATE RESTRICT;");

	// Update all views, procedures and triggers.
	require_once(__DIR__ . '/../update.php');
}
