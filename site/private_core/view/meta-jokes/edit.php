<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');
?>
<main>
	<h1>Edit Meta Joke</h1>
	<form id="edit-meta-joke" method="POST">
		<fieldset>
			<legend>Meta Joke Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true, 'value' => $metaJoke['MetaJokeName']], null, true))->buildElement() ?>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'required' => true, 'maxlength' => 9999, 'value' => $metaJoke['MetaJokeDescription']], null, true))->buildElement() ?>
			<?= (new o\SearchElement('Associated Meta', '/search/metas', false, $metaJoke['Meta'], ['name' => 'meta', 'required' => true]))->buildElement(); ?>
		</fieldset>
		<button type="submit">Update Meta Joke</button>
	</form>
</main>