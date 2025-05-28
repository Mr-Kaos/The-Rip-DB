<?php
$theme = $_COOKIE['theme'] ?? 'light';
if ($theme !== 'light' && $theme !== 'dark') {
	$theme = 'light';
	setcookie('theme', $theme, 0, '/');
}
?>

<head>
	<meta charset="utf-8">
	<meta name="keywords" content="Rips, SiIVaGunner, SilvaGunner">
	<meta name="description" content="The rip database. Find high quality rips based on their jokes, game or other sources!">
	<title><?= constant("PAGE_TITLE") ?></title>
	<link rel="stylesheet" href="/res/css/theme_<?= $theme ?>.css">
	<link rel="stylesheet" href="/res/css/layout.css">
</head>
<script src="/res/js/main.js"></script>