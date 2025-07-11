<main style="text-align:center">
	<h1>Uh oh, this wasn't supposed to happen...</h1>
	<?php if (isset($image)): ?>
		<img src="<?= $image ?>" style="max-height:900px;max-width:100%;">
	<?php endif; ?>
	<p><?= $msg ?></p>
	<p>Please <a href="https://github.com/Mr-Kaos/The-Rip-DB/issues" target="_blank">let the devs know</a> that they messed up!</p>
</main>