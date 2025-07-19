<?php

use RipDB\Objects as o;
?>
<main>
	<?php include('private_core/templates/header-tag-metas.php') ?>
	<h1>Metas</h1>
	<p>This page shows what metas meta jokes exist.</p>
	<form id="table_search" style="display:inline" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
		<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
		<a href="metas/new" style="display:inline;float:right">Add Meta</a>
	</form>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Meta Name</th>
				<th>Associated Meta Jokes</th>
				<th>Rips With This Meta</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><a href="/metas/edit/<?= $record['MetaID'] ?>"><?= $record['MetaName'] ?></a></td>
						<td>
							<?php
							foreach ($record['MetaJokes'] as $id => $metaJoke):
								echo '<button type="button" onclick="openRowBubble(this)" data-type="meta-jokes" data-id="' . $id . '">' . $metaJoke['MetaJokeName'] . '</button>';
							endforeach;
							?>
						</td>
						<td style="text-align:center"><a href="/jokes?metas[]=<?= $record['MetaID'] ?>"><?= $record['AssociatedRips'] ?></a></td>
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
						<?= (new o\InputElement('Metas per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'table_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'table_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="table_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $RecordCount ?> metas</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>
<section id="templates" style="display:none">
	<div id="callout-meta-jokes" class="callout down">
		<a href="#">Find rips with this meta joke</a>
	</div>
</section>
<script src="/res/js/results.js" defer></script>