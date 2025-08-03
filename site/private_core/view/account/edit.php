<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Account</h1>
	<div class="sidebar-split">
		<div>
			<a href="/account" <?= $subPage == 'account' ? 'class="active"' : '' ?>>Account Info</a>
			<a href="/account/playlists" <?= $subPage == 'playlists' ? 'class="active"' : '' ?>>Playlists</a>
			<a href="/login/logout">Logout</a>
		</div>
		<div id="account-content">
			<?php if ($subPage == 'account') : ?>
				<p>Account created: <?= date_format(new DateTime($account['Created']), 'Y-m-d'); ?></p>
				<form id="login-edit" action="?mode=username" method="post">
					<fieldset>
						<legend>Change Username</legend>
						<?= (new o\InputElement('Username', o\InputTypes::text, ['name' => 'username', 'value' => $account['Username'], 'data-og' => $account['Username'], 'minlength' => 3, 'maxlength' => 32, 'required' => true, 'pattern' => '^(?=.{3,32}$)[a-zA-Z0-9._+-~]+$', 'oninput' => 'validateUsernameChange(this)'], null, true))->buildElement() ?>
						<?= (new o\InputElement('Enter Password to Save', o\InputTypes::password, ['name' => 'password', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
						<button id="submit-username" type="submit" disabled>Update Username</button>
					</fieldset>
				</form>
				<form id="password-edit" action="?mode=password" method="post">
					<fieldset>
						<legend>Update Password</legend>
						<?= (new o\InputElement('Current Password', o\InputTypes::password, ['id' => 'password-old', 'name' => 'password', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
						<?= (new o\InputElement('New Password', o\InputTypes::password, ['name' => 'password-new', 'minlength' => 6, 'maxlength' => 64, 'required' => true, 'oninput' => "checkPasswordMatch(this, 'password-new2')"], null, true))->buildElement() ?>
						<?= (new o\InputElement('Confirm New Password', o\InputTypes::password, ['name' => 'password-new2', 'minlength' => 6, 'maxlength' => 64, 'required' => true, 'oninput' => "checkPasswordMatch(this, 'password-new')"], null, true))->buildElement() ?>
						<button type="submit">Update Password</button>
					</fieldset>
				</form>
				<form id="delete" action="?mode=delete" method="post">
					<fieldset>
						<legend>Delete Account</legend>
						<?= (new o\InputElement('Current Password', o\InputTypes::password, ['name' => 'password-check', 'minlength' => 6, 'maxlength' => 64, 'required' => true, 'oninput' => "checkPasswordMatch(this, 'password-check2')"], null, true))->buildElement() ?>
						<?= (new o\InputElement('Confirm Password', o\InputTypes::password, ['name' => 'password-check2', 'minlength' => 6, 'maxlength' => 64, 'required' => true, 'oninput' => "checkPasswordMatch(this, 'password-check')"], null, true))->buildElement() ?>
						<p style="color:red">Please note that deleting your account is permanent!</p>
						<button type="submit" class="btn-bad" disabled>Delete Account</button>
					</fieldset>
				</form>
				<script>
					async function validateUsernameChange(input) {
						let btnSubmit = document.getElementById('submit-username');
						let valid = checkUsername(input);
						btnSubmit.disabled = !valid;
					}
				</script>
			<?php elseif ($subPage == 'playlists') : ?>
				<?php include_once('playlist-search.php') ?>
				<hr>
				<details>
					<summary>Claim an Unsaved Playlist</summary>
					<p>Use this form to claim a playlist that was saved while not logged in.</p>
					<form action="/account/playlists/claim" method="post">
						<fieldset>
							<legend>Enter your playlist's claim code below</legend>
							<?= (new o\InputElement('Claim Code', o\InputTypes::text, ['name' => 'code', 'maxlength' => 8, 'minlength' => 8, 'pattern' => '[0-9a-zA-Z]{8}', 'required' => true]))->buildElement() ?>
						</fieldset>
						<button type="submit">Claim</button>
					</form>
					<p><em>If you've lost your claim code, you will need to create a new playlist. Sorry!</em></p>
				</details>
			<?php endif; ?>
		</div>
	</div>
</main>
<script src="/res/js/account.js" defer></script>