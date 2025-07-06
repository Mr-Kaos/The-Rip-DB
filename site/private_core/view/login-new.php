<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Create An Account For the Rip Database</h1>
	<p>Please </p>
	<form method="POST">
		<fieldset>
			<?= (new o\InputElement('Username', o\InputTypes::text, ['name' => 'username', 'minlength' => 3, 'maxlength' => 32, 'required' => true, 'pattern' => '^(?=.{3,32}$)[a-zA-Z0-9._+-~]+$'], null, true))->buildElement() ?>
			<ul>
				<li>Username must be between 3 and 32 characters long.</li>
				<li>Only alpha-numeric characters or any of the symbols <code>_ - . ~ +</code> are allowed (excludes spaces).</li>
				<li>The username must not already be taken.</li>
			</ul>
		</fieldset>
		<fieldset>
			<?= (new o\InputElement('Password', o\InputTypes::password, ['name' => 'password', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
			<?= (new o\InputElement('Confirm Password', o\InputTypes::password, ['name' => 'password2', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
			<ul>
				<li>Password must be at least 6 characters long. Maximum length allowed is 64 characters.</li>
				<li>Password must not match your username.</li>
				<li>Password must be a combination of at least letters and numbers.</li>
				<li>Password should not be something that is easily guessable based on your details.</li>
			</ul>
		</fieldset>
		<button type="submit">Create Account</button>
	</form>
	<br>
	<details>
		<summary style="font-size:larger;font-weight:bold;">About Accounts</summary>
		<p>Accounts are only used to edit and submit rips to the database. It is also used to create playlists in the RipGuessr game.</p>
		<p>All other site functionality is available without an account.</p>

		<h3>Security Information</h3>
		<p>All passwords are securely encrypted and stored for this site.</p>
		<p>Since emails are not stored for privacy concerns, if you forget your password, it won't be possible to recover your account. Please keep your login details safe to prevent this from happening!</p>
	</details>
</main>