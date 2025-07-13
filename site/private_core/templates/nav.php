<nav>
	<a href="/">Home</a>
	<div>
		<a href="/rips">Search</a>
		<ul>
			<li><a href="/rips">Rips</a></li>
			<li><a href="/jokes">Jokes</a></li>
			<li><a href="/games">Games</a></li>
			<li><a href="/tags">Tags</a></li>
		</ul>
	</div>
	<a href="/ripguessr">RipGuessr</a>
	<a href="/help">Help / FAQ</a>
	<?php if (RipDB\checkAuth()) : ?>
		<a href="/account">Account</a>
	<?php else: ?>
		<a href="/login">Login</a>
	<?php endif; ?>
</nav>
<span class="funny"></span>