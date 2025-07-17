<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');
?>
<main>
	<h1>Edit Joke</h1>
	<form id="new-joke" method="POST">
		<fieldset>
			<legend>Joke Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true, 'value' => $joke['JokeName']]))->buildElement() ?>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'required' => true, 'maxlength' => 9999, 'value' => $joke['JokeDescription']]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Joke Tags</legend>
			<?= (new o\SearchElement('Primary Tag', '/search/tags', false, $joke['PrimaryTagID'] ?? null, ['name' => 'primary', 'required' => true]))->buildElement(); ?>
			<?php $tagList = new o\SearchElement('Tag', '/search/tags', false, null, ['name' => 'tags[]', 'required' => true]); ?>
			<?= (new o\InputTable('Tags', [$tagList], ['value' => $joke['OtherTags']]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Meta Jokes</legend>
			<p>Select what Meta Jokes this joke is classified under.</p>
			<?php
			$tagList = new o\SearchElement('Meta Joke', '/search/meta-jokes', false, null, ['name' => 'metas[]', 'required' => true]);
			?>
			<?= (new o\InputTable('Meta Jokes', [$tagList], ['value' => $joke['MetaJokes'] ?? null]))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Joke</button>
	</form>
</main>