/**
 * results.js
 * Author: Mr Kaos
 * Created: 10/05/2025
 * 
 * This script provides useful interaction tools on the search page (/rips).
 */

let callouts = []; // Store any open callouts here so they can be closed later.

/**
 * Opens a dialog pop-up of the selected button from a row's column and displays its related data and options.
 * @param {HTMLButtonElement} button The button the user pressed
 */
function openRowBubble(button) {
	const CALLOUT_ARROW_HEIGHT = 20;
	let rect = button.getBoundingClientRect();
	let callout = document.createElement('div');
	callout.className = 'callout down';
	let mainLink = document.createElement('a');
	mainLink.href = `rips?${button.getAttribute('data-type')}[]=${button.getAttribute('data-id')}`;
	mainLink.innerText = 'Apply as search filter';
	callout.append(mainLink);

	let extraData = button.getAttribute('data-extra');
	if (extraData != null) {
		
		// extraData = JSON.parse(extraData);
		// for (key in extraData) {
		// 	let extraTag = document.createElement('button');
		// 	extraTag.innerText = extraData[key];
		// 	extraTag.value = key;
		// 	callout.append(extraTag);

		// }
	}

	// Build and add the callout to the page.
	document.body.append(callout);
	callouts.push(callout);

	// Position the callout above the middle of the button.
	callout.style.left = `${rect.left + (rect.width / 2) - (callout.getBoundingClientRect().width / 2)}px`;
	callout.style.top = `${rect.top - rect.height - CALLOUT_ARROW_HEIGHT}px`;

}

/**
 * If any part of the window is clicked that is not a callout, close any open callouts.
 * There should only ever be one callout open at a time, but in case multiple manage to open, this will close them all.
 */
window.addEventListener('click', function(e) {
	if (!e.target.classList.contains('callout') && (e.target.tagName != 'BUTTON' || callouts.length > 1)) {
		for (let i = 0; i < callouts.length; i++) {
			callouts[i].remove();
			callouts.splice(i, 1);
		}
	}
});