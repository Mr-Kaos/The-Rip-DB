<?php

use RipDB\Objects as o;

include_once('private_core/objects/InputTable.php');
?>
<main>
	<h1>Add A New Joke</h1>
	<p>Fill in this form to add a new joke to the database.</p>
	<p>It's best not to do it here, and instead do it while <a href="/rips/new">adding a new rip</a>.</p>

	<form id="new-joke" method="POST">
		<fieldset>
			<legend>Joke Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true]))->buildElement() ?>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'required' => true, 'maxlength' => 9999]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Joke Tags</legend>
			<?= (new o\SearchElement('Primary Tag', '/search/tags', false, null, ['name' => 'primary', 'required' => true]))->buildElement(); ?>
			<?php $tagList = new o\SearchElement('Tag', '/search/tags', false, null, ['name' => 'tags[]', 'required' => true]); ?>
			<?= (new o\InputTable('Tags', [$tagList]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Joke Meta</legend>
			<?php
			$tagList = new o\SearchElement('Meta Tag', '/search/metas', false, null, ['name' => 'metas[]', 'required' => true]);
			?>
			<?= (new o\InputTable('Meta Tags', [$tagList]))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Joke</button>
	</form>
</main>