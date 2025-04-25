<?php

/**
 * Routes file.
 * 
 * This file contains all routes available in the web app.
 */

// Home Page
Flight::route('/', function () {
	displayPage('home', 'Home');
});

// Rips Page (search)
Flight::route('/rips', function () {
	displayPage('rips');
});


/**
 * Displays the page with a header and footer.
 */
function displayPage(string $page, ?string $pageTitle = null): void
{
	echo '<!DOCTYPE HTML><html>';
	define('PAGE_TITLE', $pageTitle ?? "The Rip Database - $page");
	require('templates/head.php');

	echo "<body>";
	Flight::render($page);
	require('templates/footer.php');
	echo "</body></html>";
}
