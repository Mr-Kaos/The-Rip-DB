<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add A Meta</h1>
	<p>Fill in this form to add a new meta to the database.</p>
	<form id="new-meta" method="POST">
		<fieldset>
			<legend>Meta Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true], null, true))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Meta</button>
	</form>
</main>