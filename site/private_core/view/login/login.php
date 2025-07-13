<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Login to the Rip Database</h1>
	<form method="POST">
		<fieldset>
			<legend>Please enter your username and password to login.</legend>
			<?= (new o\InputElement('Username', o\InputTypes::text, ['name' => 'username', 'minlength' => 3, 'maxlength' => 32, 'required' => true], null, true))->buildElement() ?>
			<?= (new o\InputElement('Password', o\InputTypes::password, ['name' => 'password', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
		</fieldset>
		<button type="submit">Login</button>
	</form>
	<p>Need an account? <a href="/login/new">Create one here</a>!</p>
</main>