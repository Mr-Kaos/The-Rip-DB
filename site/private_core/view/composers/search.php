<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Composers</h1>
	<p>This page just shows what composers exist in the database.</p>
	<div>
		<form id="table_search" method="GET">
			<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
			<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null, 'autocomplete' => 'off']))->buildElement() ?>
			<a href="composers/new" style="display:inline;float:right">Add Composer</a>
		</form>
	</div>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Composer Name</th>
				<th>Alternate Spelling</th>
				<th>Rip Count</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><a href="/composers/edit/<?= $record['ComposerID'] ?>"><?= $record['ComposerFirstName'] . ' ' . $record['ComposerLastName'] ?></a></td>
						<td><?= $record['ComposerFirstNameAlt'] . ' ' . $record['ComposerLastNameAlt'] ?></td>
						<td style="text-align:center"><?= $record['RipCount'] ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="3" style="text-align: center;padding:10px 0px;" ;>No composers were found with the given criteria.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" style="text-align:center" class="pagination">
					<span style="float:left">
						<?= (new o\InputElement('Composers per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'table_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'table_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="table_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $ComposerCount ?> composers</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>