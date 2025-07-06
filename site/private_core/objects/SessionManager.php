<?php

namespace RipDB;
// This file contains functions useful for checking a client's session and authentication for certain pages and functions.

const AUTH_USER = 'USER_ID';
const ERRORS = 'ERROR_MESSAGES';
require_once('private_core/objects/DataValidators.php');

/**
 * Initialises a session if one is not already started.
 */
function initSession()
{
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
}

/**
 * Checks the user's authentication. Used to check access to certain pages and functionalities.
 * @return bool True if the user is authenticated and allowed to perform the requested action. False otherwise 
 */
function checkAuth(): bool
{
	$authorised = false;

	if (($_SESSION[AUTH_USER] ?? null) !== null) {
		$authorised = true;
	}

	return $authorised;
}

/**
 * Adds a notification to display upon next page load.
 */
function addNotification(string $message, NotificationPriority $priority = NotificationPriority::Default)
{
	if (is_array($_SESSION[ERRORS] ?? null)) {
		$_SESSION[ERRORS] = [];
	}

	$_SESSION[ERRORS][$message] = $priority;
}
