<main>
	<?php if (empty($rip)) : ?>
		<h2>This rip does not exist.</h2>
	<?php else: ?>
		<section id="summary">
			<h1><?= $rip['RipName'] ?></h1>
			<p><?= $rip['RipDescription'] ?></p>
			<a href="/rips/edit/<?= $rip['RipID']; ?>">Edit Rip</a>
		</section>
		<div style="float:left">
			<section id="jokes">
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
								<td><?php if (isset($joke['start'])) : ?>
										<?= $joke['start'] . ' -> ' . $joke['end'] ?>
									<?php endif; ?>
								</td>
								<td><?= $rip['Jokes'][$joke['JokeID']]['JokeName'] ?></td>
								<td><?= $rip['Jokes'][$joke['JokeID']]['JokeComment'] ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</section>
		</div>
		<div style="float:right">
			<section id="other">
				<table>
					<tbody>
						<tr>
							<th>Video</th>
							<td><a href="<?= $rip['RipURL'] ?>" target="_blank">YouTube</a></td>
						</tr>
						<tr>
							<th>Upload Date</th>
							<td><?= $rip['RipDate'] ?></td>
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
		</div>
	<?php endif; ?>
</main>