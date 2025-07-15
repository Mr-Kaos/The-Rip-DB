<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Edit Channel</h1>
	<p>Update an existing channels's record here.</p>
	<form id="edit-channel" method="POST">
		<fieldset>
			<legend>Channel Information</legend>
			<?= (new o\InputElement('Channel Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true, 'value' => $channel['ChannelName']], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel URL', o\InputTypes::url, ['name' => 'url', 'maxlength' => 512, 'value' => $channel['ChannelURL']], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 10000, 'value' => $channel['ChannelDescription']], null, true))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Channel</button>
	</form>
</main>