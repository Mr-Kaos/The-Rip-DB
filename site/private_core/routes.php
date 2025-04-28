<?php

/**
 * Routes file.
 * 
 * This file contains all routes available in the web app.
 */

const PAGE_EXTENSIONS = ['html', 'php']; // All allowed webpage extensions. If found in the request URi, the client is redirected to the same page without it.
const RESOURCE_EXTENSIONS = ['css', 'js', 'ico', 'jpg', 'jpeg', 'png']; // All allowed file resource extensions. Any requests to other extensions result in a 404.

$uri = explode('?', $_SERVER['REQUEST_URI'])[0];

// List of valid routes for the web app. If the request does not fall into any of these cases, return a 404.
switch ($uri) {
	case '/':
	case '':
		displayPage('home');
		break;
	case '/home':
		displayPage($uri);
		break;
	case '/rips':
		displayPage($uri);
		break;
	default:
		http_response_code(404);
		// Check to see if a resource was requested (uri ends in a file extension). If it does, do not render page.
		preg_match('/([.{1}][a-zA-Z]*)$/', $uri, $matches);
		if (count($matches) == 0) {
			displayPage('404');
		}
}

/**
 * Displays the page with a header and footer.
 */
function displayPage(string $page, ?string $pageTitle = null): void
{
	// Include page objects that are commonly used across pages
	include_once('private_core/objects/InputElement.php');
	include_once('private_core/objects/DropDownElement.php');

	echo '<!DOCTYPE HTML><html>';
	define('PAGE_TITLE', $pageTitle ?? "The Rip Database - $page");
	require('templates/head.php');
	require('templates/nav.php');
	require("view/$page.php");

	echo "<body>";
	require('templates/footer.php');
	echo "</body></html>";
}

/**
 * Returns the string that the URI ends in.
 * @return ?string The extension the URI ends in, if it ends in one. Else, null.
 */
function uriEndsWith(string $haystack, array $needles): ?string
{
	$endsWith = null;
	foreach ($needles as $needle) {
		if (str_ends_with($haystack, ".$needle")) {
			$endsWith = ".$needle";
			break;
		}
	}

	return $endsWith;
}
