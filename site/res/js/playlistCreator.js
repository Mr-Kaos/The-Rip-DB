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
	#desc;
	#form;
	#shareCode;
	#public = false;

	/**
	 * Creates a playlist object.
	 * @param {Number[]} rips IDs of rips in the playlist
	 * @param {String} name The name of the playlist.
	 * @param {String} shareCode The share code of the playlist. If given, the playlist will be in "edit" mode and will update this playlist.
	 */
	constructor(rips = [], names = [], name = 'My Playlist', shareCode = null, isPublic = false) {
		// Ensure all rips are numbers. If one is not, clear the array.
		if (Array.isArray(rips)) {
			for (let i = 0; i < rips.length; i++) {
				if (isNaN(rips[i])) {
					console.warn('An ID in the stored Rips was not numeric. The playlist has been reset!');
					rips = [];
					break;
				} else {
					rips[i] = parseInt(rips[i]);
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
		this.#shareCode = shareCode;
		this.#public = isPublic;
		this.#form = document.getElementById('playlist-creator');
	}

	/**
	 * Adds the specified Rip ID to the playlist. If the rip already exists, it is not added.
	 * @param {Number} id The ID of the rip to add
	 * @param {String} name The name of the rip. Only used to display to the user.
	 */
	addRip(id, name) {
		if (!isNaN(id)) {
			id = parseInt(id);
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
		id = parseInt(id);
		if (this.hasRip(id)) {
			let ripIndex = this.#rips.indexOf(id);
			this.#rips.splice(ripIndex, 1);
			this.#names.splice(ripIndex, 1);
			this.saveToStorage();

			// Remove the rip from the form too
			let list = this.#form.querySelector('.playlist-rips');
			if (list.childElementCount > 0) {
				list.children[ripIndex].remove();
			}

			// Check if the rip exists on the page's table, and if so, update the "add/remove" button to its "add" state.
			let rows = document.querySelectorAll('#results>tbody>tr');

			for (let i = 0; i < rows.length; i++) {
				if (rows[i].getAttribute('rip-id') == id) {
					if (rows[i] != null) {
						let btn = rows[i].firstElementChild.querySelector('button');
						if (btn != null) {
							btn.classList.remove('btn-bad');
							btn.innerText = "+";
							btn.title = 'Add to playlist';
						}
					}
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
		return this.#rips.includes(parseInt(id));
	}

	getRips() {
		return this.#rips;
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
		localStorage.setItem('playlist-rips', this.#rips);
		localStorage.setItem('playlist-names', JSON.stringify(this.#names));
		localStorage.setItem('playlist-name', this.#name ?? '');
		localStorage.setItem('playlist-desc', this.#desc ?? '');
		localStorage.setItem('playlist-code', this.#shareCode ?? '');
		localStorage.setItem('playlist-publicity', this.#public ?? false);
	}

	/**
	 * Deletes the playlist from the local storage.
	 */
	deleteFromStorage() {
		this.#clearList();
		this.#name = '';
		document.getElementById('playlist-name').value = '';
		localStorage.removeItem('playlist-rips');
		localStorage.removeItem('playlist-names');
		localStorage.removeItem('playlist-name');
		localStorage.removeItem('playlist-desc');
		localStorage.removeItem('playlist-code');
		localStorage.removeItem('playlist-publicity');
		// Hide the playlist creator.
		togglePlaylistCreator();
	}

	/**
	 * Updates the playlist creator form to display all rips and their order in the playlist.
	 */
	updateForm() {
		for (let i = 0; i < this.#rips.length; i++) {
			this.#appendToForm(this.#rips[i], this.#names[i]);
		}

		let inputName = this.#form.querySelector('#playlist-name');
		if (inputName != null) {
			inputName.value = this.#name;
		}
		let inputPublic = this.#form.querySelector('#playlist-public');
		if (inputPublic != null) {
			inputPublic.checked = this.#public;
		}
	}

	/**
	 * Adds a rip to the playlist form for reordering and display
	 * @param {Number} id The ID of the rip.
	 * @param {String} name The name of the rip
	 */
	#appendToForm(id, name) {
		let list = this.#form.querySelector('.playlist-rips');
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

		if (up && row.previousElementSibling.tagName != 'SUMMARY') {
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
	 * Saves the description of the playlist.
	 * @param {String} desc The description of the playlist.
	 */
	updateDesc(desc) {
		this.#desc = desc;
		this.saveToStorage();
	}

	/**
	 * Sets the publicity status of the playlist.
	 * @param {Boolean} publicState The publicity state of the playlist.
	 */
	updatePublicity(publicState) {
		this.#public = publicState;
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

		// Make sure there is at least one rip in the playlist
		if (this.#rips.length > 0) {
			let data = new FormData();
			data.append('name', this.#name);
			data.append('desc', this.#desc);
			data.append('public', this.#public);
			for (let i = 0; i < this.#rips.length; i++) {
				data.append('rips[]', this.#rips[i]);
			}

			// If a sharecode is set, update its playlist. (sharecode means it is in edit mode)
			if (this.#shareCode != null && this.#shareCode != '') {
				data.append('code', this.#shareCode);
				let submission = await fetch('/playlist/edit', {
					method: 'POST',
					body: data,
					headers: {
						"Accept": "application/json"
					}
				});

				if (submission.ok) {
					let response = await submission.json();
					if (response instanceof Object) {
						displayNotification(response['_Message'], NotificationPriority.Success);
					} else {
						displayNotification(response, NotificationPriority.Error);
					}
				}
			}
			// Else, create a new playlist.
			else {
				let submission = await fetch('/playlist/new', {
					method: 'POST',
					body: data,
					headers: {
						"Accept": "application/json"
					}
				});

				if (submission.ok) {
					let response = await submission.json();
					let codeRequest = await getCodes(response['_NewID'], response['InPlaylistName']);

					if (codeRequest != null) {
						// Get the template for the text box and set the codes.
						let template = document.getElementById('playlist-modal-msg').cloneNode(true);
						let claimSection = template.querySelector('div');
						if (codeRequest.ClaimCode == null) {
							claimSection.remove();
						} else {
							claimSection.querySelector('#claim-code').innerText = codeRequest.ClaimCode;

							// Store the claim cookie as a cookie for 30 days. If the user logs in, this cookie will be used to check if they have any unclaimed playlists.
							let claimCodes = getCookie('claimCodes');
							setCookie('claimCodes', (claimCodes == null ? '' : (claimCodes + ',')) + codeRequest.ClaimCode, 30);
						}

						template.querySelector('#share-code').innerText = codeRequest.ShareCode;
						template.style.display = null;

						let functions = {
							'Click Here to Close': {
								close: true,
								function: this.deleteFromStorage.bind(this)
							}
						}
						let modal = new Modal('codes', 'Playlist Codes', template, null, null, true, false, functions);
						modal.open();
					}
				}
			}

			/**
			 * Fetches the sharecode and the claim code (if the user is not logged in).
			 * @param {Number} id The ID of the new playlist
			 * @param {String} name The name of the new playlist
			 * @returns 
			 */
			async function getCodes(id, name) {
				let request = await fetch(`/playlist/getNewPlaylist?id=${id}&name=${name}`, {
					method: 'GET'
				});

				if (request.ok) {
					let codes = await request.json();

					return codes;
				} else {
					return null;
				}
			}
		} else {
			displayNotification('Please add at least one rip to the playlist!', NotificationPriority.Warning);
		}
	}

	/**
	 * Clears the playlist.
	 */
	#clearList() {
		let ripIds = new Array(...this.#rips);
		for (let i = 0; i < ripIds.length; i++) {
			this.removeRip(ripIds[i]);
		}
	}

	/**
	 * Prompts the user if they wish to clear the playlist through a modal.
	 * If they select yes, #clearList is called.
	 */
	promptClear() {
		let funcs = {
			'Yes': {
				className: 'btn-bad',
				function: this.#clearList.bind(this)
			},
			'No': {
				className: 'btn-good'
			}
		};
		let modal = new Modal('clear', "Confirm Playlist Clear", 'Are you sure you want to clear the playlist?', null, null, null, true, funcs);
		modal.open();
	}
}

/**
 * Deletes the playlist form local storage and hides the playlist editor.
 */
function cancelEdits() {
	let funcs = {
		'Cancel my changes!': {
			function: function () {
				playlist.deleteFromStorage();
				if (window.location.search.includes('playlist=')) {
					window.location = '/rips';
				}
			},
			className: 'btn-bad'
		},
		"No, Don't cancel!": {
			className: 'btn-good'
		}
	};
	let modal = new Modal('cancel-edit', "Are you sure you want to abort all changes?", "<p>Cancelling edits for this playlist will clear them from your session.</p><p>If you have previously saved the playlist, it's last saved state will remain.</p>", null, null, true, true, funcs);
	modal.open();
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

	// Update the header and footer to match the correct number of rows/colspan.
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
 * Toggles the display of the playlist buttons.
 */
function togglePlaylistCreator() {

	// If the playlist creator is not initialised, initialise it
	if (playlist == null) {
		initPlaylistCreator();
	} else {
		let playlistContainer = document.getElementById('playlist-creator');
		let rows = document.querySelectorAll('#results>tbody>tr,#results>thead>tr');
		let btnToggle = document.getElementById('playlist-toggle')

		// Set the display of the playlist container and update the text of the "Create Playlist" button 
		if (playlistContainer.style.display == 'none') {
			playlistContainer.style.display = null;
			btnToggle.innerText = 'Hide Playlist Creator';
		} else {
			playlistContainer.style.display = 'none';
			btnToggle.innerText = 'Show Playlist Creator';
		}

		// Update the visibility of each row.
		for (let i = 0; i < rows.length; i++) {
			rows[i].children[0].style.display = playlistContainer.style.display;
		}
	}
}

/**
 * Initialises the playlist editing session storage.
 * If no previous storage exists, it is created, else it is loaded.
 * @param {String} shareCode The share code to use when editing a playlist. If given, the playlist creator will be initialised for editing an existing playlist.
 */
async function initPlaylistCreator(shareCode = null) {
	let names;
	let name;
	let existingRips;
	let isPublic = false;
	let existingCode = localStorage.getItem('playlist-code');

	// Only attempt to get the playlist for editing if the code is different to what is stored and is not null.
	if (shareCode != null && shareCode != '' && shareCode != existingCode) {
		// check the user is authenticated first.
		let authenticated = await fetch('/check-auth', { method: 'GET' });

		if (authenticated.ok) {
			authenticated = await authenticated.json();

			if (authenticated == true) {
				let request = await fetch(`/playlist/getPlaylist?code=${shareCode}`, {
					method: 'get'
				});
				if (request.ok) {
					// If a string is returned, an error message should be displayed. If a JSON object is returned, parse it and build the playlist object.
					let response = await request.json();

					if (typeof (response) == 'string') {
						displayNotification(response, NotificationPriority.Error);
					} else if (response !== null) {
						name = response.PlaylistName;
						names = response.RipNames;
						existingRips = response.RipIDs;
						isPublic = response.IsPublic;

						playlist = new Playlist(existingRips, names, name, shareCode, isPublic);
						playlist.saveToStorage();
						// this.window.location = "/rips";
						prepareTable();
						togglePlaylistCreator();
					} else {
						displayNotification('Failed to obtain the specified playlist.', NotificationPriority.Error);
					}
				}
			}
		}
	} else {
		existingRips = localStorage.getItem('playlist-rips');
		if (existingRips != null) {
			existingRips = existingRips.split(',');
		}
		names = JSON.parse(localStorage.getItem('playlist-names'));
		name = localStorage.getItem('playlist-name');
		shareCode = localStorage.getItem('playlist-code');
		isPublic = localStorage.getItem('playlist-publicity') == 'true';

		playlist = new Playlist(existingRips, names, name, shareCode, isPublic);

		if (shareCode != null && shareCode != '') {
			let authenticated = await fetch('/check-auth', { method: 'GET' });

			if (authenticated.ok) {
				authenticated = await authenticated.json();

				if (authenticated == true) {
					playlist.saveToStorage();

					prepareTable();
					togglePlaylistCreator();
				} else {
					displayNotification('You must be logged in to edit a playlist.', NotificationPriority.Error);
					playlist.deleteFromStorage();
				}
			}
		} else {
			// playlist = new Playlist(existingRips, names, name, shareCode, isPublic);
			playlist.saveToStorage();

			prepareTable();
			togglePlaylistCreator();
		}
	}
}
/**
 * Checks if an existing playlist creator session is open. If it is, resumes it.
 */
window.addEventListener('load', function () {
	// Check if a playlist is being requested for editing
	if (window.location.search.includes('playlist=create')) {
		initPlaylistCreator();
	}
	else if (window.location.search.includes('playlist=')) {
		let code = window.location.search.split('=')[1];
		// If another GET variable somehow exists, remove it
		code = code.split('&')[0];
		initPlaylistCreator(code);
	}
	// Check if a playlist is currently being edited.
	else if (localStorage.getItem('playlist-rips') !== null) {
		initPlaylistCreator();
	}
});