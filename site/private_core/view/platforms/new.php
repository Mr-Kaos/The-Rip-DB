<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add A New Platform</h1>
	<p>Fill in this form to add a platform for use in games.</p>
	<p>It's best not to do it here, and instead do it while <a href="/games/new">adding or editing a game</a>.</p>

	<form id="new-platform" method="POST">
		<fieldset>
			<legend>Platform Information</legend>
			<?= (new o\InputElement('Name', o\InputTypes::text, ['name' => 'name', 'maxlength' => 128, 'required' => true]))->buildElement() ?>
		</fieldset>
		<button type="submit">Submit Platform</button>
	</form>
</main>