<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add Ripper</h1>
	<p>Add a new ripper to the database here!</p>
	<form id="new-ripper" method="POST">
		<fieldset>
			<legend>Ripper Information</legend>
			<?= (new o\InputElement('Ripper Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 256, 'required' => true], null, true))->buildElement() ?>
		</fieldset>
		<button type="submit">Add Ripper</button>
	</form>
</main>