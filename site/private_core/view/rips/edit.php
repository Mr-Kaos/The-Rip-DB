<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');
?>
<main>
	<h1>Edit Rip</h1>
	<p>This form is editing the data of an existing rip.</p>
	<form id="edit-rip" method="POST" class="form-grid">
		<fieldset>
			<legend>Rip Information</legend>
			<div style="display:flex;gap:10px">
				<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true, 'value' => $rip['RipName']], null, true))->buildElement() ?>
				<?= (new o\InputElement('Alternate Name', o\InputTypes::text, ['name' => 'altName', 'maxlength' => 2048, 'value' => $rip['RipAlternateName']], null, true))->buildElement() ?>
			</div>
			<?= (new o\InputElement('Upload Date', o\InputTypes::date, ['name' => 'date', 'required' => true, 'value' => date_format(new DateTime($rip['RipDate']), 'Y-m-d')], null, true))->buildElement() ?>
			<?= (new o\InputElement('Rip URL', o\InputTypes::url, ['name' => 'url', 'required' => true, 'value' => $rip['RipURL'], 'style' => 'width:100%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('YouTube Video ID', o\InputTypes::text, ['name' => 'ytId', 'minlength' => 11, 'maxlength' => 11, 'value' => $rip['RipYouTubeID'], 'pattern' => '[A-Za-z0-9_\-]{11}'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Rip Alternative URL', o\InputTypes::url, ['name' => 'alturl', 'value' => $rip['RipAlternateURL'], 'style' => 'width:100%', 'title' => 'URl of an alternative release for the rip, e.g. the album release URL.'], null, true))->buildElement() ?>
			<?= (new o\DropdownElement('Rip Channel', $channels, ['name' => 'channel', 'required' => true, 'selected' => $rip['RipChannel'], 'modal' => '/channels/new', 'modal-tgt-id' => 'new-channel', 'modal-value-key' => 'NewChannel'], null, true))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Rip Metadata</legend>
			<?= (new o\InputElement('Length', o\InputTypes::text, ['name' => 'length', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'required' => true, 'value' => $rip['RipLength']], null, true))->buildElement() ?>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 9999, 'value' => $rip['RipDescription'], 'style' => 'width:90%;max-width:100%'], null, true))->buildElement() ?>
			<div style="display:flex;gap:10px">
				<?= (new o\SearchElement('Game', '/search/games', false, [$rip['RipGame'] => $rip['GameName']], ['name' => 'game', 'required' => true, 'modal' => '/games/new', 'modal-tgt-id' => 'new-game', 'modal-value-key' => 'NewGame'], null, true))->buildElement() ?>
				<?= (new o\SearchElement('Genres', '/search/genres', true, $rip['Genres'], ['name' => 'genres[]', 'required' => true], null, true))->buildElement(); ?>
			</div>
		</fieldset>
		<fieldset style="grid-column:span 2">
			<legend>Rippers</legend>
			<p>If a rip features multiple rippers, put the main ripper credited first and all others after.</p>
			<?php
			$ripperList = new o\SearchElement('Ripper', '/search/rippers', false, null, ['name' => 'rippers[]', 'required' => true, 'modal' => '/rippers/new', 'modal-tgt-id' => 'new-ripper', 'modal-value-key' => 'NewRipper']);
			$ripperAlias = new o\InputElement('Alias Name', o\InputTypes::text, ['name' => 'aliases[]', 'tooltip' => "If the artist of the song is not the ripper's official name, enter it here."], null, true);
			?>
			<?= (new o\InputTable('Rippers', [$ripperList, $ripperAlias], ['value' => $rip['Rippers']]))->buildElement() ?>
		</fieldset>
		<fieldset style="grid-column:span 2">
			<legend>Rip Jokes</legend>
			<p>Enter each joke that is featured in this rip below. If the same joke appears multiple times, at different timestamps, please add it for each occurrence.</p>
			<?php
			$jokeList = new o\SearchElement('Joke', '/search/jokes', false, null, ['name' => 'jokes[]', 'required' => true, 'modal' => '/jokes/new', 'modal-tgt-id' => 'new-joke', 'modal-value-key' => 'InJokeName']);
			$start = new o\InputElement('Start', o\InputTypes::text, ['name' => 'jokeStart[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--'], null, true);
			$end = new o\InputElement('End', o\InputTypes::text, ['name' => 'jokeEnd[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--'], null, true);
			?>
			<?= (new o\InputTable('Jokes', [$jokeList, $start, $end], ['value' => $rip['Jokes']]))->buildElement() ?>
		</fieldset>
		<div>
			<button type="submit">Update Rip</button>
		</div>
	</form>
</main>