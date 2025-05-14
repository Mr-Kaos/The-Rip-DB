<?php

use RipDB\Objects as o;

include_once('private_core/objects/InputTable.php');
?>
<main>
	<h1>Add A New Tag</h1>
	<p>Fill in this form to add a tag for use in jokes.</p>
	<p>It's best not to do it here, and instead do it while <a href="/jokes/new">adding a new joke</a>.</p>

	<form id="new-tag" method="POST">
		<fieldset>
			<legend>Tag Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true]))->buildElement() ?>
			<?= (new o\InputElement('Meta Only', o\InputTypes::checkbox, ['name' => 'meta', 'value' => 1, 'value-alt' => 0, ]))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Tag</button>
	</form>
</main>