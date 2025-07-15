<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Channels</h1>
	<p>This page shows all ripping channels in the database.</p>
	<div>
		<form id="table_search" method="GET">
			<?= (new o\InputElement('Search', o\InputTypes::button, ['type' => 'submit']))->buildElement() ?>
			<?= (new o\InputElement(null, o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null, 'autocomplete' => 'off']))->buildElement() ?>
			<a href="channels/new" style="display:inline;float:right">Add Channel</a>
		</form>
	</div>
	<table id="results" class="table-search">
		<thead>
			<tr>
				<th>Channel Name</th>
				<th>Channel URL</th>
				<th>Description</th>
				<th>Rip Count</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($results)): ?>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><a href="channels/edit/<?= $record['ChannelID'] ?>"><?= $record['ChannelName'] ?></a></td>
						<td style="text-align:center"><a href="<?= $record['ChannelURL'] ?>" target="_blank"><?= $record['ChannelURL'] ?></a></td>
						<td style="text-align:center"><?= $record['ChannelDescription'] ?></td>
						<td style="text-align:center"><?= $record['RipCount'] ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="3" style="text-align: center;padding:10px 0px;" ;>No channels were found with the given criteria.</td>
				</tr>
			<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align:center" class="pagination">
					<span style="float:left">
						<?= (new o\InputElement('Channels per page:', o\InputTypes::number, ['id' => 'c', 'min' => 1, 'max' => 100, 'form' => 'table_search', 'value' => $Count]))->buildElement() ?>
					</span>
					<?= $pagination ?>
					<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'table_search', 'value' => $Page, 'onchange' => 'this.form.submit()']))->buildElement() ?>
					<button type="submit" form="table_search">Go</button>
					<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $RecordCount ?> channels</span>
				</td>
			</tr>
		</tfoot>
	</table>
</main>