/**
 * results.js
 * Author: Mr Kaos
 * Created: 10/05/2025
 * 
 * This script provides useful interaction tools on the search page (/rips).
 */
"use strict"

let callouts = []; // Store any open callouts here so they can be closed later.

/**
 * Opens a dialog pop-up of the selected button from a row's column and displays its related data and options.
 * @param {HTMLButtonElement} button The button the user pressed
 */
function openRowBubble(button) {
	let rect = button.getBoundingClientRect();
	let nameKey = '';
	let filterName = '';
	let url = 'rips';
	switch (button.getAttribute('data-type')) {
		case 'jokes':
			nameKey = 'TagName';
			filterName = 'tags';
			break;
		case 'meta-jokes':
			nameKey = 'MetaName';
			filterName = 'metas';
			url = 'jokes';
			break;
	}

	let callout = buildCallout(button.getAttribute('data-type'), button.getAttribute('data-id'), button.getAttribute('data-extra'), nameKey, url, filterName);

	// Add the callout to the page.
	document.body.append(callout);
	callouts.push(callout);

	// Position the callout above the middle of the button.
	let calloutRect = callout.getBoundingClientRect();
	callout.style.left = `${rect.left + (rect.width / 2) - (calloutRect.width / 2)}px`;
	// bottom y-coordinate of button + window y-coord - height of callout and button and the height of the callout's fin.
	callout.style.top = `${rect.bottom + window.scrollY - calloutRect.height - rect.height - 15}px`;

	/**
	 * Creates the callout element.
	 * @param {String} type This should be a valid filter name for the rips page
	 * @param {Number} id 
	 * @param {JSON} extraData 
	 * @param {String} nameKey The name of the key that stores the name of the button.
	 * @returns 
	 */
	function buildCallout(type, id, extraData, nameKey, targetUrl, filterName) {
		let callout = document.querySelector(`#templates>#callout-${type}`).cloneNode(true);
		let mainLink = callout.querySelector('a');
		callout.removeAttribute('id');
		mainLink.href = `rips?${type}[]=${id}`;

		if (extraData != null) {
			let dataContainer = callout.querySelector('div.extras');
			extraData = JSON.parse(extraData);
			for (let key in extraData) {
				let extraTag = document.createElement('button');
				extraTag.innerText = extraData[key][nameKey];
				extraTag.value = key;
				if (extraData[key].IsPrimary) {
					extraTag.style.fontWeight = 'bold';
				}
				let url = `${targetUrl}?${filterName}[]=${key}`;
				extraTag.onclick = function () {
					window.location = url;
				}
				dataContainer.append(extraTag);
			}
		}

		return callout;
	}
}

/**
 * Toggles the display of a filter element on a search page
 * @param {HTMLElement} element The filter element to toggle
 */
function toggleFilters(element) {
	let container = element.nextElementSibling;
	if (container.hasAttribute('open')) {
		container.removeAttribute('open');
		element.removeAttribute('open');
	} else {
		container.setAttribute('open', true);
		element.setAttribute('open', true);
	}
}

/**
 * If any part of the window is clicked that is not a callout, close any open callouts.
 * There should only ever be one callout open at a time, but in case multiple manage to open, this will close them all.
 */
window.addEventListener('click', function (e) {
	if (!e.target.classList.contains('callout') && (e.target.tagName != 'BUTTON' || callouts.length > 1)) {
		for (let i = 0; i < callouts.length; i++) {
			callouts[i].remove();
			callouts.splice(i, 1);
		}
	}
});