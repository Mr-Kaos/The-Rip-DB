<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Tags</h1>
	<p>This page just shows what tags exist in the database.</p>
	<form id="rip_search" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
		<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
		<details>
			<summary>More Filters</summary>
			<?= (new o\InputElement('All Tags', o\InputTypes::radio, ['id' => 'meta_all', 'name' => 'mode', 'value' => 'any', 'checked' => !in_array(($_GET['mode'] ?? null), ['standard', 'meta'])]))->buildElement() ?><br>
			<?= (new o\InputElement('Standard Tags only', o\InputTypes::radio, ['id' => 'meta_std', 'name' => 'mode', 'value' => 'standard', 'checked' => ($_GET['mode'] ?? null) == 'standard']))->buildElement() ?><br>
			<?= (new o\InputElement('Meta Tags only', o\InputTypes::radio, ['id' => 'meta_meta', 'name' => 'mode', 'value' => 'meta', 'checked' => ($_GET['mode'] ?? null) == 'meta']))->buildElement() ?>
		</details>
	</form>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Tag Name</th>
				<th>Is Meta Tag</th>
				<th>Joke Count</th>
				<th>Rip Count</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><?= $record['TagName'] ?></td>
						<td style="text-align:center"><?= $record['MetaOnly'] ? 'Yes' : 'No' ?></td>
						<td style="text-align:center"><?= $record['JokeCount'] ?></td>
						<td style="text-align:center"><?= $record['RipCount'] ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="7" style="text-align: center;padding:10px 0px;" ;>No tags were found with the given criteria.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7" style="text-align:center" class="pagination">
					<span style="float:left">
						<?= (new o\InputElement('Tags per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'rip_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'rip_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="rip_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $TagCount ?> tags</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>