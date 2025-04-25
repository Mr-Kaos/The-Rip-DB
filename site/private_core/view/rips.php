<?php

use RipDB\Objects\InputElement;
use RipDB\Objects\InputTypes;
use RipDB\RipsController;

require('private_core/controller/RipsController.php');
require('private_core/objects/InputElement.php');

$controller = new RipsController();
?>

<main>
	<h1>Rips</h1>

	<form id="rip_search" method="GET">
		<?= (new InputElement('Search', InputTypes::search, ['id' => 'search']))->buildElement() ?>
	</form>

	<?php if (!empty($controller->getData('results'))): ?>
		<section id="results">

		</section>
	<?php endif; ?>
</main>