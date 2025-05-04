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

// Rip page
Flight::route('/rip/@id', function ($id) {
	displayPage('rip', 'RipController', ['id' => $id]);
});

// New Rip Page
Flight::route('POST /new-rip', function () {
	submitForm('new-rip', 'RipController');
});
Flight::route('/new-rip', function () {
	displayPage('new-rip', 'RipController');
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
		$controller = new $controllerName($page);
		$controller->performRequest($data);

		$pageData = $controller->getPreparedData();
	}

	echo '<!DOCTYPE HTML><html>';
	define('PAGE_TITLE', $pageTitle ?? "The Rip Database - $page");
	require('templates/head.php');
	echo "<body>";
	require('templates/globalScripts.php');
	require('templates/nav.php');
	Flight::render($page, $pageData);
	require('templates/footer.php');
	echo "</body></html>";
}

/**
 * Submits a form from a POST request.
 */
function submitForm(string $page, string $controllerName)
{
	if (!is_null($controllerName)) {
		require_once("private_core/controller/$controllerName.php");
		$controllerName = "\RipDB\\$controllerName";
		$controller = new $controllerName($page);
		$result = $controller->submitRequest();

		if ($result instanceof Error) {
			echo $result->getMessage();
			echo '<a href="' . $_SERVER['HTTP_REFERER'] . '">Go Back (this page temporary)</a>';
		} else {
			header('location:' . $result);
			die();
		}
	}
}
