<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Rips</h1>
	<form id="rip_search" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
		<?= (new o\InputElement('Search by secondary (album) name', o\InputTypes::checkbox, ['name' => 'use_secondary', 'value' => 1, 'checked' => ($_GET['use_secondary'] ?? null) == 1]))->buildElement() ?>
		<details>
			<summary>More Filters</summary>
			<?= (new o\SearchElement('Tags', '/search/tags', true, $tags, ['name' => 'tags']))->buildElement() ?>
			<?= (new o\SearchElement('Jokes', '/search/jokes', true, $jokes, ['name' => 'jokes']))->buildElement() ?>
		</details>
	</form>
	<?php if (!empty($results)): ?>
		<table id="results" class="table-search">
			<thead>
				<tr>
					<th>Rip Name</th>
					<th>Alternative Name</th>
					<th>Length</th>
					<th>Ripper</th>
					<th>Jokes</th>
					<th>Upload Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><a href="/rips/<?= $record['RipID'] ?>"><?= $record['RipName'] ?></a></td>
						<td><?= $record['RipAlternateName'] ?></td>
						<td><?= $record['RipLength'] ?></td>
						<td>
							<?php foreach ($record['Rippers'] as $ripper): ?>
								<button type="button"><?= $ripper['RipperName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td>
							<?php foreach ($record['Jokes'] as $joke): ?>
								<button type="button"><?= $joke['JokeName'] ?></button>
							<?php endforeach; ?>
						</td>
						<td><?= $record['RipDate'] ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6" style="text-align:center" class="pagination">
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
	<?php endif; ?>
</main>