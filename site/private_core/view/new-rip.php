<?php

use RipDB\Objects as o;

include_once('private_core/objects/InputTable.php');
?>
<main>
	<h1>Add A New Rip</h1>
	<p>Fill in this form to add a new rip to the database.</p>

	<form id="new-rip" method="POST">
		<fieldset>
			<legend>Rip Information</legend>
			<div>
				<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true]))->buildElement() ?>
				<?= (new o\InputElement('Alternate Name', o\InputTypes::text, ['name' => 'altName', 'maxlength' => 2048]))->buildElement() ?>
				<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 9999]))->buildElement() ?>
				<?= (new o\InputElement('Length', o\InputTypes::text, ['name' => 'length', 'placeholder' => 'hh:mm:ss', 'pattern' => '[0-9]{1,}:[0-9]{1,2}:[0-9]{1,2}']))->buildElement() ?>
				</div>
			<div>
				<?= (new o\InputElement('Upload Date', o\InputTypes::date, ['name' => 'date', 'required' => true]))->buildElement() ?>
				<?= (new o\InputElement('Rip URL', o\InputTypes::url, ['name' => 'url']))->buildElement() ?>
				<?= (new o\DropdownElement('Rip Channel', $channels, ['name' => 'channel', 'required' => true]))->buildElement() ?>
				<?= (new o\DropdownElement('Game', $games, ['name' => 'game', 'required' => true]))->buildElement() ?>
			</div>
		</fieldset>
		<fieldset>
			<legend>Rippers</legend>
			<p>If a rip features multiple rippers, put the main ripper credited first and all others after.</p>
			<?php
			$ripperList = new o\DropdownElement('Ripper', $rippers, ['name' => 'rippers[]', 'required' => true]);
			$ripperAlias = new o\InputElement('Alias Name', o\InputTypes::text, ['name' => 'aliases[]', 'tooltip' => "If the artist of the song is not the ripper's official name, enter it here."]);
			?>
			<?= (new o\InputTable('Rippers', [$ripperList, $ripperAlias]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Rip Jokes</legend>
			<p>Enter each joke that is featured in this rip below. If the same joke appears multiple times, at different timestamps, please add it for each occurrence.</p>
			<?php
			$jokeList = new o\DropdownElement('Joke', $jokes, ['name' => 'jokes[]', 'required' => true]);
			$start = new o\InputElement('Start', o\InputTypes::text);
			$end = new o\InputElement('End', o\InputTypes::text);
			// $timestamps = new o\InputTable('Timestamps', [$start, $end])
			?>
			<?= (new o\InputTable('Jokes', [$jokeList, $start, $end]))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Rip</button>
	</form>
</main>