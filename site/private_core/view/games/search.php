<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Games</h1>
	<p>This page shows what games exist in the database and hwo many rips exist for them.</p>
	<div>
		<form id="table_search" method="GET">
			<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
			<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null, 'autocomplete' => 'off']))->buildElement() ?>
			<a href="games/new" style="display:inline;float:right">Add Game</a>
		</form>
	</div>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Game Name</th>
				<th>Fake Game?</th>
				<th>Rip Count</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><a href="/games/edit/<?= $record['GameID']?>"><?= $record['GameName'] ?></a></td>
						<td style="text-align:center"><?= $record['IsFake'] ?></td>
						<td style="text-align:center"><a href="rips?games[]=<?= $record['GameID'] ?>"><?= $record['RipCount'] ?></a></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="3" style="text-align: center;padding:10px 0px;" ;>No games were found with the given criteria.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" style="text-align:center" class="pagination">
					<span style="float:left">
						<?= (new o\InputElement('Games per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'table_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'table_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="table_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $RecordCount ?> games</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>