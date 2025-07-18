<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');
?>
<main>
	<h1>Edit Tag</h1>
	<p>Edit the tag name here.</p>
	<p>You should only need come here if there is a typo in the tag.</p>

	<form id="edit-tag" method="POST">
		<fieldset>
			<legend>Tag Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true, 'value' => $tag['TagName']]))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Tag</button>
	</form>
</main>