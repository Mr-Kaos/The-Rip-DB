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
 * @property {String} name The name of the playlist.
 */
class Playlist {
	#rips = [];
	#name;

	/**
	 * Creates a playlist object.
	 * @param {Number[]} rips IDs of rips in the playlist
	 * @param {String} name The name of the playlist.
	 */
	constructor(rips = [], name = 'My Playlist') {
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

		this.#rips = rips;
		this.#name = name;
	}

	/**
	 * Adds the specified Rip ID to the playlist. If the rip already exists, it is not added.
	 * @param {Number} id The ID of the rip to add
	 */
	addRip(id) {
		if (!isNaN(id)) {
			if (this.hasRip(id)) {
				displayNotification("This rip already exists in the playlist!", NotificationPriority.Warning);
			} else {
				this.#rips.push(id);
			}
		}

		console.log(this.#rips);

		this.saveToStorage();
	}

	/**
	 * Removes the specified Rip ID from the playlist.
	 * @param {Number} id The ID of the rip to remove
	 */
	removeRip(id) {
		if (this.hasRip(id)) {
			this.#rips.splice(this.#rips.indexOf(id), 1);
			this.saveToStorage();
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
			this.saveToStorage();
		}
	}

	/**
	 * Saves the playlist's data to the session storage.
	 */
	saveToStorage() {
		sessionStorage.setItem('playlist-rips', this.#rips);
		sessionStorage.setItem('playlist-name', this.#name);
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
		btnAdd.innerText = '+';
		btnAdd.onclick = e => toggleButton(btnAdd, ripId);

		// If the playlist has the current row's rip stored, toggle its button.
		if (playlist.hasRip(ripId)) {
			btnAdd.innerText = "-";
			btnAdd.classList.add('btn-bad');
		}

		addCell.append(btnAdd);
		rows[i].prepend(addCell);
	}

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
	function toggleButton(btn, ripId) {
		// If the button is in its "add" state, add the rip and set it to its "remove" state.
		if (btn.innerText == "+") {
			btn.innerText = "-";
			btn.classList.add('btn-bad');
			playlist.addRip(ripId);
		} else {
			btn.innerText = "+";
			btn.classList.remove('btn-bad');
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
	playlist = new Playlist(existingRips, sessionStorage.getItem('playlist-name'));

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