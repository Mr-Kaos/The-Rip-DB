<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Edit Platform</h1>
	<p>Edit the game platform name here.</p>
	<p>You should only need come here if there is a typo in the name.</p>

	<form id="edit-game platform" method="POST">
		<fieldset>
			<legend>Platform Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true, 'value' => $platform['PlatformName']]))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Platform</button>
	</form>
</main>