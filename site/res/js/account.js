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

/**
 * Checks that the two passwords match in real-time.
 * @param {HTMLInputElement} input The password input being used
 * @param {String} otherInputID The input of the other password input that must match the current one.
 */
function checkPasswordMatch(input, otherInputID) {
	let password2 = document.getElementById(otherInputID);
	let valid = false;
	let msg = null;
	let btnSubmit = input.form.querySelector('button[type="submit"]');

	// If one or both inputs are empty, do not display messages
	if (input.value != '' && password2.value != '') {
		if (input.value != password2.value) {
			msg = 'The passwords do not match!'
		} else if (input.value.length < 6) {
			msg = 'The password must be at least 6 characters!';
		} else {
			valid = true;
		}
	}

	displayErrorMessage(input, msg);
	displayErrorMessage(password2, msg);

	btnSubmit.disabled = !valid;
}

/**
 * Checks if the user has any unclaimed playlists created while not logged in stored as cookies.
 * This function is called when in the "playlists" sub-page.
 */
async function checkForUnclaimedPlaylists() {
	let codes = getCookie('claimCodes');

	if (codes != null) {
		let functions = {
			'Save Playlists': {
				className: 'btn-good',
				function: function () { savePlaylists(codes) }
			},
			'Remind me Later': {
				className: 'btn-ok'
			},
			'Delete them': {
				className: 'btn-bad',
				function: clearPlaylistCookie
			}
		};
		let text = document.createElement('div');
		text.style.textAlign = 'center';
		text.innerHTML = '<p>You have created some playlists while not logged in.</p><p>Would you like to save these to your account?</p>';
		let modal = new Modal('playlist-claim', 'You have claimable playlists!', text, null, null, false, true, functions);
		modal.open();
	} else {
		console.log()
	}
}

/**
 * Saves the given unclaimed playlists to the account.
 * @param {Array} claimCodes An array of claim codes to use to save their associated playlists to their account.
 */
async function savePlaylists(claimCodes) {
	let data = new FormData();
	data.append('ClaimCodes', claimCodes);
	let request = await fetch('/playlist/claim', {
		method: 'POST',
		body: data
	});

	if (request.ok) {
		// The json response will either be true or a string containing an error message.
		let success = await request.json();


		if (success == true) {
			displayNotification('Successfully saved playlists to your account!');
		} else {
			displayNotification(success, NotificationPriority.Error);
		}

		clearPlaylistCookie();
		window.location.reload();
	}
}

/**
 * Clears all playlist claim codes form the client's cookies.
 */
function clearPlaylistCookie() {
	deleteCookie('claimCodes');
}

window.addEventListener('load', (e) => {
	if (window.location.pathname == '/account/playlists') {
		checkForUnclaimedPlaylists();
	}
});