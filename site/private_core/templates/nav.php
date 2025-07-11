<nav>
	<a href="/">Home</a>
	<a href="/rips">Rips</a>
	<a href="/jokes">Jokes</a>
	<a href="/tags">Tags</a>
	<a href="/ripguessr">RipGuessr</a>
	<a href="/help">Help / FAQ</a>
	<?php if (RipDB\checkAuth()) : ?>
		<a href="/account">Account</a>
	<?php else: ?>
		<a href="/login">Login</a>
	<?php endif; ?>
</nav>
<span class="funny"></span>