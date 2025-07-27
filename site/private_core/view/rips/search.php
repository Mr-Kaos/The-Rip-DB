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
		<a href="#" style="display:inline;float:right" onclick="initPlaylistCreator()">Create Playlist</a>
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
	<div id="playlist-creator" style="text-align:center">
		<form action="/playlist/new" method="POST" onsubmit="playlist.submitPlaylist(event)">
			<h2>Playlist Summary</h2>
			<?= (new o\InputElement(null, o\InputTypes::text, ['id' => 'playlist-name', 'max-length' => 128, 'required' => true, 'oninput' => 'playlist.updateName(this.value)']))->buildElement() ?>
			<details class="playlist-rips" open>
				<summary id="rips">Show Rips</summary>
			</details>
			<button type="submit">Create Playlist</button>
		</form>
	</div>
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