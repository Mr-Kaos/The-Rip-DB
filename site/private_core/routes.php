<?php

/**
 * Routes file.
 * 
 * This file contains all routes available in the web app.
 */

const PAGE_EXTENSIONS = ['html', 'php']; // All allowed webpage extensions. If found in the request URi, the client is redirected to the same page without it.
const RESOURCE_EXTENSIONS = ['css', 'js', 'ico', 'jpg', 'jpeg', 'png']; // All allowed file resource extensions. Any requests to other extensions result in a 404.

$uri = explode('?', $_SERVER['REQUEST_URI'])[0];

// Home Page
Flight::route('/', function () {
	displayPage('home');
});

// Rips Page (search)
Flight::route('/rips', function () {
	displayPage('rips', 'RipsController');
});

// Rips Page (search)
Flight::route('/rip/@id', function (int $id) {
	displayPage('rip', 'RipController', ['id' => $id]);
});


/**
 * Displays the page with a header and footer.
 */
function displayPage(string $page, ?string $controllerName = null, array $data = [], ?string $pageTitle = null,): void
{
	// Include page objects that are commonly used across pages
	include_once('private_core/objects/InputElement.php');
	include_once('private_core/objects/DropdownElement.php');
	include_once('private_core/objects/MultiSelectDropdownElement.php');

	// Create controller if one exists
	$pageData = null;
	if (!is_null($controllerName)) {
		require_once("private_core/controller/$controllerName.php");
		$controllerName = "\RipDB\\$controllerName";
		$controller = new $controllerName($data);

		$pageData = $controller->getPreparedData();
	}

	echo '<!DOCTYPE HTML><html>';
	define('PAGE_TITLE', $pageTitle ?? "The Rip Database - $page");
	require('templates/head.php');
	echo "<body>";
	require('templates/nav.php');
	Flight::render($page, $pageData);
	require('templates/footer.php');
	echo "</body></html>";
}
