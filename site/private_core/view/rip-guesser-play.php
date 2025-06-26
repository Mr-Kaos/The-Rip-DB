<?php

use RipDB\Objects as o;
use RipDB\RipGuesser as game;
?>
<main>
	<div id="game" style="display:none">
		<h1 id="title">Round 1</h1>
		<div id="round">
			<h2 id="rip-name"></h2>
			<p><i>Listen to the rip and identify the jokes used in it!</i></p>
			<div id="audio-player"></div>
			<form id="round-form" action="javascript:void()">
			</form>
			<button type="submit" form="round-form">Submit Guess</button>
		</div>
		<div id="results" style="display:none">
			<h1>Round Results</h1>
			<ul id="answers">
				<li><b>Hmm...</b>You shouldn't really be seeing this.</li>
			</ul>
			<p>Total: <var id="score">0</var> Pts</p>
			<button type="button"">Next Round</button>
		</div>
	</div>
	<div id="settings" style="display:none">
		<h2>Game Settings</h2>
		<form action="#" onsubmit="game.setSettings(event)">
			<fieldset>
				<legend>Game Rules</legend>
				<?= (new o\InputElement('No. of Rounds', o\InputTypes::range, ['name' => 'rounds', 'min' => 1, 'max' => game\Game::MAX_ROUNDS, 'value' => 3]))->buildElement() ?>
			</fieldset>
			<fieldset>
				<legend>Difficulty</legend>
				<?= (new o\InputElement(game\Difficulty::Beginner->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-1', 'value' => game\Difficulty::Beginner->name, 'title' => game\Difficulty::Beginner->value, 'checked' => true]))->buildElement() ?>
				<?= (new o\InputElement(game\Difficulty::Standard->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-2', 'value' => game\Difficulty::Standard->name, 'title' => game\Difficulty::Standard->value]))->buildElement() ?>
				<?= (new o\InputElement(game\Difficulty::Hard->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-3', 'value' => game\Difficulty::Hard->name, 'title' => game\Difficulty::Hard->value]))->buildElement() ?>
				<details>
					<summary>Difficulty Overrides</summary>
					<?= (new o\InputElement('Min. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-min', 'min' => 1, 'max' => game\Settings::MIN_JOKES, 'value' => 1]))->buildElement() ?>
					<?= (new o\InputElement('Max. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-max', 'min' => 1, 'max' => game\Settings::MAX_JOKES, 'value' => 2]))->buildElement() ?>
					<?= (new o\InputElement('Show Number of Correct Answers', o\InputTypes::checkbox, ['name' => 'show-count', 'title' => "This will show how many answers there are for fields that take multiple values.\nE.g. This will show how many jokes are in the round's rip."]))->buildElement() ?>
				</details>
			</fieldset>
			<fieldset>
				<legend>Filters</legend>
				<?= (new o\SearchElement('Meta Jokes', '/search/meta-jokes', true, null, ['name' => 'filter-metajokes',]))->buildElement() ?>
				<?= (new o\SearchElement('Metas', '/search/metas', true, null, ['name' => 'filter-metas',]))->buildElement() ?>
				<?= (new o\InputElement('Max Rip Length', o\InputTypes::text, ['name' => 'length', 'pattern' => '([0-9]{2}:[0-9]{2})', 'value' => '03:00', 'placeholder' => '03:00', 'required' => true, 'onchange' => 'validateLength(this)']))->buildElement() ?>
			</fieldset>
			<button type="submit">Play</button>
		</form>
	</div>
</main>
<script src="/res/js/ripguesser.js" defer></script>