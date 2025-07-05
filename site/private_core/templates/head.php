<?php

use RipDB\Theme;

$theme = Theme::tryFrom($_COOKIE['theme'] ?? null) ?? Theme::Light;
?>

<head>
	<meta charset="utf-8">
	<meta name="keywords" content="Rips, SiIvaGunner, SilvaGunner, siiva, siiva db, rip database, meme music, mashup database, siivagunner database, remix database, rip guesser, ripguessr">
	<meta name="description" content="The rip database. Find high quality rips based on their jokes, game or other sources!">
	<meta property="og:title" content="The Rip Database">
	<meta property="og:image" content="http://ripdb.net/res/img/icon.png">
	<meta property="og:image:secure_url" content="https://ripdb.net/res/img/icon.png">
	<meta property="og:image:alt" content="Rip DB">
	<meta property="og:url" content="https://ripdb.net">
	<meta property="og:site_name" content="RipDB">
	<meta property="og:type" content="website">
	<meta property="og:description" content="The rip database. Find high quality rips based on their jokes, game or other sources!">
	<meta property="og:determiner" content="the" />
	<meta property="og:locale" content="en_GB" />
	<meta property="og:alternate" content="en_US" />
	<title><?= constant("PAGE_TITLE") ?></title>
	<link rel="stylesheet" href="/res/css/theme_<?= $theme->value ?>.css">
	<link rel="stylesheet" href="/res/css/layout.css">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>