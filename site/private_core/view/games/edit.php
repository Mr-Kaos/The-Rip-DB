<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Edit Game</h1>
	<p>Update an existing game's record here.</p>
	<form id="edit-game" method="POST">
		<fieldset>
			<legend>Game Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true, 'value' => $game['GameName']], null, true))->buildElement() ?>
			<?= (new o\InputElement('Game Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 10000, 'value' => $game['GameDescription']], null, true))->buildElement() ?>
			<?= (new o\InputElement('Fake Game', o\InputTypes::checkbox, ['name' => 'isFake', 'checked' => $game['IsFake'] == 1, 'title' => 'Check this box if the game is not a real game and is made up for use in rips.']))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Game</button>
	</form>
</main>