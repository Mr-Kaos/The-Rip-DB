<?php
use RipDB\Objects as o;
use RipDB\Theme;
?>
<nav>
	<a href="/">Home</a>
	<a href="/rips">Rips</a>
	<a href="/jokes">Jokes</a>
	<a href="/tags">Tags</a>
	<a href="/ripguessr">RipGuessr</a>
	<a href="/help">Help / FAQ</a>
	<form action="/settings/theme">
		<?= (new o\DropdownElement('Theme', Theme::getThemes(), ['name' => 'theme', 'selected' => $_COOKIE['theme'], 'onchange' => 'submit()', 'style' => 'background:var(--accent-2)']))->buildElement(); ?>
	</form>
</nav>
<span class="funny"></span>