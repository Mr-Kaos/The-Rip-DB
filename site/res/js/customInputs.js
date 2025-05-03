/**
 * Custom HTML elements
 */

class MultiSelect {
	#element;
	#open = false;
	#optionsDiv;

	/**
	 * 
	 * @param {HTMLElement} element The element that defines the multi select dropdown.
	 */
	constructor(element) {
		this.#element = element;
		this.#optionsDiv = this.#element.querySelector('.options');

		this.#optionsDiv.style.left = `${this.#element.offsetLeft}px`

		element.firstElementChild.onclick = e => this.toggleDisplay();
	}

	/**
	 * Displays the options in the multi select.
	 * @param {Boolean} open Determines if the multi select is to be displayed or not. If null, it toggles to the opposite of its current state.
	 */
	toggleDisplay(open = null) {
		if (open == null) {
			this.#open = !this.#open;
		} else {
			this.#open = open;
		}
		// console.log(this.#open, this);

		if (this.#open) {
			this.#optionsDiv.style.display = 'flex';
		} else {
			this.#optionsDiv.style.display = 'none';
		}
	}

	isOpen() {
		return this.#open;
	}

	getElement() {
		return this.#element;
	}
}

class InputTable {
	#element;
	#rowCount = 0;

	/**
	 * Constructs a object reference to an InputTable "element"
	 * @param {HTMLTableElement} element The Table element that defines the multi select dropdown.
	 */
	constructor(element) {
		this.#element = element;

		// Add an empty row to the table to initialise it.
		this.addRow();

		let addButton = element.querySelector('tfoot button');
		addButton.onclick = e => this.addRow();
	}

	/**
	 * Adds a row to the InputTable element
	 */
	addRow() {
		let template = this.#element.querySelector('thead>tr');
		let body = this.#element.querySelector('tbody');
		let removeButtons = this.#element.querySelectorAll('tbody button[btnRemove');

		let clone = template.cloneNode(true);
		clone.style = null;

		// Remove the null form attribute from the template to add the cloned inputs to the form.
		let inputs = clone.querySelectorAll('input,select,textarea');
		for (let i = 0; i < inputs.length; i++) {
			inputs[i].removeAttribute('form');
		}

		// Ensure that the remove buttons are not disabled if there is more than one row.
		for (let i = 0; i < removeButtons.length; i++) {
			removeButtons[i].disabled = !(this.#rowCount > 0);
		}
		let removeButton = clone.querySelector('button[btnRemove')
		removeButton.disabled = !(this.#rowCount > 0);
		removeButton.onclick = e => this.removeRow(clone);

		body.append(clone);
		this.#rowCount++;
	}

	/**
	 * Removes the specified row from the InputTable element
	 * @param {HTMLTableRowElement} row The row element to remove.
	 */
	removeRow(row) {
		row.remove();
		this.#rowCount--;

		// Ensure that the remove buttons are disabled if there is exactly one row. (There should always be one row when #rowCount is 1.)
		if (this.#rowCount == 1) {
			this.#element.querySelector('tbody button[btnRemove').disabled = true;
		}
	}
}

/**
 * Finds a parent element from the given child based on a specific attribute.
 * @param {Element} child The source element to search for its parent.
 * @param {Object} attributes A list of attributes the required parent element has. The keys must be a valid HTML attribute and the value must correspond to it.
 * @param {Number} depth The maximum number of loops to perform before the loop terminates. Absolute maximum is 128. 
 */
function findParentElement(child, attributes, depth = 32) {
	let loops = 0;
	let maxLoops = (depth > 128) ? 128 : (depth < 0 ? 1 : depth);
	let parent = null;
	let thisParent = child.parentElement;

	while (parent == null && thisParent != null && loops < maxLoops) {
		// Check each attribute against the current parent
		let matches = 0;
		for (let attr in attributes) {
			if (attributes[attr] === null && thisParent.hasAttribute(attr)) {
				matches++;
			}
			else if (thisParent.hasAttribute(attr) && thisParent.getAttribute(attr) == attributes[attr]) {
				matches++;
			}
		}

		// If the number of matched attributes matches the number of required attributes, set the parent to the current one and exit.
		if (matches == Object.keys(attributes).length) {
			parent = thisParent;
			break;
		}
		loops++;
		thisParent = thisParent.parentElement;
	}

	return parent;
}

function setupCustomInputs() {
	let multiSelectElements = document.querySelectorAll('span.multi-select');
	let inputTableElements = document.querySelectorAll('table[InputTable]');
	let multiSelects = [];
	let inputTables = [];

	if (multiSelectElements.length > 0) {
		prepareMultiSelects(multiSelectElements);
	}

	if (inputTableElements.length > 0) {
		prepareInputTables(inputTableElements);
	}

	/**
	 * Prepares any multi-select elements on the page.
	 * @param {NodeListOf<Element>} elements 
	 */
	function prepareMultiSelects(elements) {
		for (let i = 0; i < elements.length; i++) {
			multiSelects.push(new MultiSelect(elements[i]));
		}

		// Close any multi-select elements if they are clicked out of.
		window.addEventListener('click', function (e) {
			for (let i = 0; i < multiSelects.length; i++) {
				let el = findParentElement(e.target, { class: 'multi-select' });
				if (el !== null) {
					if (multiSelects[i].isOpen() && el.className != 'multi-select') {
						multiSelects[i].toggleDisplay(false);
					} else if (multiSelects[i].getElement() != el) {
						multiSelects[i].toggleDisplay(false);
					}
				} else {
					multiSelects[i].toggleDisplay(false);
				}
			}
		});
	}

	/**
	 * Prepares any InputTable elements on the page.
	 * @param {NodeListOf<Element>} elements 
	 */
	function prepareInputTables(elements) {
		for (let i = 0; i < elements.length; i++) {
			console.log(elements[i]);
			inputTables.push(new InputTable(elements[i]));
		}
	}
}

window.onload = setupCustomInputs;