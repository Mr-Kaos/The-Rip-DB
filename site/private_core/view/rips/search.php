<?php

use RipDB\Objects as o;
use RipDB\Model\RipModel as r;
?>
<main>
	<h1>Rips</h1>
	<hr>
	<form id="table_search" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
		<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
		<?= (new o\InputElement('Search by secondary (album) name', o\InputTypes::checkbox, ['name' => 'use_secondary', 'value' => 1, 'checked' => ($_GET['use_secondary'] ?? null) == 1]))->buildElement() ?>
		<a href="rips/new" style="display:inline;float:right">Add Rip</a>
		<br>
		<a id="playlist-toggle" href="#" style="display:inline;float:right" onclick="togglePlaylistCreator()">Create Playlist</a>
		<summary onclick="toggleFilters(this)" class="filters">More Filters</summary>
		<div class="filters" <?= $open ?>>
			<?= (new o\SearchElement('Tags', '/search/tags', true, $tags, ['name' => 'tags', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Jokes', '/search/jokes', true, $jokes, ['name' => 'jokes', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Games', '/search/games', true, $games, ['name' => 'games', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Rippers', '/search/rippers', true, $rippers, ['name' => 'rippers', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Rip Genres', '/search/genres', true, $genres, ['name' => 'genres', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Meta Jokes', '/search/meta-jokes', true, $metaJokes, ['name' => 'meta-jokes', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Metas', '/search/metas', true, $metas, ['name' => 'metas', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Channel', '/search/channels', false, $channel, ['name' => 'channel', 'autocomplete' => 'off']))->buildElement() ?>
		</div>
	</form>
	<table id="results" class="table-search" data-for="table_search">
		<thead>
			<tr>
				<th id="col-<?= r::SORT_RipName ?>" <?= empty($sort[r::SORT_RipName]) ? '' : 'data-sort="' . $sort[r::SORT_RipName] . '" data-ord="' . array_search(r::SORT_RipName, array_keys($sort)) . '"' ?>>Rip Name</th>
				<th id="col-<?= r::SORT_RipAlternateName ?>" <?= empty($sort[r::SORT_RipAlternateName]) ? '' : 'data-sort="' . $sort[r::SORT_RipAlternateName] . '" data-ord="' . array_search(r::SORT_RipAlternateName, array_keys($sort)) . '"' ?>>Alternative Name</th>
				<th id="col-<?= r::SORT_RipLength ?>" <?= empty($sort[r::SORT_RipLength]) ? '' : 'data-sort="' . $sort[r::SORT_RipLength] . '" data-ord="' . array_search(r::SORT_RipLength, array_keys($sort)) . '"' ?>>Length</th>
				<th>Ripper</th>
				<th>Jokes</th>
				<th>Genres</th>
				<th id="col-<?= r::SORT_RipDate ?>" <?= empty($sort[r::SORT_RipDate]) ? '' : 'data-sort="' . $sort[r::SORT_RipDate] . '" data-ord="' . array_search(r::SORT_RipDate, array_keys($sort)) . '"' ?>>Upload Date</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr rip-id="<?= $record['RipID'] ?>">
						<td><a href="/rips/<?= $record['RipID'] ?>"><?= $record['RipName'] . ' - ' . $record['GameName'] ?></a></td>
						<td>
							<?php if (!empty($record['RipAlternateURL'])): ?>
								<a href="<?= $record['RipAlternateURL']; ?>" target="_blank"><?= $record['RipAlternateName'] ?></a>
							<?php else: ?>
								<?= $record['RipAlternateName'] ?>
							<?php endif; ?>
						</td>
						<td><?= $record['RipLength'] ?></td>
						<td>
							<?php foreach ($record['Rippers'] ?? [] as $ripper): ?>
								<button type="button" onclick="openRowBubble(this)" data-type="rippers" data-id="<?= $ripper['RipperID'] ?>"><?= $ripper['RipperName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td>
							<?php foreach ($record['Jokes'] ?? [] as $joke): ?>
								<button type="button" onclick="openRowBubble(this)" data-type="jokes" data-id="<?= $joke['JokeID'] ?>" data-extra="<?= htmlspecialchars(json_encode($joke['Tags'])) ?>"><?= $joke['JokeName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td>
							<?php foreach ($record['Genres'] ?? [] as $genre): ?>
								<button type="button" onclick="openRowBubble(this)" data-type="genres" data-id="<?= $genre['GenreID'] ?>"><?= $genre['GenreName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td><?= date_format(new DateTime($record['RipDate']), 'Y/m/d') ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="7" style="text-align: center;padding:10px 0px;" ;>No rips were found with the given criteria.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7" style="text-align:center" class="pagination">
					<span style="float:left">
						<?= (new o\InputElement('Rips per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'table_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'table_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="table_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $RipCount ?> rips</span>
				</td>
			</tr>
		</tfoot>
	</table>
	<div id="playlist-creator" style="display:none" class="playlist-creator">
		<form action="/playlist/new" method="POST" onsubmit="playlist.submitPlaylist(event)">
			<h2>Playlist Editor</h2>
			<fieldset>
				<legend>Playlist Details</legend>
				<?= (new o\InputElement('Playlist Name', o\InputTypes::text, ['id' => 'playlist-name', 'max-length' => 64, 'required' => true, 'oninput' => 'playlist.updateName(this.value)', 'no-asterisk' => true], null, true))->buildElement() ?>
				<?php if (\RipDB\checkAuth()): ?>
					<div>
						<?= (new o\InputElement('Public Playlist?', o\InputTypes::checkbox, ['id' => 'playlist-public', 'oninput' => 'playlist.updatePublicity(this.checked)', 'title' => 'Public playlists are searchable by anyone in RipGuessr.']))->buildElement() ?>
					</div>
					<?= (new o\InputElement('Description', o\InputTypes::textarea, ['id' => 'playlist-desc', 'max-length' => 512, 'oninput' => 'playlist.updateDesc(this.value)'], null, true))->buildElement() ?>
				<?php endif; ?>
			</fieldset>
			<div>
				<details open>
					<summary id="rips" style="width:100%">Show Rips
						<button type="button" class="btn-bad" style="float:right" onclick="playlist.promptClear()">Clear Playlist</button>
					</summary>
					<div class="playlist-rips"></div>
				</details>
				<button type="submit" style="float:left">Save Playlist</button>
				<?php if (\RipDB\checkAuth()): ?>
					<button type="button" class="btn-warn" style="float:right" onclick="cancelEdits()">Cancel Edits</button>
				<?php endif; ?>
			</div>
		</form>
		<div id="playlist-modal-msg" style="display:none;text-align:center">
			<p>Your playlist has been successfully created!</p>
			<p>Use the code below to share it with others:</p>
			<code id="share-code" style="font-weight:bold;font-size:larger">ERROR OBTAINING CODE!</code>
			<div>
				<hr>
				<p>If you want to edit this playlist, create an account and use the code below to claim it.</p>
				<code id="claim-code" style="font-weight:bold;font-size:larger">ERROR OBTAINING CODE!</code><br><br>
				<i>Note that this playlist will be deleted if not claimed or used for 30 days.</i>
			</div>
		</div>
		<br>
	</div>
	<br>
</main>
<section id="templates" style="display:none">
	<div id="callout-rippers" class="callout down">
		<a href="#">Find Rips by this ripper</a>
	</div>
	<div id="callout-genres" class="callout down">
		<a href="#">Find rips with this genre</a>
	</div>
	<div id="callout-jokes" class="callout down">
		<a href="#">Find rips with this joke</a>
		<div class="extras">
			<p>Or search by its tags:</p>
		</div>
	</div>
</section>
<script src="/res/js/results.js" defer></script>
<script src="/res/js/playlistCreator.js" defer></script>