<?php

use RipDB\Objects as o;

?>
<main>
	<h1>Add A New Game</h1>
	<p>Fill in this form to add a game for use in rips.</p>
	<form id="new-game" method="POST">
		<fieldset>
			<legend>Game Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true], null, true))->buildElement() ?>
			<?= (new o\InputElement('Game Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 10000], null, true))->buildElement() ?>
			<?= (new o\InputElement('Fake Game', o\InputTypes::checkbox, ['name' => 'isFake', 'title' => 'Check this box if the game is not a real game and is made up for use in rips.']))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Game</button>
	</form>
</main>