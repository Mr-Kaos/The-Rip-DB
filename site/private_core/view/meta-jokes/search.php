<?php

use RipDB\Objects as o;
?>
<main>
	<?php include('private_core/templates/header-tag-metas.php') ?>
	<h1>Meta Jokes</h1>
	<p>This page shows the meta jokes that jokes are categorised within.</p>
	<form id="table_search" style="display:inline" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
		<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
		<a href="meta-jokes/new" style="display:inline;float:right">Add Meta Joke</a>
		<summary onclick="toggleFilters(this)" class="filters">More Filters</summary>
		<div class="filters" <?= $open ?>>
			<?= (new o\SearchElement('Metas', '/search/metas', true, $metas, ['name' => 'metas', 'autocomplete' => 'off']))->buildElement() ?>
		</div>
	</form>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Meta Joke Name</th>
				<th>Meta</th>
				<th>Associated Jokes</th>
				<th>Rips With This Meta Joke</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><a href="/meta-jokes/edit/<?= $record['MetaJokeID'] ?>"><?= $record['MetaJokeName'] ?></a></td>
						<td>
							<?php if ($record['MetaID'] !== null): ?>
								<button type="button" onclick="openRowBubble(this)" data-type="metas" data-id="<?= $record['MetaID'] ?>" data-extra="<?= htmlspecialchars(json_encode($record['MetaID'])) ?>"><?= $record['MetaName'] ?></button>
							<?php endif; ?>
						</td>
						<td>
							<?php
							foreach ($record['Jokes'] as $jokeID => $joke):
								echo '<button type="button" onclick="openRowBubble(this)" data-type="jokes" data-id="' . $jokeID . '">' . $joke['JokeName'] . '</button>';
							endforeach;
							?>
						</td>
						<td style="text-align:center"><a href="rips?meta-jokes[]=<?= $record['MetaJokeID'] ?>"><?= $record['AssociatedJokes'] ?></a></td>
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
						<?= (new o\InputElement('Meta Jokes per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'table_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'table_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="table_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $RecordCount ?> meta jokes</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>
<section id="templates" style="display:none">
	<div id="callout-jokes" class="callout down">
		<a href="#">Find rips with this joke</a>
	</div>
	<div id="callout-metas" class="callout down">
		<a href="#">Find Rips with this meta</a>
	</div>
</section>
<script src="/res/js/results.js" defer></script>