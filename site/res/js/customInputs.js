/**
 * Custom HTML elements
 * Author: Mr Kaos
 * 
 * Provides objects and handlers for custom page objects.
 */
"use strict";

// Globals for accessing inputs from outside
let multiSelects = [];
let inputTables = [];
let searchElements = [];

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
	#inputElement;

	/**
	 * 
	 * @param {HTMLElement} element The element that defines the multi select dropdown.
	 */
	constructor(element) {
		super(element);
		this.#inputElement = element.querySelector(`input[type=search]`)
		this.#optionsDiv = this.getElement().querySelector('.options');
		this.#optionsDiv.style.left = `${this.getElement().offsetLeft}px`

		element.firstElementChild.onclick = e => this.toggleDisplay();
	}

	/**
	 * Displays the options in the multi select.
	 * @param {Boolean} open Determines if the multi select is to be displayed or not. If null, it toggles to the opposite of its current state.
	 */
	toggleDisplay(open = null) {
		this.#optionsDiv.style.left = `${this.#inputElement.offsetLeft}px`;
		this.#inputElement.clientWidth;
		this.#optionsDiv.style.minWidth = `${this.#inputElement.clientWidth}px`;
		if (open == null) {
			this.#open = !this.#open;
		} else {
			this.#open = open;
		}

		if (this.#open) {
			this.#optionsDiv.style.display = 'flex';
		} else {
			this.#optionsDiv.style.display = 'none';
		}
	}

	isOpen() {
		return this.#open;
	}

	getOptionsDiv() {
		return this.#optionsDiv;
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

		// If values are prefilled, do not add an empty row.
		if (element.getAttribute('data-value')) {
			this.#rowCount = element.querySelectorAll('tbody>tr').length;
		}
		// Else, add an empty row to the table to initialise it.
		else {
			this.addRow();
		}

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
		let inputs = clone.querySelectorAll('input,select,textarea,span.search-element');
		for (let i = 0; i < inputs.length; i++) {
			// If a search input, construct an instance of it
			if (inputs[i].className == 'search-element') {
				searchElements.push(new SearchElement(inputs[i]));
			} else {
				let label = inputs[i].parentElement.querySelector(`label[for="${inputs[i].id}"]`);
				let newId = `${inputs[i].id}_${this.uniqid()}`;
				inputs[i].removeAttribute('form');
				inputs[i].id = newId;
				label.setAttribute('for', newId);
			}
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
			this.getElement().querySelector('tbody button[btnRemove]').disabled = true;
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
 * JavaScript wrapper for Searchable input elements.
 * @property {Boolean} #init
 * @property {Boolean} multi Determines if this search element allows for multiple selections or just one.
 * @property {String|Array} value Stores the value sof the selected option(s). Also used to hide certain options from re-appearing in the search.
 */
class SearchElement extends MultiSelect {
	#init = false;
	#multi = false;
	#value = null;
	#searchElement = null;
	$url;
	#required = false;

	constructor(element) {
		super(element);

		this.#searchElement = element.querySelector('input[type="search"]');
		this.#required = this.#searchElement.required;
		this.#searchElement.oninput = e => this.search(e.target.value, this.#searchElement.getAttribute('search-url'));
		if (element.getAttribute('type') == 'multi') {
			this.#multi = true;
			this.#value = [];
			this.#initValues(element.querySelectorAll('div.selected>span.pill'));
		} else {
			this.#initValues(element.querySelector('span.pill'));
		}

		// If the list has not been touched yet, make an empty search to load some results
		this.#searchElement.onclick = function () {
			if (!this.#init) {
				this.search('', this.#searchElement.getAttribute('search-url'));
				this.#init = true;
			} else {
				this.toggleDisplay();
			}
		}.bind(this);
	}

	/**
	 * If any pre-existing values exist, initialise their event listeners.
	 * @param {NodeList} elements
	 */
	#initValues(elements) {
		if (this.#multi) {
			for (let i = 0; i < elements.length; i++) {
				let input = elements[i].querySelector('input');
				let btnRemove = elements[i].querySelector('button');
				this.#value.push(input.value);
				btnRemove.onclick = e => this.#unsetOption(elements[i]);
			}
		} else if (elements != null) {
			let input = this.getElement().querySelector('input[type=search]');
			input.style.display = 'none';
		}
	}

	async search(input, url) {
		let response = await fetch(`${url}?q=${input}`);

		if (response.ok) {
			let result = response.json().then(data => {
				let options = this.getOptionsDiv();
				options.innerHTML = '';

				if (data.length > 0) {
					let noAdd = true;
					for (let i = 0; i < data.length; i++) {
						if (this.#multi) {
							noAdd = this.#value.includes(data[i].ID);
						} else {
							noAdd = (data[i].ID == this.#value);
						}
						if (!noAdd) {
							let option = document.createElement('span');
							option.innerText = data[i].NAME;
							option.setAttribute('value', data[i].ID);
							option.onclick = e => this.#selectOption(e.target);
							options.append(option);
						}

					}
				} else {
					options.innerHTML = '<i>No results found</i>';
				}
				if (!this.isOpen()) {
					this.toggleDisplay(true);
				}
			});
		}
	}

	/**
	 * Sets the value of the SearchElement to the value the user selected.
	 * If the input allows multiple selections, they are added to a separate container.
	 * @param {HTMLSpanElement} option The option the user selected
	 */
	#selectOption(option) {
		let clone = option.cloneNode(true);
		let input = document.createElement('input');
		input.hidden = true;
		input.value = option.getAttribute('value');
		input.name = this.getElement().getAttribute('name');
		let btnRemove = document.createElement('button');
		btnRemove.innerHTML = '&times;';
		btnRemove.type = 'button';
		btnRemove.onclick = e => this.#unsetOption(clone);
		clone.className = 'pill';
		clone.appendChild(input);
		clone.appendChild(btnRemove);

		if (this.#multi) {
			let selectionDiv = this.getElement().querySelector('.selected');
			selectionDiv.appendChild(clone);
			this.#value.push(parseInt(option.getAttribute('value')));
		} else {
			this.#searchElement.style.display = 'none';
			this.getElement().append(clone);
			this.#value = parseInt(option.getAttribute('value'));
		}
		// Remove the required attribute (if it was set) so the form can be submitted.
		this.#searchElement.required = false;
		option.remove();
		console.log(this.#value);
	}

	/**
	 * Removes the specified option from the element's value(s).
	 * @param {HTMLSpanElement} option The option to remove from the element's selection.
	 */
	#unsetOption(option) {
		let optionVal = parseInt(option.getAttribute('value'));
		option.remove();

		if (this.#multi) {
			this.#value.splice(this.#value.indexOf(optionVal), 1);
			// If all values are removed and the input is required, set the required attribute back.
			if (this.#value.length <= 0 && this.#required) {
				this.#searchElement.required = true;
			}
		} else {
			this.#value = null;
			this.#searchElement.removeAttribute('style');
			if (this.#required) {
				this.#searchElement.required = true;
			}
		}
	}
}

/**
 * Finds a parent element from the given child based on a specific attribute.
 * @param {Element} child The source element to search for its parent.
 * @param {Object} attributes A list of attributes the required parent element has. The keys must be a valid HTML attribute and the value must correspond to it. An array of values may also be specified if searching for one of multiple potential values.
 * @param {Number} depth The maximum number of loops to perform before the loop terminates. Absolute maximum is 64. 
 */
function findParentElement(child, attributes, depth = 32) {
	let loops = 0;
	let maxLoops = (depth > 64) ? 64 : (depth < 0 ? 1 : depth);
	let parent = null;
	let thisParent = child.parentElement;

	while (parent == null && thisParent != null && loops < maxLoops) {
		// Check each attribute against the current parent
		let matches = 0;
		for (let attr in attributes) {
			if (Array.isArray(attributes[attr])) {
				for (let i = 0; i < attributes[attr].length; i++) {
					if (attributes[attr][i] === null && thisParent.hasAttribute(attr)) {
						matches++;
						break;
					}
					else if (thisParent.hasAttribute(attr) && thisParent.getAttribute(attr) == attributes[attr][i]) {
						matches++;
						break;
					}
				}
			} else {
				if (attributes[attr] === null && thisParent.hasAttribute(attr)) {
					matches++;
				}
				else if (thisParent.hasAttribute(attr) && thisParent.getAttribute(attr) == attributes[attr]) {
					matches++;
				}
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
	let searchInputElements = document.querySelectorAll('span.search-element');

	if (multiSelectElements.length > 0) {
		prepareMultiSelects(multiSelectElements);
	}

	if (inputTableElements.length > 0) {
		prepareInputTables(inputTableElements);
	}

	if (searchInputElements.length > 0) {
		prepareSearchElements(searchInputElements);
	}

	/**
	 * Prepares any multi-select elements on the page.
	 * @param {NodeListOf<Element>} elements 
	 */
	function prepareMultiSelects(elements) {
		for (let i = 0; i < elements.length; i++) {
			multiSelects.push(new MultiSelect(elements[i]));
		}
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

	function prepareSearchElements(elements) {
		for (let i = 0; i < elements.length; i++) {
			searchElements.push(new SearchElement(elements[i]));
		}
	}

	// Close any multi-select elements if they are clicked out of.
	window.addEventListener('click', function (e) {
		let inputs = multiSelects.concat(searchElements);
		for (let i = 0; i < inputs.length; i++) {
			let el = findParentElement(e.target, { class: ['multi-select', 'search-element'] });
			if (el !== null) {
				if (inputs[i].isOpen() && (el.className != 'multi-select' && el.className != 'search-element')) {
					inputs[i].toggleDisplay(false);
				} else if (inputs[i].getElement() != el) {
					inputs[i].toggleDisplay(false);
				}
			} else {
				inputs[i].toggleDisplay(false);
			}
		}
	});
}

window.onload = setupCustomInputs;