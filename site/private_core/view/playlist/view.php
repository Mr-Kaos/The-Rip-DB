<main>
	<?php if (!empty($playlist)): ?>
		<h1><?= $playlist['PlaylistName'] ?></h1>
		<p><strong>Created by:</strong> <?= $playlist['Username'] ?></p>
		<h2>Rips:</h2>
		<div class="rows">
			<?php foreach ($rips as $rip): ?>
				<div>
					<div style="display:grid;gap:10px;grid-template-columns: 1fr 5fr 1fr 1fr;padding:5px">
						<button type="button" style="width:fit-content;margin-right:auto" onclick="toggleRipPreview('<?= $rip['RipYouTubeID'] ?>')">Preview</button>
						<?= $rip['RipName'] ?>
						<a href="<?= $rip['RipURL'] ?>" target="_blank">Rip URL</a>
						<?= $rip['RipLength'] ?>
					</div>
					<div style="display:none" id="<?= $rip['RipYouTubeID'] ?>">
						<iframe id="yt-embed" style="width:fit-content" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<p>You don't have permission to view the specified playlist or it does not exist.</p>
	<?php endif; ?>
</main>
<script src="/res/js/playlist.js"></script>