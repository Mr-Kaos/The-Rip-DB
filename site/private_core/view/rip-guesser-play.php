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
				<button type="button" id="rewind" disabled>&#x1F501;</button>
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
		<div style="display:flex;justify-content:space-between;align-items:center">
			<h2>Game Settings</h2>
			<button onclick="helpModal.open()" type="button">Help/FAQ</button>
		</div>
		<form action="#" onsubmit="game.setSettings(event)" class="form-grid">
			<fieldset style="grid-column:span 2">
				<legend>Game Rules</legend>
				<?= (new o\InputElement('No. of Rounds', o\InputTypes::range, ['name' => 'rounds', 'min' => 1, 'max' => game\Game::MAX_ROUNDS, 'value' => 3]))->buildElement() ?>
				<div style="display:flex">
					<?= (new o\InputElement('Min. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-min', 'min' => 1, 'max' => game\Settings::MAX_JOKES, 'value' => 1], null, true))->buildElement() ?>
					<?= (new o\InputElement('Max. Jokes per Rip', o\InputTypes::number, ['name' => 'jokes-max', 'min' => 1, 'max' => game\Settings::MAX_JOKES, 'value' => 2], null, true))->buildElement() ?>
				</div>
			</fieldset>
			<fieldset style="grid-column:span 2">
				<legend>Difficulty</legend>
				<?= (new o\InputElement(game\Difficulty::Beginner->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-1', 'value' => game\Difficulty::Beginner->name, 'title' => game\Difficulty::Beginner->value, 'checked' => true]))->buildElement() ?>
				<?= (new o\InputElement(game\Difficulty::Standard->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-2', 'value' => game\Difficulty::Standard->name, 'title' => game\Difficulty::Standard->value]))->buildElement() ?>
				<?= (new o\InputElement(game\Difficulty::Hard->name, o\InputTypes::radio, ['name' => 'difficulty', 'id' => 'difficulty-3', 'value' => game\Difficulty::Hard->name, 'title' => game\Difficulty::Hard->value]))->buildElement() ?>
				<!-- <details style="margin:10px 0px" open> -->
				<!-- <summary style="margin:5px 0px">Difficulty Overrides</summary> -->
				<!-- <?= (new o\InputElement('Show Number of Correct Answers', o\InputTypes::checkbox, ['checked' => true, 'name' => 'show-count', 'title' => "This will show how many answers there are for fields that take multiple values.\nE.g. This will show how many jokes are in the round's rip."]))->buildElement() ?> -->
				<!-- </details> -->
			</fieldset>
			<fieldset style="display:flex;justify-content:space-between;grid-column:span 2">
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
			<fieldset style="grid-column:span 2">
				<legend>Playlists</legend>
				<?= (new o\InputElement(null, o\InputTypes::search, ['name' => 'playlist-search', 'form' => '', 'placeholder' => 'Search Playlists', 'oninput' => 'displayInputMessage(this)', 'onkeypress' => 'settings.searchPlaylists(event)']))->buildElement(); ?>
				<?= (new o\InputElement(null, o\InputTypes::search, ['name' => 'playlist-code', 'form' => '', 'minlength' => 8, 'maxlength' => 8, 'pattern' => '[0-9a-zA-Z]{8}', 'placeholder' => 'Find by Playlist Code', 'oninput' => 'displayInputMessage(this)', 'onkeypress' => 'settings.searchPlaylists(event)']))->buildElement(); ?>
				<button type="button" onclick="settings.searchPlaylists()">Search</button>
				<a href="/rips?playlist=create" style="float:right"><button type="button">Create Playlist</button></a>
				<div>
					<h4>Selected Playlists</h4>
					<div id="selected-playlists" class="guesser-playlists">

					</div>
					<hr>
				</div>
				<div id="playlist-selector" class="guesser-playlists"></div>
				<br>
				<div class="guesser-playlists">
					<button id="show-more" type="button" style="grid-column-start:2" onclick="settings.showMorePlaylists(this)">More +</button>
				</div>
			</fieldset>
			<div style="grid-column:span 2">
				<button id="start-game" type="submit" style="margin: 10px auto;display: block;padding: 10px;font-size: larger;">Play</button>
			</div>
		</form>
		<section id="help" style="display:none">
			<details class="example">
				<summary>I keep getting rips with jokes I don't know!</summary>
				<p>You can specify <strong>meta jokes</strong> (e.g. artists, bands, franchises) to limit what rips will be selected to you.<br>
					Try entering some that you're familiar with.</p>
				<p>You can also filter by a <string>meta tag</strong> (e.g. jokes from a "Video Game", "Viral Video", "Anime" etc.) if you want to explore a specific genre/field.</p>
			</details>
			<details class="example">
				<summary>The number of rounds I selected weren't played.</summary>
				<p>If you've applied filters, that means <strong>there are not enough rips</strong> in the database that match the specified filters to play all the rounds you requested.<br>
					In other words, this means you've played all the rips that contain those filters!</p>
			</details>
			<details class="example">
				<summary>The game appears to be stuck and nothing is loading on-screen.</summary>
				<p>If you've managed to make this occur, that's not good!<br>
					To remedy this situation, you can try any of the following:</p>
				<ul>
					<li>Clear <strong>this site's</strong> cookies. You may have somehow launched multiple games in one session! (The game's session is stored as a cookie).</li>
					<li>Try hard-refreshing the page. This is done by pressing <kbd>ctrl</kbd> + <kbd>shift</kbd> + <kbd>r</kbd>, or <kbd>ctrl</kbd> + <kbd>F5</kbd>.</li>
					<li>Try another browser. Not an ideal solution, but this should start a new game session.</li>
				</ul>
				<p>Although this should never happen, if it does, please <strong>submit a bug report on the <a href="https://github.com/Mr-Kaos/The-Rip-DB">project's GitHub</a></strong>.</p>
			</details>
		</section>
	</div>
</main>
<div id="templates" style="display:none">
	<div class="template-playlist">
		<input type="checkbox"">
		<label class="btn-plist">
			<span>
				<strong>Unnamed</strong><br>
				<em data-name="">By Unknown User</em><br>
				<em data-count="">0 Rips</em>
			</span>
			<a href="javascript:void(0)">View</a>
		</label>
	</div>
	<div class="template-playlist-selected">
		<span></span>
		<input type="hidden" name="playlists[]">
		<a href="javascript:void(0)">&times;</a>
	</div>
</div>
<img src="/res/img/loading.gif" style="display:none">
<script src="https://www.youtube.com/iframe_api" defer></script>
<script src="/res/js/ripguesser.js" defer></script>
<script>
	let clone = document.getElementById('help').cloneNode(true);
	clone.style.display = null;
	let helpModal = new Modal('help-modal', 'Help/FAQ', clone, '90%');
</script>