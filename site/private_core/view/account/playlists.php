<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Account</h1>
	<div class="sidebar-split">
		<?php include('private_core/templates/account_nav.php'); ?>
		<div>
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
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($results as $record): ?>
							<tr>
								<td><a href="/rips?playlist=<?= $record['ShareCode'] ?>"><?= $record['PlaylistName'] ?></a></td>
								<td style="text-align:center"><?= $record['ShareCode'] ?></td>
								<td style="text-align:center"><?= (new o\InputElement(null, o\InputTypes::checkbox, ['checked' => $record['IsPublic'] == 1, 'disabled' => true]))->buildElement() ?></td>
								<td style="text-align:center"><?= $record['Created'] ?></td>
								<td style="text-align:center"><?= $record['RipCount'] ?></td>
								<td style="text-align:center"><button class="btn-bad" type="button" onclick="deletePlaylist('<?= $record['ShareCode'] ?>')">Delete</button></td>
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
				<p>You have no playlists. Visit the <a href="/rips?playlist=create">Rips page</a> to create some!</p>
			<?php endif; ?>
			<hr>
			<h3>Import Playlist from YouTube</h3>
			<p>You can import a YouTube playlist by pasting the URL of the playlist below.</p>
			<form action="/account/playlists/import" method="post">
				<?= (new o\InputElement('Playlist URL', o\InputTypes::url, ['name' => 'playlist_url', 'placeholder' => 'https://www.youtube.com?list=', 'pattern' => 'https:\/\/.*youtu.*\?.*list=([a-zA-Z_\-0-9]{34}).*']))->buildElement() ?>
				<button type="submit">Import</button>
			</form>
			<p><em>Only public or unlisted playlists can be imported. Only videos of rips that exist in this database can be imported.</em></p>
			<details>
				<summary>Claim Unsaved/Import Playlists</summary>
				<h3>Claim Unsaved Playlist</h3>
				<p>Use this form to claim a playlist that was saved while not logged in.</p>
				<form action="/account/playlists/claim" method="post">
					<fieldset>
						<legend>Enter your playlist's claim code below</legend>
						<?= (new o\InputElement('Claim Code', o\InputTypes::text, ['name' => 'code', 'maxlength' => 8, 'minlength' => 8, 'pattern' => '[0-9a-zA-Z]{8}', 'required' => true]))->buildElement() ?>
					</fieldset>
					<button type="submit">Claim</button>
				</form>
				<p><em>If you've lost your claim code, you will need to create a new playlist. Sorry!</em></p>
			</details>
		</div>
	</div>
</main>