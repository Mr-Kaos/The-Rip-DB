<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add Channel</h1>
	<p>Add a new ripping channel to the database here!</p>
	<form id="edit-channel" method="POST">
		<fieldset>
			<legend>Channel Information</legend>
			<?= (new o\InputElement('Channel Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel URL', o\InputTypes::url, ['name' => 'url', 'maxlength' => 512, 'placeholder' => 'https://www.youtube.com/channel', 'pattern' => '^https:\/{2}[w]{0,3}[youtube.com].*$'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 10000], null, true))->buildElement() ?>
		</fieldset>
		<button type="submit">Add Channel</button>
	</form>
</main>