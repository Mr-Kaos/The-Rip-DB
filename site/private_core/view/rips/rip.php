<main>
	<?php if (empty($rip)) : ?>
		<h2>This rip does not exist.</h2>
	<?php else: ?>
		<section id="summary">
			<h1 id="title"><?= $rip['RipName'] ?><?= empty($rip['MixName']) ? '' : ' (' . $rip['MixName'] . ')' ?> - <?= $rip['GameName'] ?></h1>
			<a href="/rips/edit/<?= $rip['RipID']; ?>">Edit Rip</a>
		</section>
		<div class="rip-container">
			<section id="jokes">
				<h3>Rip Description</h3>
				<?php if (empty($rip['RipDescription'])): ?>
					<p><em>No description exists for this rip.</em></p>
				<?php else: ?>
					<p id="data-Sescription"><?= $rip['RipDescription'] ?></p>
				<?php endif; ?>
				<h3>Jokes</h3>
				<?php if (!empty($jokes)): ?>
					<table style="width:100%">
						<caption>Jokes In This Rip:</caption>
						<thead>
							<tr>
								<th>Timestamp</th>
								<th>Joke</th>
								<th>Genre</th>
								<th>Comment</th>
							</tr>
						</thead>
						<tbody id="data-Jokes">
							<?php foreach ($jokes as $joke): ?>
								<tr>
									<td><?php if (isset($joke['start'])) {
											if (!empty($rip['RipYouTubeID'])) {
												echo '<a href="#" onclick="setYouTubeTimestamp(\'' . $joke['start'] . '\')">' . $joke['start'] . ' -> ' . $joke['end'] . '</a>';
											} else {
												echo $joke['start'] . ' -> ' . $joke['end'];
											}
										} ?>
									</td>
									<td><a href="/rips?jokes[]=<?= $joke['JokeID'] ?>"><?= $rip['Jokes'][$joke['JokeID']]['JokeName'] ?></a></td>
									<td><a href="/rips?genres[]=<?= $rip['Jokes'][$joke['JokeID']]['GenreID'] ?>"><?= $rip['Jokes'][$joke['JokeID']]['GenreName'] ?></a></td>
									<td><?= $rip['Jokes'][$joke['JokeID']]['JokeComment'] ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<p>This rip has no jokes documented yet.</p>
					<p>Why not be the first to <a href="/rips/edit/<?= $rip['RipID']; ?>">add them</a>?</p>
				<?php endif; ?>
			</section>
			<section id="other">
				<table style="width:100%">
					<tbody>
						<tr>
							<th>Video</th>
							<td style="text-align:center">
								<?php if (!empty($rip['RipYouTubeID'])): ?>
									<iframe class="rip-embed" id="yt-embed" src="https://www.youtube-nocookie.com/embed/<?= $rip['RipYouTubeID']; ?>?vq=240p" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
								<?php endif; ?>
								<a id="data-ytURL" href="<?= $rip['RipURL'] ?>" target="_blank">YouTube</a>
							</td>
						</tr>
						<tr>
							<th>Upload Date</th>
							<td id="data-UploadDate"><?= date_format(new DateTime($rip['RipDate']), 'F d, Y') ?></td>
						</tr>
						<tr>
							<th>Channel</th>
							<td><a href="<?= $rip['ChannelURL'] ?>" target="_blank"><?= $rip['ChannelName'] ?></a></td>
						</tr>
						<tr>
							<th>Length</th>
							<td id="data-Length"><?= $rip['RipLength'] ?></td>
						</tr>
						<tr>
							<th>Game</th>
							<td><button id="data-GameName" type="button" onclick="window.location='/rips?games[]=<?= $rip['RipGame'] ?>'"><?= $rip['GameName']  ?></button></td>
						</tr>
						<tr>
							<th>Platform</th>
							<?php if (empty($rip['Platforms'])): ?>
								<td><i>Unknown platform</i></td>
							<?php else: ?>
								<td><?= implode(', ', $rip['Platforms']) ?></td>
							<?php endif; ?>
						</tr>
						<tr>
							<th>Composers/Artists</th>
							<td>
								<?php $composers = [];
								foreach ($rip['Composers'] as $composer): ?>
									<?php array_push($composers, $composer['ComposerName']) ?>
									<button type="button" onclick="window.location='/rips?composers[]=<?= $composer['ComposerID'] ?>'"><?= $composer['ComposerName']  ?></button>
								<?php endforeach; ?>
								<?= empty($rip["Composers"]) ? "<i>No composers have been credited.</i>" : '' ?>
							</td>
						</tr>
						<tr>
							<th>Rippers</th>
							<td>
								<?php $rippers = [];
								foreach ($rip['Rippers'] as $ripper): ?>
									<?php array_push($rippers, $ripper['RipperName']) ?>
									<button type="button" onclick="window.location='/rips?rippers[]=<?= $ripper['RipperID'] ?>'"><?= $ripper['RipperName']  ?></button>
								<?php endforeach; ?>
							</td>
						</tr>
						<?php if (!empty($rip['RipAlternateURL']) || !empty($rip['RipWikiURL'])) : ?>
							<tr>
								<th>Other Links</th>
								<td>
									<ul>
										<?php if (!empty($rip['RipAlternateURL'])): ?>
											<li id="data-AltURL"><a href="<?= $rip['RipAlternateURL'] ?>" target="_blank">Album Release</a></li>
										<?php endif; ?>
										<?php if (!empty($rip['RipWikiURL'])): ?>
											<li id="data-WikiURL"><a href="<?= $rip['RipWikiURL'] ?>" target="_blank">Wiki Page</a></li>
										<?php endif; ?>
									</ul>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<?php if ($hasWiki): ?>
					<hr>
					<button type="button" onclick="generateWikiPage()">Generate Wiki Page</button>
				<?php endif; ?>
			</section>
		</div>
	<?php endif; ?>
</main>
<datalist id="data-template">
	<option id="data-YouTubeID"><?= $rip['RipYouTubeID'] ?></option>
	<option id="data-AlternateName"><?= $rip['RipAlternateName'] ?></option>
	<option id="data-Game"><?= $rip['GameName'] ?></option>
	<option id="data-RipName"><?= $rip['RipName'] ?></option>
	<option id="data-MixName"><?= $rip['MixName'] ?></option>
	<option id="data-Rippers"><?= implode(";", $rippers) ?></option>
	<option id="data-Composers"><?= implode(";", $composers) ?></option>
	<option id="data-Platforms"><?= implode(";", $rip['Platforms']) ?></option>
</datalist>
<script src="/res/js/rip.js"></script>