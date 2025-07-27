/**
 * PlaylistCreator.js
 * 
 * Author: Mr Kaos
 * Created: 26/07/2025
 * This is a script that allows the creation of playlists using the rips page to search for rips.
 * When creating the playlist, selected rips are stored in the local browser storage and when the user is finished, submits them to the database.
 */

let playlist = null;

/**
 * Playlist object.
 * Contains functions to aid in managing the structure of the playlist.
 * @property {Array} rips The rips IDs in the playlist
 * @property {Array} names The names of the rips in the playlist. The names should share the same index as its rip ID from #rips.
 * @property {String} name The name of the playlist.
 */
class Playlist {
	#rips = [];
	#names = [];
	#name;
	#form;

	/**
	 * Creates a playlist object.
	 * @param {Number[]} rips IDs of rips in the playlist
	 * @param {String} name The name of the playlist.
	 */
	constructor(rips = [], names = [], name = 'My Playlist') {
		// Ensure all rips are numbers. If one is not, clear the array.
		if (Array.isArray(rips)) {
			for (let i = 0; i < rips.length; i++) {
				if (isNaN(rips[i])) {
					console.warn('An ID in the stored Rips was not numeric. The playlist has been reset!');
					rips = [];
					break;
				}
			}
		} else {
			rips = [];
		}

		if (!Array.isArray(names)) {
			names = [];
		}

		// Check if the length of the names and rips are identical. If not, reset them.
		if (rips.length != names.length) {
			rips = [];
			names = [];
		}

		this.#rips = rips;
		this.#name = name;
		this.#names = names;
		this.#form = document.getElementById('playlist-creator');
	}

	/**
	 * Adds the specified Rip ID to the playlist. If the rip already exists, it is not added.
	 * @param {Number} id The ID of the rip to add
	 * @param {String} name The name of the rip. Only used to display to the user.
	 */
	addRip(id, name) {
		if (!isNaN(id)) {
			if (this.hasRip(id)) {
				displayNotification("This rip already exists in the playlist!", NotificationPriority.Warning);
			} else {
				this.#rips.push(id);
				this.#names.push(name);
			}
		}

		this.#appendToForm(id, name);
		this.saveToStorage();
	}

	/**
	 * Removes the specified Rip ID from the playlist.
	 * @param {Number} id The ID of the rip to remove
	 */
	removeRip(id) {
		if (this.hasRip(id)) {
			let ripIndex = this.#rips.indexOf(id);
			this.#rips.splice(ripIndex, 1);
			this.#names.splice(ripIndex, 1);
			this.saveToStorage();

			// Remove the rip from the form too
			let list = this.#form.querySelector('details');
			list.children[ripIndex + 1].remove();

			// Check if the rip exists on the page's table, and if so, update the "add/remove" button to its "add" state.
			let rows = document.querySelectorAll('#results>tbody>tr');
			for (let i = 0; i < rows.length; i++) {
				if (rows[i].getAttribute('rip-id') == id) {
					let btn = rows[i].firstChild.querySelector('button');
					btn.classList.remove('btn-bad');
					btn.innerText = "+";
					btn.title = 'Add to playlist';
					break;
				}
			}
		}
	}

	/**
	 * Checks if the given rip ID exists in the playlist already.
	 * @param {Number} id The ID of the rip to check.
	 * @return {Boolean} True if the rip ID exists. False otherwise
	 */
	hasRip(id) {
		return this.#rips.includes(id);
	}

	/**
	 * Moves the position of the specified rip in the playlist.
	 * @param {Number} id The ID of the rip
	 * @param {Number} moveTo The index to move the rip to.
	 */
	moveRip(id, moveTo) {
		// Check the rip exists
		if (this.hasRip(id)) {
			// Ensure the new position is valid.
			if (moveTo > this.#rips.length) {
				moveTo = this.#rips.length;
			} else if (moveTo < 0) {
				moveTo = 0;
			}
			let ripIndex = this.#rips.indexOf(id);

			[this.#rips[ripIndex], this.#rips[moveTo]] = [this.#rips[moveTo], this.#rips[ripIndex]];
			[this.#names[ripIndex], this.#names[moveTo]] = [this.#names[moveTo], this.#names[ripIndex]];
			this.saveToStorage();
		}
	}

	/**
	 * Saves the playlist's data to the session storage.
	 */
	saveToStorage() {
		sessionStorage.setItem('playlist-rips', this.#rips);
		sessionStorage.setItem('playlist-names', JSON.stringify(this.#names));
		sessionStorage.setItem('playlist-name', this.#name);
	}

	/**
	 * Updates the playlist creator form to display all rips and their order in the playlist.
	 */
	updateForm() {
		for (let i = 0; i < this.#rips.length; i++) {
			this.#appendToForm(this.#rips[i], this.#names[i]);
		}

		let inputName = this.#form.querySelector('#playlist-name');
		inputName.value = this.#name;
	}

	/**
	 * Adds a rip to the playlist form for reordering and display
	 * @param {Number} id The ID of the rip.
	 * @param {String} name The name of the rip
	 */
	#appendToForm(id, name) {
		let list = this.#form.querySelector('details');
		let row = document.createElement('div');
		let cellMove = document.createElement('div');
		let cellName = document.createElement('a');
		let btnUp = document.createElement('i');
		let btnDown = document.createElement('i');
		let btnRemove = document.createElement('button');

		// Set up buttons
		let br = document.createElement('br');
		btnUp.innerHTML = "&#x25b2;"
		btnUp.onclick = e => this.#moveRow(row, true, id)
		btnDown.innerHTML = "&#x25bc;"
		btnDown.onclick = e => this.#moveRow(row, false, id);
		cellMove.append(btnUp, br, btnDown);

		btnRemove.innerHTML = '&times;';
		btnRemove.title = 'Remove';
		btnRemove.className = 'btn-bad';
		btnRemove.onclick = e => this.removeRip(id);

		// Set name
		cellName.innerText = name;
		cellName.href = `/rips/${id}`;

		// Build row
		row.style.display = 'flex';
		row.style.alignItems = 'center';
		row.append(cellMove, cellName, btnRemove);
		list.appendChild(row);
	}

	/**
	 * Moves the given row up or down. Also adjusts the position of row's rip in the playlist.
	 * @param {HTMLElement} row The row to move.
	 * @param {Boolean} up The direction to move the row. True is up, false is down.
	 */
	#moveRow(row, up, ripId) {
		let swapRow;
		let clone;
		let ripIndex = this.#rips.indexOf(ripId);

		if (up && row.previousElementSibling.tagName != 'summary') {
			swapRow = row.previousElementSibling;
			clone = row.cloneNode(true);
			this.moveRip(ripId, ripIndex - 1);
		} else if (!up && row.nextElementSibling !== null) {
			clone = row.cloneNode(true);
			swapRow = row.nextElementSibling;
			this.moveRip(ripId, ripIndex + 1);
		}

		if (swapRow !== undefined) {
			row.parentNode.insertBefore(clone, row);
			swapRow.parentNode.insertBefore(row, swapRow);
			swapRow.parentNode.replaceChild(swapRow, clone);
		}
	}

	/**
	 * Saves the name of the playlist.
	 * @param {String} name The name of the playlist.
	 */
	updateName(name) {
		this.#name = name;
		this.saveToStorage();
	}

	/**
	 * Submits rip to the database asynchronously.
	 * Upon a successful submission, if the user is not logged in, it will give them a unique ID of the playlist for use in sharing.
	 * This ID can be used to claim the playlist and save it to an account too.
	 * @param {SubmitEvent} e The event of form submission.
	 */
	async submitPlaylist(e) {
		e.preventDefault();

		let data = new FormData();
		data.append('name', this.#name);
		for (let i = 0; i < this.#rips.length; i++) {
			data.append('rips[]', this.#rips[i]);
		}

		let submission = await fetch('/playlist/new', {
			method: 'POST',
			body: data,
			headers: {
				"Accept": "application/json"
			}
		});

		if (submission.ok) {
			let response = await submission.json();
			console.log(response);
		}
	}
}

/**
 * Modifies the search results table for use in adding rips to the playlist.
 * Creates a new column with a button to add the row's rip to the playlist.
 */
function prepareTable() {
	let table = document.getElementById('results');
	let rows = table.querySelectorAll('tbody>tr');

	// Create "Add" buttons to each row.
	for (let i = 0; i < rows.length; i++) {
		let addCell = document.createElement('td');
		let btnAdd = document.createElement('button');
		let ripId = rows[i].getAttribute('rip-id');
		let ripName = rows[i].children[0].innerText;
		btnAdd.innerText = '+';
		btnAdd.title = 'Add to playlist';
		btnAdd.onclick = e => toggleButton(btnAdd, ripId, ripName);

		// If the playlist has the current row's rip stored, toggle its button.
		if (playlist.hasRip(ripId)) {
			btnAdd.innerText = "-";
			btnAdd.classList.add('btn-bad');
		}

		addCell.append(btnAdd);
		rows[i].prepend(addCell);
	}
	playlist.updateForm();

	// Update ht header and footer to match the correct number of rows/colspan.
	let footer = table.querySelector('tfoot>tr>td');
	footer.setAttribute('colspan', footer.getAttribute('colspan') + 1);
	let head = table.querySelector('thead>tr');
	let addCell = document.createElement('th');
	head.prepend(addCell);

	/**
	 * Toggles the button by adding/removing its associated rip from the playlist.
	 * @param {HTMLButtonElement} btn The button being pressed.
	 * @param {Number} ripId The ID of the rip to add/remove.
	 */
	function toggleButton(btn, ripId, ripName) {
		// If the button is in its "add" state, add the rip and set it to its "remove" state.
		if (btn.innerText == "+") {
			btn.innerText = "-";
			btn.classList.add('btn-bad');
			btn.title = 'Remove from playlist';
			playlist.addRip(ripId, ripName);
		} else {
			btn.innerText = "+";
			btn.classList.remove('btn-bad');
			btn.title = 'Add to playlist';
			playlist.removeRip(ripId);
		}
	}
}

/**
 * Initialises the playlist editing session storage.
 * If no previous storage exists, it is created, else it is loaded.
 */
function initPlaylistCreator() {
	let existingRips = sessionStorage.getItem('playlist-rips');
	if (existingRips != null) {
		existingRips = existingRips.split(',');
	}
	let names = JSON.parse(sessionStorage.getItem('playlist-names'));
	playlist = new Playlist(existingRips, names, sessionStorage.getItem('playlist-name'));

	prepareTable();
}

/**
 * Checks if an existing playlist creator session is open. If it is, resumes it.
 */
function checkSession() {
	if (sessionStorage.getItem('playlist-rips') !== null) {
		initPlaylistCreator();
	}
}

window.onload = checkSession;