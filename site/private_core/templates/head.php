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
	<meta property="og:image" content="/res/img/icon.png">
	<meta property="og:url" content="https://ripdb.net">
	<title><?= constant("PAGE_TITLE") ?></title>
	<link rel="stylesheet" href="/res/css/theme_<?= $theme ?>.css">
	<link rel="stylesheet" href="/res/css/layout.css">
	<link rel="icon" href="/res/img/favicon.ico" type="image/x-icon">
</head>