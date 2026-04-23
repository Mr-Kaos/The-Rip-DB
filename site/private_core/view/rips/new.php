<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');
?>
<main>
	<h1>Add A New Rip</h1>
	<p>Fill in this form to add a new rip to the database.</p>
	<form id="new-rip" method="POST" class="form-grid">
		<fieldset>
			<legend>Rip Information</legend>
			<div style="display:flex;gap:10px">
				<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true], null, true))->buildElement() ?>
				<?= (new o\InputElement('Mix Name', o\InputTypes::text, ['name' => 'mixName', 'maxlength' => 256], null, true))->buildElement() ?>
			</div>
			<?= (new o\InputElement('Alternate Name', o\InputTypes::text, ['name' => 'altName', 'maxlength' => 2048, 'style' => 'width:100%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Upload Date', o\InputTypes::date, ['name' => 'date', 'required' => true, 'value' => date_format(new DateTime(), 'Y-m-d')], null, true))->buildElement() ?>
			<?= (new o\InputElement('Rip URL', o\InputTypes::url, ['name' => 'url', 'required' => true, 'style' => 'width:100%', 'oninput' => 'getYouTubeID(this.value)'], null, true))->buildElement() ?>
			<?= (new o\InputElement('YouTube Video ID', o\InputTypes::text, ['name' => 'ytId', 'minlength' => 11, 'maxlength' => 11, 'pattern' => '[A-Za-z0-9_\-]{11}'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Rip Alternative URL', o\InputTypes::url, ['name' => 'alturl', 'style' => 'width:100%', 'title' => 'URl of an alternative release for the rip, e.g. the album release URL.'], null, true))->buildElement() ?>
			<?= (new o\DropdownElement('Rip Channel', $channels, ['name' => 'channel', 'required' => true, 'modal' => '/channels/new', 'modal-tgt-id' => 'new-channel', 'modal-value-key' => 'NewChannel'], null, true))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Rip Metadata</legend>
			<?= (new o\InputElement('Length', o\InputTypes::timestamp, ['name' => 'length', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'required' => true, 'maxlength' => 9], null, true))->buildElement() ?>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 9999, 'style' => 'width:250px;height:60px'], null, true))->buildElement() ?>
			<div style="display:flex;gap:10px">
				<?= (new o\SearchElement('Game', '/search/games', false, null, ['name' => 'game', 'required' => true, 'modal' => '/games/new', 'modal-tgt-id' => 'new-game', 'modal-value-key' => 'NewGame'], null, true))->buildElement() ?>
			</div>
			<?= (new o\InputElement('Wiki URL', o\InputTypes::url, ['name' => 'wikiUrl', 'style' => 'width:100%'], null, true))->buildElement() ?>
			<?php
			$composerList = new o\SearchElement('Composer/Artist', '/search/composers', false, null, ['name' => 'composers[]', 'modal' => '/composers/new', 'modal-tgt-id' => 'new-composer', 'modal-value-key' => 'FirstName']);
			?>
			<?= (new o\InputTable('Composers/Artists', [$composerList]))->buildElement() ?>
		</fieldset>
		<fieldset style="grid-column:span 2">
			<legend>Rippers</legend>
			<p>If a rip features multiple rippers, put the main ripper credited first and all others after.</p>
			<?php
			$ripperList = new o\SearchElement('Ripper', '/search/rippers', false, null, ['name' => 'rippers[]', 'modal' => '/rippers/new', 'modal-tgt-id' => 'new-ripper', 'modal-value-key' => 'NewRipper']);
			$ripperAlias = new o\InputElement('Alias Name', o\InputTypes::text, ['name' => 'aliases[]', 'tooltip' => "If the artist of the song is not the ripper's official name, enter it here."], null, true);
			?>
			<?= (new o\InputTable('Rippers', [$ripperList, $ripperAlias]))->buildElement() ?>
		</fieldset>
		<fieldset style="grid-column:span 2">
			<legend>Rip Jokes</legend>
			<p>Enter each joke that is featured in this rip below. If the same joke appears multiple times, at different timestamps, please add it for each occurrence.</p>
			<?php
			$jokeList = new o\SearchElement('Joke', '/search/jokes', false, null, ['name' => 'jokes[]', 'required' => true, 'modal' => '/jokes/new', 'modal-tgt-id' => 'new-joke', 'modal-value-key' => 'InJokeName']);
			$start = new o\InputElement('Start', o\InputTypes::timestamp, ['name' => 'jokeStart[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'style' => 'width:60px'], null, true);
			$end = new o\InputElement('End', o\InputTypes::timestamp, ['name' => 'jokeEnd[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'style' => 'width:60px'], null, true);
			$comment = (new o\InputElement('Notes', o\InputTypes::text, ['name' => 'comment[]', 'maxlength' => 1024], null, true));
			$genre = (new o\SearchElement('Genre', '/search/genres', false, null, ['name' => 'genres[]'], null, true));
			?>
			<?= (new o\InputTable('Jokes', [$jokeList, $start, $end, $genre, $comment]))->buildElement() ?>
		</fieldset>
		<div>
			<button type="submit">Submit Rip</button>
		</div>
	</form>
</main>
<script src="/res/js/rip.js"></script>