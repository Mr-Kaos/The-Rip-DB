/**
 * Custom HTML elements
 * Author: Mr Kaos
 * 
 * Provides objects and handlers for custom page objects.
 */

// Globals for accessing inputs from outside
let multiSelects = [];
let inputTables = [];

class CustomElement {
	#element;

	constructor(element) {
		this.#element = element;

	}

	getElement() {
		return this.#element;
	}
}

class MultiSelect extends CustomElement {
	#open = false;
	#optionsDiv;

	/**
	 * 
	 * @param {HTMLElement} element The element that defines the multi select dropdown.
	 */
	constructor(element) {
		super(element);
		this.#optionsDiv = this.getElement().querySelector('.options');
		this.#optionsDiv.style.left = `${this.getElement().offsetLeft}px`

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
}

class InputTable extends CustomElement {
	#rowCount = 0;

	/**
	 * Constructs a object reference to an InputTable "element"
	 * @param {HTMLTableElement} element The Table element that defines the multi select dropdown.
	 */
	constructor(element) {
		super(element);
		// Add an empty row to the table to initialise it.
		this.addRow();

		let addButton = element.querySelector(`#add_${element.id}`);
		addButton.onclick = e => this.addRow();
	}

	/**
	 * Adds a row to the InputTable element
	 */
	addRow() {
		let template = this.getElement().querySelector(`thead#temp_${this.getElement().id}>tr`);
		let body = this.getElement().querySelector(`tbody#body_${this.getElement().id}`);
		let removeButtons = this.getElement().querySelectorAll(`tbody#body_${this.getElement().id} button[btnRemove]`);

		let clone = template.cloneNode(true);
		clone.style = null;

		// Remove the null form attribute from the template to add the cloned inputs to the form.
		// Also create a unique ID for each input so the labels are associated correctly.
		let inputs = clone.querySelectorAll('input,select,textarea');
		for (let i = 0; i < inputs.length; i++) {
			let label = inputs[i].parentElement.querySelector(`label[for="${inputs[i].id}"]`);
			let newId = `${inputs[i].id}_${this.uniqid()}`;
			inputs[i].removeAttribute('form');
			inputs[i].id = newId;
			label.setAttribute('for', newId);
		}

		// Ensure that the remove buttons are not disabled if there is more than one row.
		for (let i = 0; i < removeButtons.length; i++) {
			removeButtons[i].disabled = !(this.#rowCount > 0);
		}
		let removeButton = clone.querySelector('button[btnRemove]');
		removeButton.disabled = !(this.#rowCount > 0);
		removeButton.onclick = e => this.removeRow(clone);

		body.append(clone);
		this.#rowCount++;
	}

	/**
	 * Finds the InputTable with the given id and adds a row to it.
	 * @param {Element} id 
	 */
	static addRow(id) {
		for (let i = 0; i < inputTables.length; i++) {
			if (inputTables[i].getElement().id == id) {
				inputTables[i].addRow();
				break;
			}
		}
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
			getElement().querySelector('tbody button[btnRemove]').disabled = true;
		}
	}

	/**
	 * Code obtained from https://stackoverflow.com/questions/4872380/uniqid-in-javascript-jquery
	 * Generates a unique id. used to ensure that a nested InputTable has a unique id if its parent table creates a new row.
	 */
	uniqid(prefix = "", random = false) {
		const sec = Date.now() * 1000 + Math.random() * 1000;
		const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");
		return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}` : ""}`;
	};
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
		// Reverse list so the deepest nodes get configured first.
		elements = [...elements];
		elements.reverse();
		for (let i = 0; i < elements.length; i++) {
			inputTables.push(new InputTable(elements[i]));
		}
	}
}

window.onload = setupCustomInputs;