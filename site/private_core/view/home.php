<?php

use RipDB\Objects as o;
?>
<main>
	<h1>The Rip DB</h1>
	<p>Welcome to The Rip DB!</p>
	<p>Here you can search for rips based on their content, genre, joke or any other related data. Head over to the <a href="/rips">Rips page</a> to search for one, or use the search box below.</p>

	<section>
		<form action="/rips">
			<?= (new o\InputElement('Search', o\InputTypes::search, ['id' => 'search']))->buildElement() ?>
		</form>
		<a href="/rips/random"><button>I'm feeling lucky!</button></a>
		<p>We currently have <?= $RipCount ?> rips!</p>
	</section>
</main>