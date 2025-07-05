<?php

use RipDB\Theme;

$theme = Theme::tryFrom($_COOKIE['theme'] ?? null) ?? Theme::Light;
?>

<head>
	<meta charset="utf-8">
	<meta name="keywords" content="SiIvaGunner guess, guess that siivagunner, rip guesser, ripguessr, guess that rip, music guesser, music guessing game, rip guessing game">
	<meta name="description" content="A music guessing game with a twist - can you identify what a song has been mixed up with?">
	<meta property="og:title" content="RipGuessr">
	<meta property="og:image" content="http://ripdb.net/res/img/ripguesser_logo.png">
	<meta property="og:image:secure_url" content="https://ripdb.net/res/img/ripguesser_logo.png">
	<meta property="og:image:alt" content="RipGuessr logo. A blue capital letter R and an orange capital letter G above a record with a yellow question mark floating above the record.">
	<meta property="og:url" content="https://ripdb.net/ripguessr">
	<meta property="og:site_name" content="RipGuessr">
	<meta property="og:type" content="website">
	<meta property="og:description" content="A music guessing game with a twist - can you identify what a song has been mixed up with?">
	<meta property="og:determiner" content="the" />
	<meta property="og:locale" content="en_GB" />
	<meta property="og:alternate" content="en_US" />
	<title><?= constant("PAGE_TITLE") ?></title>
	<link rel="stylesheet" href="/res/css/theme_<?= $theme->value ?>.css">
	<link rel="stylesheet" href="/res/css/layout.css">
	<link rel="icon" href="/res/img/ripguesser_icon-128x.png" type="image/x-icon">
</head>