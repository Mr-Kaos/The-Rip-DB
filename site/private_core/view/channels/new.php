<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add Channel</h1>
	<p>Add a new ripping channel to the database here!</p>
	<form id="new-channel" method="POST" class="form-grid">
		<fieldset>
			<legend>Channel Information</legend>
			<?= (new o\InputElement('Channel Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true, 'style' => 'width:98%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel URL', o\InputTypes::url, ['name' => 'url', 'required' => true, 'maxlength' => 512, 'placeholder' => 'https://www.youtube.com/channel', 'pattern' => '^https:\/{2}[w]{0,3}[youtube.com].*$', 'style' => 'width:98%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Channel Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 10000, 'style' => 'width:98%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Is Active?', o\InputTypes::checkbox, ['name' => 'active', 'checked' => true]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Other Channel Details</legend>
			<?= (new o\InputElement('Channel Wiki URL', o\InputTypes::url, ['name' => 'wiki-url', 'maxlength' => 512, 'placeholder' => 'https://wiki.channel-name.xyz', 'pattern' => '^https:\/{2}.*$', 'style' => 'width:98%'], null, true))->buildElement() ?>
		</fieldset>
		<div style="grid-column:span 2">
			<button type="submit">Add Channel</button>
		</div>
	</form>
</main>