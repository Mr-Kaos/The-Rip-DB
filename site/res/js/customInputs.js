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
	let multiSelects = [];

	for (let i = 0; i < multiSelectElements.length; i++) {
		multiSelects.push(new MultiSelect(multiSelectElements[i]));
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

window.onload = setupCustomInputs;