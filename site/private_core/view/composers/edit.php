<?php
use RipDB\Objects as o;
?>
<main>
	<h1>Edit Composer</h1>
	<p>Edit the composer's name here.</p>

	<form id="edit-composer" method="POST">
		<fieldset>
			<legend>Full Name</legend>
			<p>Enter the composer's English spelling of their name here.</p>
			<?= (new o\InputElement('First Name', o\InputTypes::text, ['name' => 'first-name', 'maxlength' => 128, 'required' => true, 'value' => $composer['ComposerFirstName']]))->buildElement() ?>
			<?= (new o\InputElement('Last Name', o\InputTypes::text, ['name' => 'last-name', 'maxlength' => 128, 'value' => $composer['ComposerLastName']]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Alternate Name Spelling</legend>
			<p>Use this section for an alternate spelling of the composer's name, such as in their native language.</p>
			<?= (new o\InputElement('First Name', o\InputTypes::text, ['name' => 'first-name-alt', 'maxlength' => 256, 'value' => $composer['ComposerFirstNameAlt']]))->buildElement() ?>
			<?= (new o\InputElement('Last Name', o\InputTypes::text, ['name' => 'last-name-alt', 'maxlength' => 256, 'value' => $composer['ComposerLastNameAlt']]))->buildElement() ?>
		</fieldset>
		<button type="submit">Update Composer</button>
	</form>
</main>