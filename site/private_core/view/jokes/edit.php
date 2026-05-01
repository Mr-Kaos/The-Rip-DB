<?php

use RipDB\Objects as o;

include_once('private_core/objects/pageElements/InputTable.php');
?>
<main>
	<h1><?= $heading ?></h1>
	<form id="form-joke" method="POST" class="form-grid">
		<fieldset style="grid-column:span 2">
			<legend>Joke Information</legend>
			<div class="form-grid">
				<div>
					<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 1024, 'required' => true, 'value' => $joke['JokeName'] ?? null], null, true))->buildElement() ?>
					<?= (new o\InputElement('Description', o\InputTypes::textarea, ['name' => 'description', 'maxlength' => 9999, 'value' => $joke['JokeDescription'] ?? null], null, true))->buildElement() ?>
				</div>
				<?php $altName = new o\InputElement('Alternate Name', o\InputTypes::text, ['name' => 'alt_names[]', 'max-length' => 512, 'style' => 'width:90%'], null, true); ?>
				<?= (new o\InputTable('Alternate Names', [$altName], ['value' => $joke['AltNames'] ?? null]))->buildElement() ?>
			</div>
		</fieldset>
		<fieldset>
			<legend>Joke Tags</legend>
			<?= (new o\SearchElement('Primary Tag', '/search/tags', false, $joke['PrimaryTagID'] ?? null, ['name' => 'primary', 'modal' => '/tags/new', 'modal-tgt-id' => 'new-tag', 'modal-value-key' => 'InTagName']))->buildElement(); ?>
			<?php $tagList = new o\SearchElement('Tag', '/search/tags', false, null, ['name' => 'tags[]', 'modal' => '/tags/new', 'modal-tgt-id' => 'new-tag', 'modal-value-key' => 'InTagName']); ?>
			<?= (new o\InputTable('Tags', [$tagList], ['value' => $joke['OtherTags'] ?? null]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Meta Jokes</legend>
			<p>Select what Meta Jokes this joke is classified under.</p>
			<?php
			$tagList = new o\SearchElement('Meta Joke', '/search/meta-jokes', false, null, ['name' => 'metas[]', 'modal' => '/meta-jokes/new', 'modal-tgt-id' => 'new-meta-joke', 'modal-value-key' => 'InName']);
			?>
			<?= (new o\InputTable('Meta Jokes', [$tagList], ['value' => $joke['MetaJokes'] ?? null]))->buildElement() ?>
		</fieldset>
		<div>
			<button type="submit">Submit Joke</button>
		</div>
	</form>
</main>