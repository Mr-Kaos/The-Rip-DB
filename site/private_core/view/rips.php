<?php

use RipDB\Objects as o;

require('private_core/controller/RipsController.php');

$controller = new RipDB\RipsController();
?>

<main>
	<h1>Rips</h1>

	<form id="rip_search" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::search, ['id' => 'search']))->buildElement() ?>
		<?= (new o\InputElement('Search by secondary (album) name', o\InputTypes::checkbox, ['name' => 'use_secondary']))->buildElement() ?>
	</form>

	<?php if (!empty($controller->getData('results'))): ?>
		<table id="results">
			<thead>
				<tr>
					<th>Rip Name</th>
					<th>Alternative Name</th>
					<th>Ripper</th>
					<th>Jokes</th>
					<th>Upload Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($controller->getData('results') as $record): ?>
					<tr>
						<td><a href="/rip/<?= $record['RipID'] ?>"><?= $record['RipName'] ?></a></td>
						<td><?= $record['RipAlternateName'] ?></td>
						<td></td>
						<td></td>
						<td><?= $record['RipDate'] ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" style="text-align:center">
						<?= (new o\InputElement(null, o\InputTypes::number, ['id' => 'p', 'min' => 1, 'placeholder' => 'Page number', 'form' => 'rip_search', 'value' => $controller->getData('Page')]))->buildElement() ?>
						<button type="submit" form="rip_search">Go</button>
						<span>Showing <?= $controller->getData('RecordStart') ?> to <?= $controller->getData('RecordEnd') ?></span>
					</td>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
</main>