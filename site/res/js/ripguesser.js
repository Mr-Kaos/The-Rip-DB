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
						this.openSettings();
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
			this.openSettings();
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
			ready = await request.json();
			if (!ready) {
				displayNotification("Game failed to initialise!", NotificationPriority.Error);
			} else {
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
	openSettings() {
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
			let response = await request.json();
			if (response != false) {
				activeGame = response;
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
		audioPlayer.innerHTML = `<iframe width="0" height="0" src="https://www.youtube-nocookie.com/embed/${roundData['RipYouTubeID']}?autoplay=1&controls=0&showInfo=0&autohide=1" frameborder="0" allow="autoplay;"></iframe>`;

		console.log(roundData);

		for (let key in roundData) {
			let input = document.createElement('input');
			let label = document.createElement('label');
			let count = document.createElement('span');
			switch (key) {
				// Easy difficulty
				case 'Jokes':
					label.innerText = 'Jokes';
					break;
				// Standard Difficulty
				case 'GameName':
					label.innerText = 'Game Name';
					count = null;
					break;
				// Hard difficulty
				case 'AlternateName':
					label.innerText = 'Alternative Name';
					input.title = "The rip's name in its album release"
					count = null;
					break;
				case 'Rippers':
					label.innerText = 'Rippers';
					break;
				default:
					continue;
			}
			input.id = label.for = input.name = key;

			// Append the input to the game container.
			form.appendChild(label);
			form.appendChild(input);
			if (count != null) {
				count.innerText = `(0/${roundData[key]})`
				form.appendChild(count);
			}
		}

		gameContainer.style.display = 'unset';
	}
}

/**
 * Handles asynchronous requests to the web app's API.
 */
class API_Request {
	API_ADDRESS = 'api';
	#method;
	#url;
	#urlEncoded;
	#urlParams;

	/**
	 * 
	 * @param {string} method The method of the API to call
	 * @param {Object} urlParams An associative array containing for get parameters in the URL.
	 */
	constructor(method, urlParams) {
		if (method.startsWith('/')) {
			method = method.substring(1);
		}
		this.#url = method;
		if (urlParams instanceof Object || urlParams == null) {
			this.#urlParams = urlParams;
		} else {
			throw TypeError('Parameter "urlParams" must be of type "Array".');
		}

		// Check that "api" exists in the url
		if (urlParams != null) {
			this.#url += '?' + API_Request.encodeObject(urlParams);
		}
		this.#urlEncoded = encodeURI(this.#url);

		this.#urlParams = urlParams;
		this.#method = method;
	}

	/**
	 * Make a GET request to the API 
	 * @return {Object} The parsed JSON from the API GET request
	 */
	async get() {
		let result = null;

		let errorMsg;
		let response = await fetch(this.#urlEncoded);

		if (response.ok) {
			result = await response.json().then(data => {
				switch (response.status) {
					case 200:
					case 201:
					case 202:
						return data;
					case 204:
						errorMsg = 'GET(' + this.#method + '): returned no content';
						break;
					case 205:
						errorMsg = 'GET(' + this.#method + '): requests page reset. Data on page may be out of date!';
						break;
					default:
						errorMsg = 'GET(' + this.#method + '): Request was successful however something unanticipated has occurred';
						break;
				}
			}).catch(err => {
				console.error(`GET(${this.#method}): returned malformed content. ${err}`);
				// addNotification('Unable to complete request properly', NotificationTypes.Error);
			});
		} else {
			let errorMsg = 'GET(' + this.#method + '): ';
			errorMsg += this.getErrorMessage(response.status, 'GET');
			// addNotification('Unable to complete request properly', NotificationTypes.Error);
		}

		if (errorMsg != undefined) {
			console.warn(errorMsg);
		}
		return result;
	}

	/**
	 * Sends a POST request to the API request method.
	 * @param {FormData} formData a FormData object to send in the POST request.
	 */
	async post(formData) {
		// Make a POST request to send data to the API
		let response = await fetch(this.#urlEncoded, {
			method: 'POST',
			body: formData
		});

		if (!response.ok) {
			const errorData = await response.json();
			console.warn(this.getErrorMessage(response.status, 'POST'));
			return errorData;
		}

		return response;
	}

	/**
	 * Performs a PUT request to using the constructor's given data and method.
	 * Returns a response indicating if the request was successful or not.
	 * @param {Object} data A JavaScript Object containing the data to be submitted to the server.
	 *       If a regular Object is sent, the content-type will be x-www-form-urlencoded. When FormData, the content-type will be form-data.
	 *        NOTE: DO NOT USE FormData as this does not work! It does no set the boundary in the Content-Type header, which prevents the data from being parsed on the server.
	 * @param {String} successMessage A message to display if the request was successful.
	 * @return {Boolean} True if the request was successfully sent to the server.
	 */
	async put(data, successMessage = "Successfully updated data") {
		let success = false;
		let contentType = 'application/x-www-form-urlencoded;charset=UTF-8';

		if (data instanceof FormData) {
			contentType = 'multipart/form-data';
		} else {
			data = API_Request.encodeObject(data);
		}
		const response = await fetch(this.#urlEncoded, {
			method: 'PUT',
			headers: {
				'Content-Type': contentType
			},
			body: data
		});

		if (response.ok) {
			let msg = await response.json();

			if (msg.FAILURE != null) {
				// addNotification(msg.FAILURE, NotificationTypes.Error);
			} else if (successMessage !== null) {
				// addNotification(successMessage);
				success = true;
			}
		} else {
			console.warn(this.getErrorMessage(response.status, 'PUT'));
			// addNotification("Could not update data.", NotificationTypes.Warning);
		}
		return success;
	}

	/**
	 * Encodes an object to be a valid for a URL.
	 * @param {Object} data 
	 */
	static encodeObject(data) {
		let body = [];
		let key;
		for (key in data) {
			let encodedKey = '';
			let encodedValue = encodeURIComponent(data[key]);

			if ((Array.isArray(data[key])) && !key.endsWith('[]')) {
				encodedKey = key + '[]';
				for (let i = 0; i < data[key].length; i++) {
					body.push(encodedKey + '=' + data[key][i]);
				}
			} else {
				if (key.endsWith('[]')) {
					encodedKey = key;
				} else {
					encodedKey = encodeURIComponent(key);
				}
				body.push(encodedKey + '=' + encodedValue);
			}
		}
		return body.join('&');
	}

	getErrorMessage(code, method) {
		let errorMsg;
		switch (code) {
			case 400:
				errorMsg = 'Malformed request.';
				break;
			case 403:
				errorMsg = 'User is unauthorised to perform this method. Maybe seek permission first before doing this.';
				break;
			case 404:
				errorMsg = 'The method called does not exist! ';
				break;
			case 405:
				errorMsg = 'The method used (' + method + ') is not allowed. Nuh uh uh!';
				break;
			case 408:
				errorMsg = 'The request timed out. You waited and waited, but didn\'t hear anything back.';
				break;
			case 418:
				errorMsg = 'The entity is in fact short and stout. Cannot brew coffee!';
				break;
			case 429:
				errorMsg = 'Too many requests sent, try again later. Hold your horses!';
				break;
			case 451:
				errorMsg = 'Content blocked. 1984?';
				break;
			case 500:
				errorMsg = `Internal Server Error. (It's me, hi I'm the problem, it's me).`;
				break;
			case 503:
				errorMsg = 'Service Unavailable!';
				break;
			default:
				errorMsg = 'An unanticipated error has occurred. This shouldn\'t ever happen. Code:', code;
				break;
		}
		return 'API request error: ' + errorMsg;
	}
}

let game = new Game();