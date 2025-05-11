<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Rips</h1>
	<form id="rip_search" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
		<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
		<?= (new o\InputElement('Search by secondary (album) name', o\InputTypes::checkbox, ['name' => 'use_secondary', 'value' => 1, 'checked' => ($_GET['use_secondary'] ?? null) == 1]))->buildElement() ?>
		<details <?= $open ?>>
			<summary>More Filters</summary>
			<?= (new o\SearchElement('Tags', '/search/tags', true, $tags, ['name' => 'tags']))->buildElement() ?>
			<?= (new o\SearchElement('Jokes', '/search/jokes', true, $jokes, ['name' => 'jokes']))->buildElement() ?>
			<?= (new o\SearchElement('Games', '/search/games', true, $games, ['name' => 'games']))->buildElement() ?>
			<?= (new o\SearchElement('Rippers', '/search/rippers', true, $rippers, ['name' => 'rippers']))->buildElement() ?>
			<?= (new o\SearchElement('RipGenres', '/search/genres', true, $genres, ['name' => 'genres']))->buildElement() ?>
		</details>
	</form>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Rip Name</th>
				<th>Alternative Name</th>
				<th>Length</th>
				<th>Ripper</th>
				<th>Jokes</th>
				<th>Genres</th>
				<th>Upload Date</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><a href="/rips/<?= $record['RipID'] ?>"><?= $record['RipName'] ?></a></td>
						<td><?= $record['RipAlternateName'] ?></td>
						<td><?= $record['RipLength'] ?></td>
						<td>
							<?php foreach ($record['Rippers'] as $ripper): ?>
								<button type="button" onclick="openRowBubble(this)" data-type="rippers" data-id="<?= $ripper['RipperID'] ?>"><?= $ripper['RipperName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td>
							<?php foreach ($record['Jokes'] as $joke): ?>
								<button type="button" onclick="openRowBubble(this)" data-type="jokes" data-id="<?= $joke['JokeID'] ?>" data-extra="<?= htmlspecialchars(json_encode($joke['Tags'])) ?>"><?= $joke['JokeName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td>
							<?php foreach ($record['Genres'] as $genre): ?>
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
						<?= (new o\InputElement('Rips per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'rip_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'rip_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="rip_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $RipCount ?> rips</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>
<script src="/res/js/results.js" defer></script>