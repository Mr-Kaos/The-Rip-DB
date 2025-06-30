<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Jokes</h1>
	<p>This page just shows what jokes exist in the database.</p>
	<form id="rip_search" style="display:inline" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
		<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
		<a href="jokes/new" style="display:inline;float:right">Add Joke</a>
		<summary onclick="toggleFilters(this)" class="filters">More Filters</summary>
		<div class="filters" <?= $open ?>>
			<?= (new o\SearchElement('Meta Jokes', '/search/meta-jokes', true, $metaJokes, ['name' => 'metajokes', 'autocomplete' => 'off']))->buildElement() ?>
			<?= (new o\SearchElement('Metas', '/search/metas', true, $metas, ['name' => 'metas', 'autocomplete' => 'off']))->buildElement() ?>
		</div>
	</form>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Joke Name</th>
				<th>Tags</th>
				<th>Meta Jokes</th>
				<th>Rips With This Joke</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><?= $record['JokeName'] ?></td>
						<td>
							<?php
							foreach ($record['Tags'] as $tagId => $tag):
								if ($tag['IsPrimary']) {
									echo '<button type="button" onclick="openRowBubble(this)" data-type="tags" data-id="' . $tagId . '" style="font-weight:bold">' . $tag['TagName'] . '</button>';
								} else {
									echo '<button type="button" onclick="openRowBubble(this)" data-type="tags" data-id="' . $tagId . '">' . $tag['TagName'] . '</button>';
								}
							endforeach;
							?>
						</td>
						<td>
							<?php foreach ($record['MetaJokes'] as $metaJokeId => $metaJoke): ?>
								<button type="button" onclick="openRowBubble(this)" data-type="meta-jokes" data-id="<?= $metaJokeId ?>" data-extra="<?= htmlspecialchars(json_encode($metaJoke['Metas'])) ?>"><?= $metaJoke['MetaJokeName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td style="text-align:center"><a href="rips?jokes[]=<?= $record['JokeID'] ?>"><?= $record['RipCount'] ?></a></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="5" style="text-align: center;padding:10px 0px;" ;>No jokes were found with the given criteria.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" style="text-align:center" class="pagination">
					<span style="float:left">
						<?= (new o\InputElement('Jokes per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'rip_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'rip_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="rip_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $JokeCount ?> jokes</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>
<section id="templates" style="display:none">
	<div id="callout-tags" class="callout down">
		<a href="#">Find Rips that have this tag in its joke</a>
	</div>
	<div id="callout-meta-jokes" class="callout down">
		<a href="#">Find Rips with this meta joke</a>
		<div class="extras">
			<p>Or search by its meta:</p>
		</div>
	</div>
</section>
<script src="/res/js/results.js" defer></script>