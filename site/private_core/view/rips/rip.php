<main>
	<?php if (empty($rip)) : ?>
		<h2>This rip does not exist.</h2>
	<?php else: ?>
		<section id="summary">
			<h1><?= $rip['RipName'] ?></h1>
			<p><?= $rip['RipDescription'] ?></p>
			<a href="/rips/edit/<?= $rip['RipID']; ?>">Edit Rip</a>
		</section>
		<section id="jokes" style="float:left">
			<?php if (!empty($jokes)): ?>
				<table>
					<caption>Jokes In This Rip:</caption>
					<thead>
						<tr>
							<th>Timestamp</th>
							<th>Joke</th>
							<th>Comment</th>
						</tr>
					</thead>
					<tbody>
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
								<td><?= $rip['Jokes'][$joke['JokeID']]['JokeName'] ?></td>
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
		<section id="other" style="float:right;width:40%">
			<table>
				<tbody>
					<tr>
						<th>Video/Link</th>
						<td>
							<a href="<?= $rip['RipURL'] ?>" target="_blank">YouTube</a>
							<?php if (!empty($rip['RipYouTubeID'])): ?>
								<br>
								<iframe id="yt-embed" width="640" height="360" style="width:fit-content" src="https://www.youtube-nocookie.com/embed/<?= $rip['RipYouTubeID']; ?>?vq=240p" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>Upload Date</th>
						<td><?= date_format(new DateTime($rip['RipDate']), 'M d, Y') ?></td>
					</tr>
					<tr>
						<th>Rip Channel</th>
						<td><a href="<?= $rip['ChannelURL'] ?>" target="_blank"><?= $rip['ChannelName'] ?></a></td>
					</tr>
					<tr>
						<th>Length</th>
						<td><?= $rip['RipLength'] ?></td>
					</tr>
					<tr>
						<th>Game</th>
						<td><?= $rip['GameName'] ?></td>
					</tr>
					<tr>
						<th>Genres</th>
						<td>
							<?php foreach ($rip['Genres'] as $genre): ?>
								<?= $genre['GenreName'] ?>
							<?php endforeach; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
	<?php endif; ?>
</main>
<script src="/res/js/rip.js"></script>