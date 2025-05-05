<?php
$theme = $_COOKIE['theme'] ?? 'light';
if ($theme !== 'light' && $theme !== 'dark') {
	$theme = 'light';
	setcookie('theme', $theme, 0, '/');
}
?>

<head>
	<title><?= constant("PAGE_TITLE") ?></title>
	<link rel="stylesheet" href="/res/css/theme_<?= $theme ?>.css">
	<link rel="stylesheet" href="/res/css/layout.css">
</head>