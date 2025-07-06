<?php
use RipDB\Objects as o;
use RipDB\Theme;
?>
<footer>
	<div>
		<span>Contribute or report bugs <a href="https://github.com/Mr-Kaos/The-Rip-DB" target="_blank">on GitHub</a>!</span>
	</div>
	<div>
		<span>Made with brown bricks by <a href="https://github.com/Mr-Kaos" target="_blank">Mr Kaos</a></span>
	</div>
	<div>
		<form action="/settings/theme">
			<?= (new o\DropdownElement('Theme', Theme::getThemes(), ['name' => 'theme', 'selected' => $_COOKIE['theme'] ?? '', 'onchange' => 'submit()', 'style' => 'background:var(--accent-2)', 'required' => true, 'no-asterisk' => true]))->buildElement(); ?>
		</form>
	</div>
</footer>