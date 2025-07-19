<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Update Meta</h1>
	<form id="edit-meta" method="POST">
		<fieldset>
			<legend>Meta Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true, 'value' => $meta['MetaName']], null, true))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Meta</button>
	</form>
</main>