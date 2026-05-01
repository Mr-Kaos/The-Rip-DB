<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');
?>
<main>
	<h1><?= $heading ?></h1>
	<button type="button" onclick="importFromWiki()" style="float:right">Import from Wikitext</button>
	<p>Enter rip details here to store it in the database.</p>
	<form id="edit-rip" method="POST" class="form-grid">
		<fieldset>
			<legend>Rip Information</legend>
			<div style="display:flex;gap:10px">
				<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true, 'value' => $rip['RipName'] ?? null], null, true))->buildElement() ?>
				<?= (new o\InputElement('Mix Name', o\InputTypes::text, ['name' => 'mixName', 'maxlength' => 256, 'value' => $rip['MixName'] ?? null], null, true))->buildElement() ?>
			</div>
			<?= (new o\InputElement('Alternate Name', o\InputTypes::text, ['name' => 'altName', 'maxlength' => 2048, 'value' => $rip['RipAlternateName'] ?? null, 'style' => 'width:100%'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Upload Date', o\InputTypes::date, ['name' => 'date', 'required' => true, 'value' => date_format(new DateTime($rip['RipDate'] ?? null), 'Y-m-d')], null, true))->buildElement() ?>
			<?= (new o\InputElement('Rip URL', o\InputTypes::url, ['name' => 'url', 'required' => true, 'value' => $rip['RipURL'] ?? null, 'style' => 'width:100%', 'oninput' => 'getYouTubeID(this.value)'], null, true))->buildElement() ?>
			<?= (new o\InputElement('YouTube Video ID', o\InputTypes::text, ['name' => 'ytId', 'minlength' => 11, 'maxlength' => 11, 'value' => $rip['RipYouTubeID'] ?? null, 'pattern' => '[A-Za-z0-9_\-]{11}'], null, true))->buildElement() ?>
			<?= (new o\InputElement('Rip Alternative URL', o\InputTypes::url, ['name' => 'alturl', 'value' => $rip['RipAlternateURL'] ?? null, 'style' => 'width:100%', 'title' => 'URl of an alternative release for the rip, e.g. the album release URL.'], null, true))->buildElement() ?>
			<?= (new o\DropdownElement('Rip Channel', $channels, ['name' => 'channel', 'required' => true, 'selected' => $rip['RipChannel'] ?? null, 'modal' => '/channels/new', 'modal-tgt-id' => 'new-channel', 'modal-value-key' => 'InChannel'], null, true))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Rip Metadata</legend>
			<?= (new o\InputElement('Length', o\InputTypes::timestamp, ['name' => 'length', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'required' => true, 'value' => $rip['RipLength'] ?? null, 'maxlength' => 9], null, true))->buildElement() ?>
			<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 9999, 'value' => $rip['RipDescription'] ?? null, 'style' => 'width:100%;max-width:100%'], null, true))->buildElement() ?>
			<?php $game = (($rip['RipGame'] ?? null) == null ? null : [$rip['RipGame'] => $rip['GameName']]); ?>
			<div style="display:flex;gap:10px">
				<?= (new o\SearchElement('Game', '/search/games', false, $game, ['name' => 'game', 'required' => true, 'modal' => '/games/new', 'modal-tgt-id' => 'new-game', 'modal-value-key' => 'NewGame'], null, true))->buildElement() ?>
			</div>
			<?= (new o\InputElement('Wiki URL', o\InputTypes::url, ['name' => 'wikiUrl', 'value' => $rip['RipWikiURL'] ?? null, 'style' => 'width:100%'], null, true))->buildElement() ?>
			<?= (new o\SearchElement('Composers', '/search/composers', true, $rip['Composers'] ?? null, ['name' => 'composers[]'], null, true))->buildElement(); ?>
		</fieldset>
		<fieldset style="grid-column:span 2">
			<legend>Rippers</legend>
			<p>If a rip features multiple rippers, put the main ripper credited first and all others after.</p>
			<?php
			$ripperList = new o\SearchElement('Ripper', '/search/rippers', false, null, ['name' => 'rippers[]', 'modal' => '/rippers/new', 'modal-tgt-id' => 'new-ripper', 'modal-value-key' => 'NewRipper']);
			$ripperAlias = new o\InputElement('Alias Name', o\InputTypes::text, ['name' => 'aliases[]', 'tooltip' => "If the artist of the song is not the ripper's official name, enter it here."], null, true);
			?>
			<?= (new o\InputTable('Rippers', [$ripperList, $ripperAlias], ['id' => 'rippers', 'value' => $rip['Rippers'] ?? null]))->buildElement() ?>
		</fieldset>
		<fieldset style="grid-column:span 2">
			<legend>Rip Jokes</legend>
			<p>Enter each joke that is featured in this rip below. If the same joke appears multiple times, at different timestamps, please add it for each occurrence.</p>
			<?php
			$jokeList = new o\SearchElement('Joke', '/search/jokes', false, null, ['name' => 'jokes[]', 'modal' => '/jokes/new', 'modal-tgt-id' => 'form-joke', 'modal-value-key' => 'InJokeName']);
			$start = new o\InputElement('Start', o\InputTypes::timestamp, ['name' => 'jokeStart[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'style' => 'width:60px'], null, true);
			$end = new o\InputElement('End', o\InputTypes::timestamp, ['name' => 'jokeEnd[]', 'pattern' => '(?:[0-9]{0,2}:?)([0-9]{2}:[0-9]{2})', 'placeholder' => '--:--:--', 'style' => 'width:60px'], null, true);
			$comment = (new o\InputElement('Notes', o\InputTypes::text, ['name' => 'comment[]', 'maxlength' => 1024], null, true));
			$genre = (new o\SearchElement('Genre', '/search/genres', false, null, ['name' => 'genres[]'], null, true));
			?>
			<?= (new o\InputTable('Jokes', [$jokeList, $start, $end, $genre, $comment], ['id' => 'jokes', 'value' => $rip['Jokes'] ?? null]))->buildElement() ?>
		</fieldset>
		<div>
			<button type="submit">Save Rip</button>
		</div>
	</form>
	<div id="import-errors" style="display:none">
		<br>
		<div class="notif highlight alert">
			<p>Some content was detected from the source but does not exist in the database!<br><br>Please check the following items and add them to the database if valid to ensure a more complete import of the wiki page:</p>
		</div>
		<br>
		<div class="grid"></div>
	</div>
</main>
<div id="template-import" style="display:none">
	<p>Paste the wikitext from a wiki page in here and click "Parse Page" to update the rip's record.</p>
	<p><em><strong>Note that currently any jokes, games, composers or rippers that do not exist in the database will not be automatically imported and must be added manually.</strong></em></p>
	<?= (new o\InputElement('Wiki Source', o\InputTypes::textarea, ['name' => 'wiki_source', 'style' => 'width:calc(100% - 12px);min-height:50px;height:50px;resize:vertical;', 'placeholder' => 'Paste wiki page source code here'], null, true))->buildElement() ?>
	<details>
		<summary>About Wikitext Import</summary>
		<p>This import feature requires the editable source of a wiki page (the wikitext, wiki markup or wikicode). This is the markup language of a wiki page used across many wiki sites such as MediaWiki and Fandom. It can easily be accessed by appending &quot;<code>&quest;action=edit</code>&quot; at the end of a wiki page's URL.</p>
		<p>It works by simply looking at each line of text one by one and scanning for keywords in each line to determine what type of data it is. Unfortunately, this means it is also prone to errors and may not always find something.</p>
		<p>If you believe something should have been captured but isn't, please submit a bug report on the GitHub and the line of text in the wikitext that was not imported correctly.</p>
		</detail>
</div>
<script src="/res/js/rip.js"></script>