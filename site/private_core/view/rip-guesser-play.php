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
				<?= (new o\InputElement(null, o\InputTypes::search, ['name' => 'playlist-search', 'form' => '', 'placeholder' => 'Search Playlists']))->buildElement(); ?>
				<?= (new o\InputElement(null, o\InputTypes::search, ['name' => 'playlist-code', 'form' => '', 'placeholder' => 'Find by Playlist Code']))->buildElement(); ?>
				<a href="/rips?playlist=create" style="float:right"><button type="button" id="show-more">Create Playlist</button></a>
				<hr>
				<div id="playlist-selector" class="guesser-playlists"></div>
				<br>
				<div class="guesser-playlists">
					<button type="button" style="grid-column-start:2" onclick="showMorePlaylists(this)">More +</button>
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
	<div class="btn-plist">
		<input type="checkbox" name="playlists[]">
		<label>
			<span>
				<strong>Unnamed</strong><br>
				<em data-name="">By Unknown User</em><br>
				<em data-count="">0 Rips</em>
			</span>
			<a href="javascript:showPlaylist(null)">View</a>
		</label>
	</div>
</div>
<img src="/res/img/loading.gif" style="display:none">
<script src="https://www.youtube.com/iframe_api" defer></script>
<script src="/res/js/ripguesser.js" defer></script>
<script>
	let clone = document.getElementById('help').cloneNode(true);
	clone.style.display = null;
	let helpModal = new Modal('help-modal', 'Help/FAQ', clone, '90%');

	function showPlaylist(id) {
		if (Number.isInteger(id)) {
			let modal = new PageModal('playlist-preview', 'Preview Playlist', `/playlist/view/${id}`);
			modal.open();
		}
	}

	let page = 0;
	let listsPerPage;
	async function showMorePlaylists(button) {
		let request = await fetch(`/ripguessr/setup/playlists-more?page=${page}`, {
			method: 'GET'
		});

		// If response is ok, build the cells for the next few playlists.
		if (request.ok) {
			let data = await request.json();
			// Set the number of playlists retrieved per page to the amount received from the server.
			// This allows the constants defined in the server to be used here.
			if (listsPerPage == null) {
				listsPerPage = data.length;
			}

			let playlistContainer = document.getElementById('playlist-selector');
			let template = document.querySelector('#templates>.btn-plist');

			for (let i = 0; i < data.length; i++) {
				let plist = template.cloneNode(true);
				let input = plist.querySelector('input[type=checkbox]');
				let label = plist.querySelector('label');
				let name = label.querySelector('strong');
				let creator = label.querySelector('em[data-name]');
				let count = label.querySelector('em[data-count');

				name.innerText = data[i].PlaylistName;
				creator.innerText = 'By ' + data[i].Username;
				count.innerText = data[i].RipCount + ' Rips';

				// Set input values.
				input.id = `playlist-${data[i].PlaylistID}`;
				input.value = data[i].PlaylistID;
				label.setAttribute('for', `playlist-${data[i].PlaylistID}`);

				playlistContainer.append(plist);
			}

			if (data.length < listsPerPage) {
				button.innerText = 'There are no more playlists available.';
				button.disabled = true;
			} else {
				button.disabled = false;
				console.log(button);
			}

			page++;
		}
	}

	/**
	 * Searches playlists asynchronously and displays the matching results in the playlists box.
	 */
	function searchPlaylists(name) {

	}

	showMorePlaylists(document.getElementById('show-more'));
</script>