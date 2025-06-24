/**
 * Rip Guesser Game
 * 
 * Author: Mr Kaos
 * Created: 20/06/2025
 * Description: This file contains the main code for the Rip Guesser game.
 */

"use strict";

const settingsDiv = document.getElementById('settings');
const gamDiv = document.getElementById('game');

/**
 * Game object. Handles user interaction with the game.
 */
class Game {
	#gameID;
	#showAnserCount = true;
	#round = 0;
	#roundTimer;

	constructor() {
		this.#initGame();
	}

	async #initGame() {
		let activeGame = await this.#checkForActiveGame();
		if (activeGame !== false) {
			this.#gameID = activeGame;

			// Ask if a new game should be started
			let modalFunctions = {
				'Start New Game': {
					function: function () {
						this.#resetGame();
						this.toggleSettings();
					}.bind(this),
					colour: '#fff',
					background: '#ff0000'
				},
				'Resume Current Game': {
					function: this.nextRound.bind(this),
					background: '#00ff00'
				}
			}
			let modal = new Modal("game-reset", 'You already have a game running!', "Do you want to continue or start a new game?", null, null, false, false, modalFunctions);
			modal.open();
		} else {
			this.toggleSettings();
		}
	}

	/**
	 * Sends the game's settings to the server to start the game.
	 */
	async setSettings(e) {
		e.preventDefault();
		let data = new FormData(e.target);
		let ready = false;

		let request = await fetch('/ripguessr/game/start', {
			method: 'POST',
			body: data
		});

		// If the server responds with true, then the game can start
		if (request.ok) {
			try {
				ready = await request.json();
			} catch (e) {
				console.error(e);
				displayNotification("ERROR: The game was successfully initialised but no response was received from the server.", NotificationPriority.Error)
			}
			if (!ready) {
				displayNotification("Game failed to initialise!", NotificationPriority.Error);
			} else {
				this.toggleSettings();
				this.nextRound();
			}
		}
	}

	async joinGame(sessionID) {
		console.log("NOT AVAILABLE YET");
	}

	/**
	 * Moves the game to the next round.
	 */
	async nextRound() {
		let request = await fetch('/ripguessr/game/round-next', {
			method: 'GET'
		});

		if (request.ok) {
			this.#initRound(await request.json());
		} else {
			console.error('Could not start next round!')
			displayNotification("Failed to start next round!", NotificationPriority.Error);
		}
	}

	/**
	 * Displays the settings form when setting up a game.
	 */
	toggleSettings() {
		let settingsDiv = document.getElementById('settings');
		let display = settingsDiv.style.display;
		if (display == 'none') {
			settingsDiv.style.display = "unset";
		} else {
			settingsDiv.style.display = "none";
		}
	}

	/**
	 * Checks if there is already an active game.
	 * If there is, its session ID is returned.
	 * @return {Boolean|String} The games ID if a game is already active, false otherwise.
	 */
	async #checkForActiveGame() {
		let activeGame = false;
		let request = await fetch('/ripguessr/game/check', {
			method: 'GET'
		});

		if (request.ok) {
			try {
				let response = await request.json();
				if (response != false) {
					activeGame = response;
				}
			} catch (e) {
				console.error(e);
				displayNotification("ERROR: Could not check for active game session.\nSomething may be wrong on the server's end.", NotificationPriority.Error)
			}
		}
		return activeGame;
	}

	/**
	 * Resets the active game, if one is set in the client's session.
	 */
	async #resetGame() {
		console.log("HERE");
		let request = await fetch('/ripguessr/game/purge', {
			method: 'DELETE'
		});
	}

	/**
	 * Initialises a round by generating the 
	 * @param {Object} roundData A JSON object containing the fields that need to be answered.
	 */
	#initRound(roundData) {
		let gameContainer = document.getElementById('game');
		let audioPlayer = gameContainer.querySelector('#audio-player');
		let form = gameContainer.querySelector('#round-form');
		form.onsubmit = e => this.#submitRound(e, form);
		audioPlayer.innerHTML = `<iframe width="400" height="200" src="https://www.youtube-nocookie.com/embed/${roundData['_RipYouTubeID']}?autoplay=1&controls=0&showInfo=0&autohide=1" frameborder="0" allow="autoplay;"></iframe>`;

		let title = gameContainer.querySelector('#rip-name');

		if (roundData['_RipName'] != null) {
			title.innerText = "This round's rip is: " + roundData['_RipName'];
			if (roundData['_GameName'] != null) {
				title.innerText += ' - ' + roundData['_GameName'];
			}
		}

		console.log(roundData);
		for (let key in roundData) {
			let input = null;
			let label = null;
			switch (key) {
				// Easy difficulty
				case 'Jokes':
					input = new GuessInput('Jokes', 'jokes[]', roundData[key], '/search/jokes');
					break;
				// Standard Difficulty
				case 'GameName':
					input = new GuessInput('Game Name', 'game', false, '/search/games');
					break;
				// Hard difficulty
				case 'AlternateName':
					label = document.createElement('label');
					input = document.createElement('input');
					input.title = "The rip's name in its album release";
					label.innerText = 'Alternative Name';
					input.id = label.for = input.name = key;
					break;
				case 'Rippers':
					input = new GuessInput('Rippers', 'rippers[]', roundData[key], '/search/rippers');
					break;
				default:
					continue;
			}

			// Append the input and label to the game container.
			if (label != null) {
				form.appendChild(label);
				form.appendChild(input);
			} else {
				form.appendChild(input.getElement());
			}
		}

		// let test = setInterval(function() {
		// 	console.log(navigator.mediaSession);
		// }, 1000);

		gameContainer.style.display = 'unset';
	}

	#submitRound(event, form) {
		event.preventDefault();
		let data = new FormData(form);
		console.log(data);
	}
}

/**
 * Special version of the search element input with the main difference being that it has a answer count beside it for multi-value inputs
 * and does not have the search preview upon first interaction with the input.
 */
class GuessInput extends SearchElement {
	#selected = 0
	#max = 0;
	#countElement = null;

	/**
	 * Creates a new SearchElement object.
	 * @param {HTMLElement} appendToElement The element to append the search Element to.
	 * @param {String} label The label for the input
	 * @param {String} name The name attribute of the element
	 * @param {Number} multiAnswerCount If the input allows multiple answers (e.g. jokes), a value greater than 0 should be given. Else, the number of correct values for the input should be given.
	 * @param {String} url The url used to provide search live results
	 * @param {String} id the ID attribute of the element.
	 */
	constructor(label, name, multiAnswerCount = 0, url = null, id = null) {
		let element = document.createElement('span');
		element.id = id;
		element.className = 'search-element';
		element.setAttribute('type', ((multiAnswerCount > 0) ? 'multi' : 'search'));

		// Make sure the name is an array if a multi input is given.
		if (multiAnswerCount > 0) {
			if (!name.endsWith('[]')) {
				name += '[]';
			}
		}
		element.setAttribute('name', name);

		if (id == null) {
			id = name;
		}
		// let id = 
		element.innerHTML = `<label for="${id}" >${label}</label><input id="${id}" type="search" autocomplete="off" search-url="${url}">`;

		if (multiAnswerCount > 0) {
			element.innerHTML += `<span class="count">(0/${multiAnswerCount})</span><div class="selected"></div>`;
		}
		element.innerHTML += '<div class="options"></div>';

		super(element, false);
		this.#max = parseInt(multiAnswerCount);

		if (multiAnswerCount > 0) {
			// Create the counter element
			this.#countElement = element.querySelector(`span.count`);

			// Listen for when elements are added and removed.
			element.addEventListener('setOption', function () {
				this.#updateAnswerCount(1);
			}.bind(this));
			element.addEventListener('unsetOption', function () {
				this.#updateAnswerCount(-1);
			}.bind(this));
			this.#countElement.innerText = `(${this.#selected}/${this.#max})`;
		}
	}

	/**
	 * Updates the answer count in of the input.
	 * @param {Number} increment Either 1 or -1 to increment o decrement the selected options.
	 */
	#updateAnswerCount(increment) {
		this.#selected += increment;
		this.#countElement.innerText = `(${this.#selected}/${this.#max})`;

		// If there is max + 1 selected values, disable adding
		if (this.#selected > this.#max) {
			this.canAdd = false;
			this.#countElement.style.color = '#ff0000';
		} else {
			this.canAdd = true;
			this.#countElement.style.color = 'unset';
		}

		// reset the input
		this.getSearchElement().value = '';
		this.getOptionsDiv().innerHTML = '';
	}
}

let game = new Game();