<?php
// This page should be embedded in the /playlists/edit page within the "playlists" sub-page.
use RipDB\Objects as o;
?>
<?php if (!empty($results)): ?>
	<div>
		<form id="table_search" method="GET">
			<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
			<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null, 'autocomplete' => 'off']))->buildElement() ?>
		</form>
	</div>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Playlist Name</th>
				<th>Share Code</th>
				<th>Public?</th>
				<th>Created</th>
				<th>Rips</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($results as $record): ?>
				<tr>
					<td><a href="/rips?playlist=<?= $record['ShareCode'] ?>"><?= $record['PlaylistName'] ?></a></td>
					<td style="text-align:center"><?= $record['ShareCode'] ?></td>
					<td style="text-align:center"><?= (new o\InputElement(null, o\InputTypes::checkbox, ['checked' => $record['IsPublic'] == 1]))->buildElement() ?></td>
					<td style="text-align:center"><?= $record['Created'] ?></td>
					<td style="text-align:center"><?= $record['RipCount'] ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" style="text-align:center" class="pagination">
					<span style="float:left">
						<?= (new o\InputElement('Playlists per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'table_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'table_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="table_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $TagCount ?> playlists</span>
				</td>
			</tr>
		</tfoot>
	</table>
<?php else: ?>
	<p>You have no playlists. Visit the <a href="/rips">Rips page</a> to create some!</p>
<?php endif; ?>