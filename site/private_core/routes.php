<?php

use RipDB\Theme;

/**
 * Routes file.
 * 
 * This file contains all routes available in the web app.
 */

$uri = explode('?', $_SERVER['REQUEST_URI'])[0];
require_once('private_core/config/themes.php');
require_once('private_core/objects/SessionManager.php');

/**
 * All valid HTTP methods available in this system.
 */
enum HttpMethod
{
	case GET;
	case POST;
	case PUT;
	case DELETE;
}

// Error page
Flight::group('/error', function () {
	Flight::route('/@code', function ($code) {
		displayPage('error', 'ErrorController', ['error' => $code], 'Oh No!');
		http_response_code(500);
	});
});

// Home Page
Flight::route('/', function () {
	displayPage('home', 'HomeController', [], 'Home');
});

// Rips Pages
Flight::group('/rips', function () {
	Flight::route('/', function () {
		displayPage('rips/search', 'RipController', [], 'Rips');
	});
	Flight::route('/random', function () {
		displayPage('rips/rip', 'RipController', ['random' => true]);
	});

	Flight::route('POST /new', function () {
		submitForm('rips/new', 'RipController');
	});

	Flight::route('/new', function () {
		displayPage('rips/new', 'RipController', [], 'New Rip');
	});

	Flight::route('POST /edit/@id', function ($id) {
		submitForm('rips/edit', 'RipController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('rips/edit', 'RipController', ['id' => $id], 'Edit Rip');
	});

	Flight::route('/@id', function ($id) {
		displayPage('rips/rip', 'RipController', ['id' => $id], 'View Rip');
	});
});

// Jokes Pages
Flight::group('/jokes', function () {
	Flight::route('POST /new', function () {
		submitForm('jokes/new', 'JokeController');
	});
	Flight::route('/new', function () {
		displayPage('jokes/new', 'JokeController', [], 'New Joke');
	});
	Flight::route('POST /edit/@id', function ($id) {
		submitForm('jokes/edit', 'JokeController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('jokes/edit', 'JokeController', ['id' => $id], 'Edit Joke');
	});
	Flight::route('/', function () {
		displayPage('jokes/search', 'JokeController', [], 'Jokes');
	});
});

// Meta Jokes Pages
Flight::group('/meta-jokes', function () {
	Flight::route('POST /new', function () {
		submitForm('meta-jokes/new', 'MetaJokeController');
	});
	Flight::route('/new', function () {
		displayPage('meta-jokes/new', 'MetaJokeController', [], 'New Meta Joke');
	});
	Flight::route('POST /edit/@id', function ($id) {
		submitForm('meta-jokes/edit', 'MetaJokeController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('meta-jokes/edit', 'MetaJokeController', ['id' => $id], 'Edit Meta Joke');
	});
	Flight::route('/', function () {
		displayPage('meta-jokes/search', 'MetaJokeController', [], 'Meta Jokes');
	});
});

// Meta Pages
Flight::group('/metas', function () {
	Flight::route('POST /new', function () {
		submitForm('metas/new', 'MetaController');
	});
	Flight::route('/new', function () {
		displayPage('metas/new', 'MetaController', [], 'New Meta');
	});
	Flight::route('POST /edit/@id', function ($id) {
		submitForm('metas/edit', 'MetaController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('metas/edit', 'MetaController', ['id' => $id], 'Edit Meta');
	});
	Flight::route('/', function () {
		displayPage('metas/search', 'MetaController', [], 'Metas');
	});
});

// Tag Pages
Flight::group('/tags', function () {
	Flight::route('/', function () {
		displayPage('tags/search', 'TagController', [], 'Tags');
	});
	Flight::route('POST /new', function () {
		submitForm('tags/new', 'TagController');
	});
	Flight::route('/new', function () {
		displayPage('tags/new', 'TagController', [], 'New Tag');
	});
	Flight::route('POST /edit/@id', function ($id) {
		submitForm('tags/edit', 'TagController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('tags/edit', 'TagController', ['id' => $id], 'Edit Tag');
	});
});

// Game Pages
Flight::group('/games', function () {
	Flight::route('/', function () {
		displayPage('games/search', 'GameController', [], 'Games');
	});
	Flight::route('POST /new', function () {
		submitForm('games/new', 'GameController');
	});
	Flight::route('/new', function () {
		displayPage('games/new', 'GameController', [], 'New Game');
	});
	Flight::route('POST /edit/@id', function ($id) {
		submitForm('games/edit', 'GameController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('games/edit', 'GameController', ['GameID' => $id], 'Edit Game');
	});
});

// Channel Pages
Flight::group('/channels', function () {
	Flight::route('/', function () {
		displayPage('channels/search', 'ChannelController', [], 'Channels');
	});
	Flight::route('POST /new', function () {
		submitForm('channels/new', 'ChannelController');
	});
	Flight::route('/new', function () {
		displayPage('channels/new', 'ChannelController', [], 'New Channel');
	});
	Flight::route('POST /edit/@id', function ($id) {
		submitForm('channels/edit', 'ChannelController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('channels/edit', 'ChannelController', ['id' => $id], 'Edit Channel');
	});
});

// Ripper Pages
Flight::group('/rippers', function () {
	Flight::route('/', function () {
		displayPage('rippers/search', 'RipperController', [], 'Rippers');
	});
	Flight::route('POST /new', function () {
		submitForm('rippers/new', 'RipperController');
	});
	Flight::route('/new', function () {
		displayPage('rippers/new', 'RipperController', [], 'New Ripper');
	});
	Flight::route('POST /edit/@id', function ($id) {
		submitForm('rippers/edit', 'RipperController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('rippers/edit', 'RipperController', ['id' => $id], 'Edit Ripper');
	});
});

// Help Page
Flight::route('/help', function () {
	displayPage('help', null, [], 'Help / FAQ');
});

const RIP_GUESSER_HEAD = 'head_ripguesser.php';

// Rip Guesser Page
Flight::group('/ripguessr', function () {
	Flight::route('/', function () {
		displayPage('rip-guesser', 'GuesserController', [], 'Rip Guessr', RIP_GUESSER_HEAD);
	});
	// The play page should not have any GET tags or subpages.
	Flight::route('/play', function () {
		if (count($_GET) > 0) {
			Flight::redirect('/ripguessr/play');
		}
		displayPage('rip-guesser-play', 'GuesserController', [], 'Rip Guessr', RIP_GUESSER_HEAD);
	});
	Flight::route('/play/@any', function ($any) {
		Flight::redirect('/ripguessr/play');
	});

	// Async requests for live game interaction:
	if (str_contains($_SERVER['HTTP_REFERER'] ?? null, 'ripguessr/play')) {
		// Requests here should always be returned as a JSON.
		Flight::route('POST /game/@request', function ($request) {
			performAPIRequest('game', $request, HttpMethod::POST, 'GuesserController');
		});
		Flight::route('GET /game/@request', function ($request) {
			performAPIRequest('game', $request, HttpMethod::GET, 'GuesserController');
		});
		Flight::route('DELETE /game/@request', function ($request) {
			performAPIRequest('game', $request, HttpMethod::DELETE, 'GuesserController');
		});
		Flight::route('GET /search/@request', function ($request) {
			performAPIRequest('search', $request, HttpMethod::GET, 'GuesserController');
		});
	}
});

// Settings Requests
Flight::route('GET /settings/theme', function () {
	$theme = Theme::tryFrom($_GET['theme'] ?? null) ?? Theme::Light;
	setcookie('theme', $theme->value, 0, '/');
	header('location:' . $_SERVER['HTTP_REFERER']);
	die();
});

// Login requests
Flight::group('/login', function () {
	Flight::route('POST /', function () {
		submitForm('login/login', 'LoginController');
	});
	Flight::route('/', function () {
		displayPage('login/login', 'LoginController', [], 'Login');
	});

	Flight::route('POST /new', function () {
		submitForm('login/new', 'LoginController');
	});
	Flight::route('/new', function () {
		displayPage('login/new', 'LoginController', [], 'Login');
	});

	Flight::route('/logout', function () {
		submitForm('logout', 'LoginController');
	});
});

Flight::group('/account', function () {
	Flight::route('POST /', function () {
		submitForm('account/edit', 'AccountController');
	});

	Flight::route('/', function () {
		displayPage('account/edit', 'AccountController', ['subPage' => 'account']);
	});

	// Async requests:
	if (str_ends_with($_SERVER['HTTP_REFERER'] ?? null, '/account') || str_ends_with($_SERVER['HTTP_REFERER'] ?? null, '/login/new')) {
		Flight::route('GET /check-username', function () {
			performAPIRequest('check', 'user', HttpMethod::GET, 'AccountController');
		});
	}
});

// Dropdown search requests
Flight::group('/search', function () {

	Flight::route('GET /tags', function () {
		performAPIRequest('search', 'tags', HttpMethod::GET);
	});
	Flight::route('GET /jokes', function () {
		performAPIRequest('search', 'jokes', HttpMethod::GET);
	});
	Flight::route('GET /meta-jokes', function () {
		performAPIRequest('search', 'meta-jokes', HttpMethod::GET);
	});
	Flight::route('GET /metas', function () {
		performAPIRequest('search', 'metas', HttpMethod::GET);
	});
	Flight::route('GET /games', function () {
		performAPIRequest('search', 'games', HttpMethod::GET);
	});
	Flight::route('GET /rippers', function () {
		performAPIRequest('search', 'rippers', HttpMethod::GET);
	});
	Flight::route('GET /genres', function () {
		performAPIRequest('search', 'genres', HttpMethod::GET);
	});
	Flight::route('GET /rippers', function () {
		performAPIRequest('search', 'rippers', HttpMethod::GET);
	});
	Flight::route('GET /rip-names', function () {
		performAPIRequest('search', 'rip-names', HttpMethod::GET);
	});
	Flight::route('GET /rip-alt-names', function () {
		performAPIRequest('search', 'rip-names', HttpMethod::GET);
	});
	Flight::route('GET /@other', function ($other) {
		http_response_code(404);
		die();
	});
});

/**
 * Displays the page with a header and footer.
 */
function displayPage(string $page, ?string $controllerName = null, array $data = [], ?string $pageTitle = null, string $headerFileOverride = 'head.php'): void
{
	RipDB\initSession();
	// Include page objects that are commonly used across pages
	include_once('private_core/objects/pageElements/InputElement.php');
	include_once('private_core/objects/pageElements/DropdownElement.php');
	include_once('private_core/objects/pageElements/SearchElement.php');

	// Create controller if one exists
	$pageData = null;
	if (!is_null($controllerName)) {
		require_once("private_core/controller/$controllerName.php");
		$controllerName = "\RipDB\\Controller\\$controllerName";
		$controller = new $controllerName($page);
		$controller->performRequest($data);
		$pageData = $controller->getPreparedData();

		if ($controller != null) {
			if (!is_null($pageTitleOverride = $controller->getPageTitle())) {
				$pageTitle = $pageTitleOverride;
			}
		}
	}

	echo '<!DOCTYPE HTML><html>';
	define('PAGE_TITLE', "The Rip Database | " . ($pageTitle === null ? $page : $pageTitle));
	require("templates/$headerFileOverride");
	echo "<body>";
	require('templates/globalScripts.php');
	require('templates/nav.php');
	Flight::render($page, $pageData);
	require('templates/footer.php');
	// Check for any notifications
	echo outputNotifications();
	echo "</body></html>";
}

/**
 * Performs an API request.
 * @param ?string $functionGroup The group in which an API function belongs to. If none, can be null.
 * @param string $function The name of the API function to execute.
 * @param HttpMethod $method The HTTP method the request is to use.
 * @param string $controllerName The name of the Controller the API request will use. Defaults to "APIController.
 * @param array $data Any extra data for the request. This is in addition to the body data being sent.
 */
function performAPIRequest(?string $functionGroup, string $function, HttpMethod $method, string $controllerName = 'APIController', array $data = [])
{
	require_once("private_core/controller/$controllerName.php");
	$controllerName = "\RipDB\\Controller\\$controllerName";
	$controller = new $controllerName($function);

	if ($controller instanceof RipDB\Objects\IAsyncHandler) {
		$response = [];

		switch ($method) {
			case HttpMethod::GET:
				$response = $controller->get($function, $functionGroup);
				break;
			case HttpMethod::POST:
				$response = $controller->post($function, $functionGroup);
				break;
			case HttpMethod::PUT:
				$response = $controller->put($function, $functionGroup);
				break;
			case HttpMethod::DELETE:
				$response = $controller->delete($function, $functionGroup);
				break;
		}
		Flight::json($response);
	} else {
		http_response_code(501);
	}
}

/**
 * Submits a form from a POST request.
 */
function submitForm(string $page, string $controllerName, ?array $data = null)
{
	if (!is_null($controllerName)) {
		RipDB\initSession();
		require_once("private_core/controller/$controllerName.php");
		$controllerName = "\RipDB\\Controller\\$controllerName";
		$controller = new $controllerName($page);
		$result = $controller->validateRequest($data);

		// If an ACCEPT header was requested to respond as JSON, be sure to fulfil it.
		// Ths is often used for modals that are submitting forms from other pages.
		if (($_SERVER['HTTP_ACCEPT'] ?? null) == 'application/json') {
			Flight::json($result);
		} else {
			if ($result === false) {
				Flight::redirect($_SERVER['HTTP_REFERER']);
			} else {
				Flight::redirect($result);
			}
		}
	}
}

/**
 * Checks to see if there are any notifications to display, and if so creates a simple hidden HTML element for JavaScript to parse and display to the user.
 */
function outputNotifications(): string
{
	$notifs = '';
	if (!empty($_SESSION[RipDB\ERRORS] ?? null)) {
		$notifs .= '<div id="server-notifs" style="display:none">';
		foreach ($_SESSION[RipDB\ERRORS] as $msg => $priority) {
			$notifs .= '<p priority="' . $priority->name . '">' . htmlspecialchars($msg) . '</p>';
		}
		$notifs .= '</div>';
		$_SESSION[RipDB\ERRORS] = [];
	}
	return $notifs;
}
