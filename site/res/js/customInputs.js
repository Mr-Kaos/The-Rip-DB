/**
 * Custom HTML elements
 * Author: Mr Kaos
 * 
 * Provides objects and handlers for custom page objects.
 */
"use strict";

// Globals for accessing inputs from outside
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

class InputTable extends CustomElement {
	#rowCount = 0;
	#body = null

	/**
	 * Constructs a object reference to an InputTable "element"
	 * @param {HTMLTableElement} element The Table element that defines the multi select dropdown.
	 */
	constructor(element) {
		super(element);
		this.#body = this.getElement().querySelector(`tbody#body_${this.getElement().id}`);

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

		// Initialise any existing/current rows
		let removeButtons = this.#body.querySelectorAll(`button[btnRemove]`);
		for (let i = 0; i < removeButtons.length; i++) {
			removeButtons[i].onclick = e => this.removeRow(removeButtons[i].parentElement.parentElement);
		}
	}

	/**
	 * Adds a row to the InputTable element
	 */
	addRow() {
		let template = this.getElement().querySelector(`thead#temp_${this.getElement().id}>tr`);
		let removeButtons = this.#body.querySelectorAll(`button[btnRemove]`);

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

		this.#body.append(clone);
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
			let button = this.getElement().querySelector('tbody button[btnRemove]');
			if (button != null) {
				button.disabled = true;
			}
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

let ev = new Event('setOption');

/**
 * JavaScript wrapper for Searchable input elements.
 * @property {Boolean} #init
 * @property {Boolean} multi Determines if this search element allows for multiple selections or just one.
 * @property {String|Array} value Stores the value sof the selected option(s). Also used to hide certain options from re-appearing in the search.
 * @event setOption This event fires when an option is selected in list of results given.
 * @event unsetOption This event fires when an option is removed from the list of selected results.
 */
class SearchElement extends CustomElement {
	#open = false;
	#optionsDiv;
	#inputElement;
	#init = false;
	#multi = false;
	#value = null;
	#searchElement = null;
	$url;
	#required = false;
	#highlighted = -1;
	#hasSearched = false; // Set to true once a search has been made.
	canAdd = true;

	constructor(element, allowRand = true) {
		super(element);
		this.#inputElement = element.querySelector(`input[type=search]`)
		this.#optionsDiv = this.getElement().querySelector('.options');
		this.#optionsDiv.style.left = `${this.getElement().offsetLeft}px`

		element.firstElementChild.onclick = e => this.toggleDisplay();

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

		/* Add event listeners */
		// If the list has not been touched yet, make an empty search to load some results
		this.#searchElement.onclick = function () {
			if (!this.#init) {
				let url = '';
				if (allowRand) {
					url = '?rand'
				}
				this.search('', this.#searchElement.getAttribute('search-url') + url);
				this.#init = true;
			} else if (this.canAdd) {
				this.toggleDisplay();
			}
		}.bind(this);

		/**
		 * Allows moving the selected option with the keyboard
		 * @param {KeyboardEvent} event 
		 */
		window.addEventListener('keydown', function (event) {
			if (this.isOpen()) {
				let unset = this.#highlighted;
				let validKey = true;

				switch (event.key) {
					case 'ArrowUp':
						event.preventDefault();
						this.#highlighted--;
						break;
					case 'ArrowDown':
						event.preventDefault();
						this.#highlighted++;
						break;
					case 'Enter':
						event.preventDefault();
						break;
					default:
						validKey = false;
				}

				if (validKey) {
					let options = this.getOptionsDiv().querySelectorAll('span');
					if (options.length > 0) {
						if (event.key == "Enter") {
							this.#setOption(options[this.#highlighted]);
						} else {
							if (this.#highlighted < 0) {
								this.#highlighted = 0;
							} else if (this.#highlighted >= options.length) {
								this.#highlighted = options.length - 1;
							}

							if (options[unset] != undefined) {
								options[unset].classList.remove('dropdown-hover');
							}
							options[this.#highlighted].classList.add('dropdown-hover');
							options[this.#highlighted].scrollIntoView();
						}
					}
				}
			}
		}.bind(this));
	}

	/**
	 * Opens up a modal to add a new item to the search element's options.
	 * Upon submission of the form, if successful, the value is automatically selected via #setOption.
	 * @param {Element} options The div element containing the search element's list of options.
	 */
	async #addItem(options) {
		let form = new FormModal(`new_item-${this.getElement().id}`, 'Add Item', this.#inputElement.getAttribute('modal'), this.#inputElement.getAttribute('modal-tgt-id'));
		form.open();

		let response = await form.onSubmit();

		let option = document.createElement('span');
		option.innerText = response[this.#inputElement.getAttribute('modal-value-key')];
		option.setAttribute('value', response['_NewID']);
		options.append(option);
		this.#setOption(option);

		this.toggleDisplay(false);
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

	/**
	 * Returns the selected value(s) of the input.
	 * @returns {String[]|String|Number[]|Number|null} The selected value(s) of the search element. If the element allows for multiple selections, an array of all selected values is returned. Else, the selected value is returned.
	 */
	getSelection() {
		return this.#value;
	}

	isOpen() {
		return this.#open;
	}

	getOptionsDiv() {
		return this.#optionsDiv;
	}

	getSearchElement() {
		return this.#searchElement;
	}

	/**
	 * If any pre-existing values exist, initialise their event listeners.
	 * @param {NodeList} elements
	 */
	#initValues(elements) {
		// If multi, get all pills and add their values to this input's value list
		if (this.#multi) {
			for (let i = 0; i < elements.length; i++) {
				this.#inputElement.required = false;
				let input = elements[i].querySelector('input');
				let btnRemove = elements[i].querySelector('button');
				this.#value.push(input.value);
				btnRemove.onclick = e => this.#unsetOption(elements[i]);
			}
		}
		// If a single-select, {elements} is the pill.
		else if (elements != null) {
			let input = elements.querySelector('input[hidden]');
			this.#inputElement.style.display = 'none';
			let btnRemove = elements.querySelector('button');
			btnRemove.onclick = e => this.#unsetOption(elements);
			this.#value = input.value;
			this.#inputElement.required = false;
		}
	}

	async search(input, url) {
		if (this.canAdd) {
			if (this.#init) {
				url += `?q=${input}`;
			}
			let response = await fetch(url);

			if (response.ok) {
				response.json().then(data => {
					let options = this.getOptionsDiv();
					options.innerHTML = '';

					// If a modal is assigned, add an "Add Item" option
					if (this.#inputElement.hasAttribute('modal')) {
						// Make sure all other modal attributes are set. If not, produce a warning.
						if (this.#inputElement.hasAttribute('modal-tgt-id') && this.#inputElement.hasAttribute('modal-value-key')) {
							let option = document.createElement('span');
							option.innerText = '< Add Item >';
							option.onclick = e => this.#addItem(options);
							options.append(option);
						} else {
							console.warn('DEV WARNING: This search element has a modal set, but is missing ts other required attributes!', this.getElement());
						}
					}

					if (data != null && data.length > 0) {
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
								option.onclick = e => this.#setOption(e.target);
								options.append(option);
							}
						}

						this.#hasSearched = true;
					} else if (this.#hasSearched && data != null && input != '') {
						options.innerHTML = '<i>No results found</i>';
					}
					if (!this.isOpen()) {
						this.toggleDisplay(true);
					}
				});
				this.#resetHighlight();
			}
		}
	}

	/**
	 * Sets the value of the SearchElement to the value the user selected.
	 * If the input allows multiple selections, they are added to a separate container.
	 * @param {HTMLSpanElement} option The option the user selected
	 */
	#setOption(option) {
		// Only add the element if adding is allowed
		if (this.canAdd && option != undefined) {
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
			option.style.display = "none";
			this.toggleDisplay();

			// Add event dispatcher for setting an option.
			option.dispatchEvent(new CustomEvent('setOption', {
				bubbles: true
			}));
		}
	}

	/**
	 * Removes the specified option from the element's value(s).
	 * @param {HTMLSpanElement} option The option to remove from the element's selection.
	 */
	#unsetOption(option) {
		let optionVal = parseInt(option.getAttribute('value'));

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

		// Re-display the option in the list
		let options = this.getOptionsDiv();
		let opt = options.querySelector(`span[value="${optionVal}"]`);
		if (opt != null) {
			opt.style.display = 'unset';
		}

		// Add event dispatcher for unsetting an option.
		option.dispatchEvent(new CustomEvent('unsetOption', {
			bubbles: true
		}));
		option.remove();
	}

	#resetHighlight() {
		this.#highlighted = -1;
	}

	isMulti() {
		return this.#multi;
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

function setupCustomInputs(mainElement) {
	let inputTableElements = mainElement.querySelectorAll('table[InputTable]');
	let dropdownElements = mainElement.querySelectorAll('select[modal]');
	let searchInputElements = mainElement.querySelectorAll('span.search-element');

	if (inputTableElements.length > 0) {
		prepareInputTables(inputTableElements);
	}

	if (searchInputElements.length > 0) {
		prepareSearchElements(searchInputElements);
	}

	if (dropdownElements.length > 0) {
		prepareDropdownElements(dropdownElements);
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

	/**
	 * Prepares search input elements on the page and creates a window event listener to ensure only one is open at a time.
	 * @param {NodeListOf<Element>} elements 
	 */
	function prepareSearchElements(elements) {
		for (let i = 0; i < elements.length; i++) {
			searchElements.push(new SearchElement(elements[i]));
		}

		// Close any search elements if they are clicked out of.
		window.addEventListener('click', function (e) {
			for (let i = 0; i < searchElements.length; i++) {
				let el = findParentElement(e.target, { class: ['search-element'] });
				if (el !== null) {
					if (searchElements[i].isOpen() && (el.className != 'search-element')) {
						searchElements[i].toggleDisplay(false);
					} else if (searchElements[i].getElement() != el) {
						searchElements[i].toggleDisplay(false);
					}
				} else {
					searchElements[i].toggleDisplay(false);
				}
			}
		});
	}

	/**
	 * Checks any dropdown (select) elements on the page if they require a modal to add new items.
	 * @param {NodeListOf<HTMLSelectElement>} elements 
	 */
	function prepareDropdownElements(elements) {
		console.error("TODO");
	}
}

window.onload = e => setupCustomInputs(document);