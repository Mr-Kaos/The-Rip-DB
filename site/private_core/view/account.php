<?php

use RipDB\Objects as o;

?>
<main>
	<h1>Account</h1>

	<div class="sidebar-split">
		<div>
			<a href="/account" <?= $subPage == 'account' ? 'class="active"' : '' ?>>Account Info</a>
			<a href="/login/logout">Logout</a>
		</div>
		<div id="account-content">
			<?php if ($subPage == 'account') : ?>
				<p>Account created: <?= date_format(new DateTime($account['Created']), 'Y-m-d'); ?></p>
				<form id="login-edit" action="?mode=username" method="post">
					<fieldset>
						<legend>Change Username</legend>
						<?= (new o\InputElement('Username', o\InputTypes::text, ['name' => 'username', 'value' => $account['Username'], 'minlength' => 3, 'maxlength' => 32, 'required' => true], null, true))->buildElement() ?>
						<?= (new o\InputElement('Enter Password to Save', o\InputTypes::password, ['name' => 'password', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
						<button type="submit">Update Username</button>
					</fieldset>
				</form>
				<form id="password-edit" action="?mode=password" method="post">
					<fieldset>
						<legend>Update Password</legend>
						<?= (new o\InputElement('Current Password', o\InputTypes::password, ['name' => 'password', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
						<?= (new o\InputElement('New Password', o\InputTypes::password, ['name' => 'password-new', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
						<?= (new o\InputElement('Confirm New Password', o\InputTypes::password, ['name' => 'password-new2', 'minlength' => 6, 'maxlength' => 64, 'required' => true], null, true))->buildElement() ?>
						<button type="submit">Update Password</button>
					</fieldset>
				</form>
			<?php endif; ?>
		</div>
	</div>
</main>