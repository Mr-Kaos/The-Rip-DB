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
	displayPage('home', 'HomeController');
});

// Rips Pages
Flight::group('/rips', function () {
	Flight::route('/', function () {
		displayPage('rips', 'RipController');
	});

	Flight::route('POST /new', function () {
		submitForm('new-rip', 'RipController');
	});

	Flight::route('/new', function () {
		displayPage('new-rip', 'RipController');
	});

	Flight::route('/@id', function ($id) {
		displayPage('rip', 'RipController', ['id' => $id]);
	});
});

// Jokes Pages
Flight::group('/jokes', function () {
	Flight::route('POST /new', function () {
		submitForm('new-joke', 'JokeController');
	});
	Flight::route('/new', function () {
		displayPage('new-joke', 'JokeController');
	});
	Flight::route('/', function () {
		displayPage('jokes', 'JokeController');
	});
});

// Tag Pages
Flight::group('/tags', function () {
	Flight::route('/', function () {
		displayPage('tags', 'TagController');
	});
	Flight::route('POST /new', function () {
		submitForm('new-tag', 'TagController');
	});
	Flight::route('/new', function () {
		displayPage('new-tag', 'TagController');
	});
});

// Settings Requests
Flight::route('/settings/theme', function () {
	$theme = ($_COOKIE['theme'] ?? 'light') == 'light' ? 'dark' : 'light';
	setcookie('theme', $theme, 0, '/');
	header('location:' . $_SERVER['HTTP_REFERER']);
	die();
});

// Dropdown search requests
Flight::group('/search', function () {

	Flight::route('GET /tags', function () {
		performAPIRequest('tags');
	});

	Flight::route('GET /jokes', function () {
		performAPIRequest('jokes');
	});
	Flight::route('GET /metas', function () {
		performAPIRequest('metas');
	});
	Flight::route('GET /games', function () {
		performAPIRequest('games');
	});
	Flight::route('GET /rippers', function () {
		performAPIRequest('rippers');
	});
	Flight::route('GET /genres', function () {
		performAPIRequest('genres');
	});
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
	include_once('private_core/objects/SearchElement.php');

	// Create controller if one exists
	$pageData = null;
	if (!is_null($controllerName)) {
		require_once("private_core/controller/$controllerName.php");
		$controllerName = "\RipDB\\Controller\\$controllerName";
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

function performAPIRequest(string $page)
{
	require_once("private_core/controller/APIController.php");
	$controller = new \RipDB\Controller\APIController($page);
	$controller->performRequest();

	Flight::json($controller->getPreparedData()['Result']);
}

/**
 * Submits a form from a POST request.
 */
function submitForm(string $page, string $controllerName)
{
	if (!is_null($controllerName)) {
		require_once("private_core/controller/$controllerName.php");
		$controllerName = "\RipDB\\Controller\\$controllerName";
		$controller = new $controllerName($page);
		$result = $controller->submitRequest();

		if (is_array($result)) {
			foreach ($result as $error) {
				echo $error->getMessage();
			}
			echo '<a href="' . $_SERVER['HTTP_REFERER'] . '">Go Back (this page temporary)</a>';
			die();
		} else {
			Flight::redirect($result);
		}
	}
}
