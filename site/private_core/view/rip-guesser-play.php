<?php

use RipDB\Objects as o;
use RipDB\RipGuesser as game;
?>
<main style="max-width:1000px;margin-left:auto;margin-right:auto;">
	<div id="game" style="display:none">
		<h1 id="title">Round ?</h1>
		<h2 id="rip-name"></h2>
		<div id="stream"></div>
		<div id="round">
			<p><i>Listen to the rip and identify the jokes used in it!</i></p>
			<div id="player">
				<button type="button" id="play-pause">&#x23F3;</button>
				<?= (new o\InputElement('Volume', o\InputTypes::range, ['id' => 'volume', 'min' => 0, 'max' => 100, 'value' => 50]))->buildElement() ?>
			</div>
			<form id="round-form" action="javascript:void()" style="display:flex">
			</form>
			<button type="submit" form="round-form">Submit Guess</button>
		</div>
		<div id="results" style="display:none">
			<h2>Results:</h2>
			<div id="answers" class="grid">
				<ul>
					<li><b>Hmm...</b> You shouldn't really be seeing this.</li>
				</ul>
			</div>
			<p>Total: <var id="score">0</var> Pts</p>
			<button type="button" id="advance-round">Next Round</button>
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
					<?= (new o\InputElement('Min. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-min', 'min' => 1, 'max' => game\Settings::MAX_JOKES, 'value' => 1]))->buildElement() ?>
					<?= (new o\InputElement('Max. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-max', 'min' => 1, 'max' => game\Settings::MAX_JOKES, 'value' => 2]))->buildElement() ?>
					<!-- <?= (new o\InputElement('Show Number of Correct Answers', o\InputTypes::checkbox, ['checked' => true, 'name' => 'show-count', 'title' => "This will show how many answers there are for fields that take multiple values.\nE.g. This will show how many jokes are in the round's rip."]))->buildElement() ?> -->
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
<script src="https://www.youtube.com/iframe_api"></script>