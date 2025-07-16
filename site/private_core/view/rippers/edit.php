<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Edit Ripper</h1>
	<p>Update an existing ripper's record here.</p>
	<form id="edit-ripper" method="POST">
		<fieldset>
			<legend>Ripper Information</legend>
			<?= (new o\InputElement('Ripper Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 256, 'required' => true, 'value' => $ripper['RipperName']], null, true))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Ripper</button>
	</form>
</main>