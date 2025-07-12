/**
 * account.js
 * Author: Mr Kaos
 * Description: This script contains helper functions for validating usernames and other basic account stuff.
 */

/**
 * Checks of the username is already taken.
 * @param {HTMLInputElement} input The input element responsible for storing the username.
 */


async function checkUsername(input) {
	let og = input.getAttribute('data-og');
	let valid = false;
	let pattern = /^(?=.{3,32}$)(?!.*[{}\\|\/;,\[\]^@])[a-zA-Z0-9._+-~]+$/g;

	if (input.value.match(pattern) == null) {
		displayErrorMessage(input, "This username contains invalid characters.", NotificationPriority.Error);
	}
	// Ensure the minimum requirements are met before searching
	else if (input.value.length >= 3 && input.value.length <= 32 && input.value != og) {
		let request = await fetch(`/account/check-username?username=${input.value}`, {
			method: 'GET'
		});

		if (request.ok) {
			if (valid = await request.json()) {
				displayErrorMessage(input, "Username OK!", NotificationPriority.Success);
			} else {
				displayErrorMessage(input, "This username is taken/not allowed.", NotificationPriority.Error);
			}
		}
	} else if (input.value != og) {
		displayErrorMessage(input, 'The username must be between 3 and 32 characters!');
	} else {
		displayErrorMessage(input);
	}

	return valid;
}