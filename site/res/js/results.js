/**
 * results.js
 * Author: Mr Kaos
 * Created: 10/05/2025
 * 
 * This script provides useful interaction tools on search pages.
 */
"use strict"

let callouts = []; // Store any open callouts here so they can be closed later.
const ORDINAL_LAST = 99; // This value is used if a heading sort input does not have the ordinal attribute. In other words, it ensures that the input is placed at the end of the sorted list.

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
 * Applies a column sorting button to the specified column
 * @param {HTMLTableRowElement} th The table 
 * @param {HTMLFormElement} form The form the table ias associated with for searching.
 */
function applySort(th, form) {
	let btn = document.createElement('i');
	let input = document.createElement('input');
	let sortContainer = form.querySelector('.sorts');
	let sort = th.getAttribute('data-sort'); // Should be "ASC", "DESC" or null.
	let ordinal = th.getAttribute('data-ord') ?? ORDINAL_LAST; // Should be "ASC", "DESC" or null.
	input.hidden = true;
	input.name = 's[]';
	input.setAttribute('data-ord', ordinal == '' ? ORDINAL_LAST : ordinal);

	switch (sort) {
		case 'ASC':
			btn.innerHTML = "&#x2191;";
			input.value = `${th.id.substring(4)}-${sort}`;
			btn.onclick = e => changeSort(`${th.id.substring(4)}-DESC`, input);
			break;
		case 'DESC':
			btn.innerHTML = "&#x2193;";
			input.value = `${th.id.substring(4)}-${sort}`;
			btn.onclick = e => changeSort('', input);
			break;
		default:
			btn.innerHTML = "&#x21C5;";
			btn.onclick = e => changeSort(`${th.id.substring(4)}-ASC`, input);
			input.disabled = true;
			break;
	}

	th.appendChild(btn);
	sortContainer.appendChild(input);

	/**
	 * Changes the sort direction of the input and submits the table's form.
	 */
	function changeSort(newDirection, input) {
		if (newDirection != '') {
			input.disabled = false;
		}
		input.value = newDirection;
		form.submit();
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


/**
 * Page initialiser function.
 */
function init() {
	// Query the page for the headers of a table. If any exist, prepare them to allow sorting.
	// Only th elements with id attributes are applicable.
	let tables = document.querySelectorAll('table.table-search');

	for (let i = 0; i < tables.length; i++) {
		let tableHeads = tables[i].querySelectorAll('thead>tr>th[id]');
		let form = document.getElementById(tables[i].getAttribute('data-for'));
		let sortInputContainer = document.createElement('div');
		sortInputContainer.className = 'sorts';
		form.appendChild(sortInputContainer);
		for (let j = 0; j < tableHeads.length; j++) {
			applySort(tableHeads[j], form);
		}

		// Sort the sorting inputs to ensure they are submitted in the order the user has selected
		let sorts = sortInputContainer.querySelectorAll('input');
		let clones = Array.from(sorts).sort(compareSortOrdinal);

		sortInputContainer.innerHTML = '';
		for (let j = 0; j < clones.length; j++) {
			sortInputContainer.appendChild(clones[j])
		}
	}

	function compareSortOrdinal(inputA, inputB) {
		let ordinalA = inputA.getAttribute('data-ord') ?? ORDINAL_LAST;
		let ordinalB = inputB.getAttribute('data-ord') ?? ORDINAL_LAST;

		return parseInt(ordinalA) > parseInt(ordinalB);
	}
}

window.addEventListener('load', init);