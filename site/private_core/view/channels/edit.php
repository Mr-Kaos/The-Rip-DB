<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Edit Channel</h1>
	<p>Update an existing channel's record here.</p>
	<form id="edit-channel" method="POST" class="form-grid">
		<fieldset>
			<legend>Channel Information</legend>
			<?= (new o\InputElement('Channel Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true, 'value' => $channel['ChannelName'], 'style' => 'width:98%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel URL', o\InputTypes::url, ['name' => 'url', 'maxlength' => 512, 'value' => $channel['ChannelURL'], 'style' => 'width:98%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 10000, 'value' => $channel['ChannelDescription'], 'style' => 'width:98%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Is Active?', o\InputTypes::checkbox, ['name' => 'active', 'checked' => $channel['IsActive']]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Other Channel Details</legend>
			<?= (new o\InputElement('Channel Wiki URL', o\InputTypes::url, ['name' => 'wiki-url', 'maxlength' => 512, 'value' => $channel['WikiURL'], 'placeholder' => 'https://wiki.channel-name.xyz', 'pattern' => '^https:\/{2}.*$', 'style' => 'width:98%'], null, true))->buildElement() ?>
		</fieldset>
		<div style="grid-column:span 2">
			<button type="submit">Update Channel</button>
		</div>
	</form>
</main>