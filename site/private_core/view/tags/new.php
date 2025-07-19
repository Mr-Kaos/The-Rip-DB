<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add A New Tag</h1>
	<p>Fill in this form to add a tag for use in jokes.</p>
	<p>It's best not to do it here, and instead do it while <a href="/jokes/new">adding a new joke</a>.</p>

	<form id="new-tag" method="POST">
		<fieldset>
			<legend>Tag Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true]))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Tag</button>
	</form>
</main>