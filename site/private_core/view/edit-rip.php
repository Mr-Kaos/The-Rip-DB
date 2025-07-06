<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');

?>
<main>
	<h1>Edit Rip</h1>
	<p>This form is editing the data of an existing rip.</p>

	<form id="edit-rip" method="POST">
		<fieldset>
			<legend>Rip Information</legend>
			<div>
				<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true, 'value' => $rip['RipName']]))->buildElement() ?>
				<?= (new o\InputElement('Alternate Name', o\InputTypes::text, ['name' => 'altName', 'maxlength' => 2048, 'value' => $rip['RipAlternateName']]))->buildElement() ?>
			</div>
			<div>
				<?= (new o\InputElement('Upload Date', o\InputTypes::date, ['name' => 'date', 'required' => true, 'value' => date_format(new DateTime($rip['RipDate']), 'Y-m-d')]))->buildElement() ?>
				<?= (new o\InputElement('Rip URL', o\InputTypes::url, ['name' => 'url', 'required' => true, 'value' => $rip['RipURL']]))->buildElement() ?>
				<?= (new o\InputElement('YouTube Video ID', o\InputTypes::text, ['name' => 'ytId', 'minlength' => 11, 'maxlength' => 11, 'value' => $rip['RipYouTubeID'], 'pattern' => '[A-Za-z0-9_\-]{11}']))->buildElement() ?>
				<?= (new o\DropdownElement('Rip Channel', $channels, ['name' => 'channel', 'required' => true, 'selected' => $rip['RipChannel']]))->buildElement() ?>
			</div>
		</fieldset>
		<fieldset>
			<legend>Rip Metadata</legend>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 9999, 'value' => $rip['RipDescription']]))->buildElement() ?>
			<?= (new o\InputElement('Length', o\InputTypes::text, ['name' => 'length', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'required' => true, 'value' => $rip['RipLength']]))->buildElement() ?>
			<?= (new o\SearchElement('Game', '/search/games', false, [$rip['RipGame'] => $rip['GameName']], ['name' => 'game', 'required' => true]))->buildElement() ?>
			<?= (new o\SearchElement('Genres', '/search/genres', true, $rip['Genres'], ['name' => 'genres[]', 'required' => true]))->buildElement(); ?>
		</fieldset>
		<fieldset>
			<legend>Rippers</legend>
			<p>If a rip features multiple rippers, put the main ripper credited first and all others after.</p>
			<?php
			$ripperList = new o\SearchElement('Ripper', '/search/rippers', false, null, ['name' => 'rippers[]', 'required' => true]);
			$ripperAlias = new o\InputElement('Alias Name', o\InputTypes::text, ['name' => 'aliases[]', 'tooltip' => "If the artist of the song is not the ripper's official name, enter it here."]);
			?>
			<?= (new o\InputTable('Rippers', [$ripperList, $ripperAlias], ['value' => $rip['Rippers']]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Rip Jokes</legend>
			<p>Enter each joke that is featured in this rip below. If the same joke appears multiple times, at different timestamps, please add it for each occurrence.</p>
			<?php
			$jokeList = new o\SearchElement('Joke', '/search/jokes', false, null, ['name' => 'jokes[]', 'required' => true]);
			$start = new o\InputElement('Start', o\InputTypes::text, ['name' => 'jokeStart[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--']);
			$end = new o\InputElement('End', o\InputTypes::text, ['name' => 'jokeEnd[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--']);
			?>
			<?= (new o\InputTable('Jokes', [$jokeList, $start, $end], ['value' => $rip['Jokes']]))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Rip</button>
	</form>
</main>