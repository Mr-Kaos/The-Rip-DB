<?php

use RipDB\Objects as o;
?>
<main>
	<h1>Add A New Composer</h1>
	<p>Fill in this form to add a composer to associate to rips.</p>

	<form id="new-composer" method="POST">
		<fieldset>
			<legend>Full Name</legend>
			<p>Enter the composer's English spelling of their name here.</p>
			<?= (new o\InputElement('First Name', o\InputTypes::text, ['name' => 'first-name', 'required' => true, 'maxlength' => 128]))->buildElement() ?>
			<?= (new o\InputElement('Last Name', o\InputTypes::text, ['name' => 'last-name', 'maxlength' => 128]))->buildElement() ?>
		</fieldset>
		<fieldset>
			<legend>Alternate Name Spelling</legend>
			<p>Use this section for an alternate spelling of the composer's name, such as in their native language.</p>
			<?= (new o\InputElement('First Name', o\InputTypes::text, ['name' => 'first-name-alt', 'maxlength' => 128]))->buildElement() ?>
			<?= (new o\InputElement('Last Name', o\InputTypes::text, ['name' => 'last-name-alt', 'maxlength' => 128]))->buildElement() ?>
		</fieldset>
		<button type="submit">Add Composer</button>
	</form>
</main>