<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add A Meta Joke</h1>
	<p>Fill in this form to add a new meta joke to the database.</p>
	<form id="new-meta-joke" method="POST">
		<fieldset>
			<legend>Meta Joke Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true], null, true))->buildElement() ?>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'required' => true, 'maxlength' => 9999], null, true))->buildElement() ?>
			<?= (new o\SearchElement('Associated Meta', '/search/metas', false, null, ['name' => 'meta', 'required' => true, 'modal' => '/metas/new', 'modal-tgt-id' => 'new-meta', 'modal-value-key' => 'InName']))->buildElement(); ?>
		</fieldset>
		<button type="submit">Submit Meta Joke</button>
	</form>
</main>