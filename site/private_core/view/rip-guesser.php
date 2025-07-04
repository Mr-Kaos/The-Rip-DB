<?php

use RipDB\RipGuesser as rg;
?>

<main>
	<h1>RipGuessr</h1>
	<section>
		<p>Welcome to RipGuessr!</p>
		<p>Guess the rip and/or its jokes to win.</p>
	</section>
	<section>
		<a href="/ripguessr/play" class="button">Play</a>
	</section>
	<section id="how-to-play">
		<div id="how-to-1">
			<h2>How To Play</h2>
			<p>The game is simple - A random rip will be selected from the database and will be played to you.<br>When listening to the rip, you will need to guess <strong>what jokes are used in the rip</strong>.</p>
		</div>
		<div id="how-to-2">
			<h3>Difficulty Levels</h3>
			<p>Selecting harder difficulties will require you to guess more details about the rip. Each difficulty will require you to guess these additional attributes, along with those from easier difficulties:</p>
			<div class="grid">
				<div>
					<h4>Beginner</h4>
					<i>Great for anyone who listens to music.</i>
					<p class="list-heading">Guessable Attributes:</p>
					<ul>
						<li>Jokes (<?= rg\PTS_CORRECT_JOKE ?>pts per joke)</li>
					</ul>
				</div>
				<div>
					<h4>Standard</h4>
					<i>Great for those familiar with Video Game Soundtrack.</i>
					<p class="list-heading">Guessable Attributes:</p>
					<ul>
						<li>Rip Name (<?= rg\PTS_CORRECT_RIP_NAME ?>pts)</li>
						<li>Rip's Game (<?= rg\PTS_CORRECT_GAME ?>pts)</li>
					</ul>
				</div>
				<div>
					<h4>Hard</h4>
					<i>Designed for those who are experienced in &quot;High Quality Rips&quot;.</i>
					<p class="list-heading">Guessable Attributes:</p>
					<ul>
						<li>Ripper Name (<?= rg\PTS_CORRECT_RIPPER ?>pts per ripper)</li>
						<li>Alternate Rip Name (<?= rg\PTS_CORRECT_ALT_NAME ?>pts)</li>
					</ul>
				</div>
			</div>
			<p>The rip's video will also be hidden on all difficulties except for Beginner.</p>
		</div>
		<div id="how-to-3">
			<h3>Scoring</h3>
			<p>Each correct guess will net you points. Depending on what attribute you are guessing will determine how many points you get.</p>
			<p>At the end of all rounds, your score will be tallied up. Aim for the highest score!</p>
		</div>
	</section>
</main>