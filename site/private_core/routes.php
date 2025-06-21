<?php

use RipDB\Objects\IAsyncHandler;

/**
 * Routes file.
 * 
 * This file contains all routes available in the web app.
 */

const PAGE_EXTENSIONS = ['html', 'php']; // All allowed webpage extensions. If found in the request URi, the client is redirected to the same page without it.
const RESOURCE_EXTENSIONS = ['css', 'js', 'ico', 'jpg', 'jpeg', 'png']; // All allowed file resource extensions. Any requests to other extensions result in a 404.

$uri = explode('?', $_SERVER['REQUEST_URI'])[0];

/**
 * All valid HTTP methods available in this system.
 */
enum HttpMethod
{
	case GET;
	case POST;
	case PUT;
}

// Home Page
Flight::route('/', function () {
	displayPage('home', 'HomeController', [], 'Home');
});

// Rips Pages
Flight::group('/rips', function () {
	Flight::route('/', function () {
		displayPage('rips', 'RipController', [], 'Rips');
	});
	Flight::route('/random', function () {
		displayPage('rip', 'RipController', ['random' => true]);
	});

	Flight::route('POST /new', function () {
		submitForm('new-rip', 'RipController');
	});

	Flight::route('/new', function () {
		displayPage('new-rip', 'RipController', [], 'New Rip');
	});

	Flight::route('POST /edit/@id', function ($id) {
		submitForm('edit-rip', 'RipController', ['id' => $id]);
	});
	Flight::route('/edit/@id', function ($id) {
		displayPage('edit-rip', 'RipController', ['id' => $id], 'Edit Rip');
	});

	Flight::route('/@id', function ($id) {
		displayPage('rip', 'RipController', ['id' => $id], 'View Rip');
	});
});

// Jokes Pages
Flight::group('/jokes', function () {
	Flight::route('POST /new', function () {
		submitForm('new-joke', 'JokeController');
	});
	Flight::route('/new', function () {
		displayPage('new-joke', 'JokeController', [], 'New Joke');
	});
	Flight::route('/', function () {
		displayPage('jokes', 'JokeController', [], 'Jokes');
	});
});

// Tag Pages
Flight::group('/tags', function () {
	Flight::route('/', function () {
		displayPage('tags', 'TagController', [], 'Tags');
	});
	Flight::route('POST /new', function () {
		submitForm('new-tag', 'TagController');
	});
	Flight::route('/new', function () {
		displayPage('new-tag', 'TagController', [], 'New Tag');
	});
});

// Help Page
Flight::route('/help', function () {
	displayPage('help', null, [], 'Help / FAQ');
});

// Rip Guesser Page
Flight::group('/ripguessr', function () {
	Flight::route('/', function () {
		displayPage('rip-guesser', 'GuesserController', [], 'Rip Guessr');
	});
	Flight::route('/play', function () {
		session_start();
		displayPage('rip-guesser-play', 'GuesserController', [], 'Rip Guessr');
	});

	// Async requests for live game interaction:
	// Requests here should always be returned as a JSON.
	Flight::route('POST /game/@request', function ($request) {
		performAPIRequest('game', $request, HttpMethod::POST, 'GuesserController');
	});
	Flight::route('GET /game/@request', function ($request) {
		performAPIRequest('game', $request, HttpMethod::GET, 'GuesserController');
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
	Flight::route('GET /channels', function () {
		performAPIRequest('search', 'channels', HttpMethod::GET);
	});
	Flight::route('GET /@other', function ($other) {
		http_response_code(404);
		die();
	});
});

/**
 * Displays the page with a header and footer.
 */
function displayPage(string $page, ?string $controllerName = null, array $data = [], ?string $pageTitle = null): void
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

	if ($controller != null) {
		if (!is_null($pageTitleOverride = $controller->getPageTitle())) {
			$pageTitle = $pageTitleOverride;
		}
	}

	echo '<!DOCTYPE HTML><html>';
	define('PAGE_TITLE', "The Rip Database | " . ($pageTitle === null ? $page : $pageTitle));
	require('templates/head.php');
	echo "<body>";
	require('templates/globalScripts.php');
	require('templates/nav.php');
	Flight::render($page, $pageData);
	require('templates/footer.php');
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
		}
		Flight::json($response);
	} else {
		http_response_code(501);
	}
}

function parsePut(): ?array
{
	$input = file_get_contents('php://input', 'r');
	$put = [];

	/**
	 * This code has been adapted from code provided here https://gist.github.com/cwhsu1984/3419584ad31ce12d2ad5fed6155702e2.
	 * Adjustments have been made to improve efficiency and cleanliness.
	 * 
	 * Parse raw HTTP request data
	 *
	 * Pass in $a_data as an array. This is done by reference to avoid copying
	 * the data around too much.
	 *
	 * Any files found in the request will be added by their field name to the
	 * $data['files'] array.
	 *
	 * @param string $input The input from php://input.
	 * @return array Associative array of request data
	 */
	function parse_raw_http_request(string $input): array
	{
		$a_data = [];
		// read incoming data

		// grab multipart boundary from content type header
		preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);

		$boundary = null;
		// content type is probably regular form-encoded
		if (!count($matches)) {
			// Check the input to be certain there is no boundary - DISABlED AS THE PUT seems to work for now.
			// Note: This might work IF the submitted form type was Form-Data and not x-www-form-urlencoded. Will need to be tested one day.
			// $boundary = trim(substr($input, 0, strpos($input, "\n")));
			// if (!str_starts_with($boundary, '----')) {
			// we expect regular puts to contain a query string containing data
			parse_str(urldecode($input), $a_data);
			return $a_data;
			// }
		} else {
			$boundary = $matches[1];
		}

		// split content by boundary and get rid of last -- element
		$a_blocks = explode('--' . $boundary, $input);
		array_pop($a_blocks);

		// loop data blocks
		foreach ($a_blocks as $block) {
			if (empty($block)) {
				continue;
			}
			$separator = "\n\r";

			// Check if the block is a file. if it is, set a flag so  we know to grab the filename instead.
			$isFile = strpos($block, 'filename') !== FALSE;
			$line = strtok($block, $separator);

			// First line should always contain the name and content disposition.
			if ($isFile) {
				preg_match('/filename=\"([^\"]*)\"/m', $line, $matches);
			} else {
				preg_match('/name=\"([^\"]*)\"/m', $line, $matches);
			}
			$name = $matches[1];
			$val = '';

			// Check all other lines to check for the data and the content-type
			while ($line !== false) {
				// Ignore the content type if given.
				if (strpos($line, 'Content-Type') == false) {
					$val = $line;
				}
				$line = strtok($separator);
			}

			if ($isFile) {
				array_push($a_data['_FILES'], [
					'name' => [$name],
					'file' => $val
				]);
			} elseif (str_ends_with($name, '[]')) {
				$name = substr($name, 0, strlen($name) - 2);
				if (!is_array($a_data[$name] ?? null)) {
					$a_data[$name] = [];
				}
				array_push($a_data[$name], $val);
			} else {
				$a_data[$name] = $val;
			}
		}
		return $a_data;
	}

	// Check if the content type is multipart/form-data or x-www-form-urlencoded
	// If form-data, parse the php input file for each submitted FormData component
	if (str_contains($_SERVER["CONTENT_TYPE"], 'form-data')) {
		$put = parse_raw_http_request($input);
	}
	// If x-www-form-urlencoded, the values should only be delimited by ampersands like a URL.
	else {
		parse_str($input, $put);
	}

	return $put;
}
/**
 * Submits a form from a POST request.
 */
function submitForm(string $page, string $controllerName, ?array $data = null)
{
	if (!is_null($controllerName)) {
		require_once("private_core/controller/$controllerName.php");
		$controllerName = "\RipDB\\Controller\\$controllerName";
		$controller = new $controllerName($page);
		$result = $controller->submitRequest($data);

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
