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
				<span>
					<?= (new o\InputElement('&#x1F50A', o\InputTypes::range, ['id' => 'volume', 'min' => 0, 'max' => 100, 'value' => 50]))->buildElement() ?>
				</span>
			</div>
			<form id="round-form" action="javascript:void()" style="display:flex">
			</form>
			<button type="submit" id="submit-round" form="round-form" style="display:block;margin:auto;">Submit Guess</button>
		</div>
		<div id="results" style="display:none">
			<h2>Results:</h2>
			<div id="results-data">
				<div id="answers" class="grid">
					<ul>
						<li><b>Hmm...</b> You shouldn't really be seeing this.</li>
					</ul>
				</div>
				<p>Total: <var id="score">0</var> Pts</p>
			</div>
			<button type="button" id="advance-round">Next Round</button>
			<div id="feedback" class="container" style="text-align:center;width:fit-content;margin:auto;">
				<p>How suitable was this rip for RipGuessr?</p>
				<button class="btn-good" id="btnGood">&gt;:] Nice</button>
				<button class="btn-bad" id="btnBad">&gt;:[ Not Nice</button>
				<button class="btn-warn" id="btnIncorrect" style="margin:auto;margin-top:5px;display:inline-block">It was missing/has the wrong joke!</button>
				<form id="feedback-extra" style="display:none">
					<?= (new o\InputElement('What was wrong?', o\InputTypes::text, ['id' => 'joke', 'minlength' => 1, 'maxlength' => 1024, 'style' => 'margin-top:10px;min-width:250px', 'placeholder' => 'Enter the incorrect/missing joke(s) here', 'disabled' => true, 'required' => true]))->buildElement(); ?>
					<br>
					<button id="btnExtraSubmit" type="submit" style="margin-top:5px;">Submit Feedback</button>
				</form>
			</div>
		</div>
	</div>
	<div id="settings" style="display:none">
		<h2>Game Settings</h2>
		<form action="#" onsubmit="game.setSettings(event)">
			<fieldset>
				<legend>Game Rules</legend>
				<?= (new o\InputElement('No. of Rounds', o\InputTypes::range, ['name' => 'rounds', 'min' => 1, 'max' => game\Game::MAX_ROUNDS, 'value' => 3]))->buildElement() ?>
				<div style="display:flex">
					<?= (new o\InputElement('Min. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-min', 'min' => 1, 'max' => game\Settings::MAX_JOKES, 'value' => 1], null, true))->buildElement() ?>
					<?= (new o\InputElement('Max. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-max', 'min' => 1, 'max' => game\Settings::MAX_JOKES, 'value' => 2], null, true))->buildElement() ?>
				</div>
			</fieldset>
			<fieldset>
				<legend>Difficulty</legend>
				<?= (new o\InputElement(game\Difficulty::Beginner->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-1', 'value' => game\Difficulty::Beginner->name, 'title' => game\Difficulty::Beginner->value, 'checked' => true]))->buildElement() ?>
				<?= (new o\InputElement(game\Difficulty::Standard->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-2', 'value' => game\Difficulty::Standard->name, 'title' => game\Difficulty::Standard->value]))->buildElement() ?>
				<?= (new o\InputElement(game\Difficulty::Hard->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-3', 'value' => game\Difficulty::Hard->name, 'title' => game\Difficulty::Hard->value]))->buildElement() ?>
				<!-- <details style="margin:10px 0px" open> -->
				<!-- <summary style="margin:5px 0px">Difficulty Overrides</summary> -->
				<!-- <?= (new o\InputElement('Show Number of Correct Answers', o\InputTypes::checkbox, ['checked' => true, 'name' => 'show-count', 'title' => "This will show how many answers there are for fields that take multiple values.\nE.g. This will show how many jokes are in the round's rip."]))->buildElement() ?> -->
				<!-- </details> -->
			</fieldset>
			<fieldset style="display:flex;justify-content:space-between">
				<legend>Filters</legend>
				<div>
					<?= (new o\SearchElement('Meta Jokes', '/search/meta-jokes', true, null, ['name' => 'filter-metajokes']))->buildElement() ?>
					<?= (new o\SearchElement('Metas', '/search/metas', true, null, ['name' => 'filter-metas',]))->buildElement() ?>
				</div>
				<div style="display:flex">
					<?= (new o\InputElement('Min. Rip Length', o\InputTypes::text, ['name' => 'minlength', 'pattern' => '([0-9]{2}:[0-9]{2})', 'value' => '00:10', 'placeholder' => '00:10', 'required' => true, 'onchange' => 'validateLength(this)'], null, true))->buildElement() ?>
					<?= (new o\InputElement('Max. Rip Length', o\InputTypes::text, ['name' => 'maxlength', 'pattern' => '([0-9]{2}:[0-9]{2})', 'value' => '03:00', 'placeholder' => '03:00', 'required' => true, 'onchange' => 'validateLength(this)'], null, true))->buildElement() ?>
				</div>
			</fieldset>
			<button type="submit" style="margin: 10px auto;display: block;padding: 10px;font-size: larger;">Play</button>
		</form>
		<section>
			<h3>Help/FAQ</h3>
			<details class="example">
				<summary>I keep getting rips with have jokes I don't know!</summary>
				<p>You can specify <b>meta jokes</b> (e.g. artists, bands, franchises) to limit what rips will be selected to you.<br>
					Try entering some that you're familiar with.</p>
				<p>You can also filter by a <b>meta tag</b> (e.g. jokes from a "Video Game", "Viral Video", "Anime" etc.) if you want to explore a specific genre/field.</p>
			</details>
		</section>
	</div>
</main>
<script src="https://www.youtube.com/iframe_api" defer></script>
<script src="/res/js/ripguesser.js" defer></script>