<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Jokes</h1>
	<p>This page just shows what jokes exist in the database.</p>
	<form id="rip_search" style="display:inline" method="GET">
		<?= (new o\InputElement('Search', o\InputTypes::search, ['id' => 'search', 'value' => $_GET['search'] ?? null]))->buildElement() ?>
	</form>
	<a href="jokes/new" style="display:inline;float:right">Add Joke</a>
	<?php if (!empty($results)): ?>
		<table id="results" class="table-search">
			<thead>
				<tr>
					<th>Joke Name</th>
					<th>Description</th>
					<th>Tags</th>
					<th>Rips With This Joke</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($results as $record): ?>
					<tr>
						<td><?= $record['JokeName'] ?></td>
						<td><?= $record['JokeDescription'] ?></td>
						<td>
							<?php
							foreach ($record['Tags'] as $tag):
								if ($tag['IsPrimary']) {
									echo '<button type="button"><b>' . $tag['TagName'] . '</b></button>';
								} else {
									echo '<button type="button">' . $tag['TagName'] . '</button>';
								}
							endforeach;
							?>
						</td>
						<td><a href="rips?jokes[]=<?= $record['JokeID'] ?>"><?= $record['RipCount'] ?></a></td>
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
						<span style="float:right">Showing <b><?= $RecordStart ?> to <?= $RecordEnd ?></b> of <?= $JokeCount ?> jokes</span>
					</td>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
</main>