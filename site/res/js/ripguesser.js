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
const EMBED_WIDTH = 640;
const EMBED_HEIGHT = 360;

/**
 * Game object. Handles user interaction with the game.
 */
class Game {
	#gameID;
	#showAnserCount = true;
	#round = 0;
	#roundTimer;
	#gameContainer = null;
	#roundData = null;
	#player;

	constructor() {
		this.#initGame();
	}

	async #initGame() {
		this.#gameContainer = document.getElementById('game');
		let activeGame = await this.#checkForActiveGame();
		if (activeGame !== false) {
			this.#gameID = activeGame;

			// Ask if a new game should be started
			let modalFunctions = {
				'Start New Game': {
					function: function () {
						this.#resetGame();
						this.#toggleGameDisplay('settings');
					}.bind(this),
					colour: '#fff',
					background: '#ff0000'
				},
				'Resume Current Game': {
					function: this.#startGame.bind(this),
					background: '#00ff00'
				}
			}
			let modal = new Modal("game-reset", 'You already have a game running!', "Do you want to continue or start a new game?", null, null, false, false, modalFunctions);
			modal.open();
		} else {
			this.#toggleGameDisplay('settings');
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
				this.#toggleGameDisplay('settings');
				this.#startGame();
			}
		}
	}

	/**
	 * Starts/resumes the game.
	 */
	#startGame() {
		this.nextRound();
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
			// Check if the data received is for a new round, or is the results of the game
			let data = await request.json();
			if (data.GameEnd != undefined) {
				if (data.Message != undefined) {
					displayNotification(data.Message, NotificationPriority.Warning);
				}
				this.#showFinalResults(data.Summary);
			} else {
				this.#initRound(data);
			}
		} else {
			console.error('Could not start next round!')
			displayNotification("Failed to start next round!", NotificationPriority.Error);
		}
	}

	/**
	 * Toggles the visibility of the game's active page.
	 * @param {String} mode Must be either "settings", "round" or "results".
	 */
	#toggleGameDisplay(mode) {
		let settingsDiv = document.getElementById('settings');
		let roundContainer = this.#gameContainer.querySelector('#round');
		let resultsContainer = this.#gameContainer.querySelector('#results');
		switch (mode) {
			case 'settings':
				this.#gameContainer.style.display = 'none';
				settingsDiv.style.display = "unset";
				roundContainer.style.display = "none";
				resultsContainer.style.display = "none";
				break;
			case 'round':
				this.#gameContainer.style.display = 'unset';
				settingsDiv.style.display = "none";
				roundContainer.style.display = "unset";
				resultsContainer.style.display = "none";
				break;
			case 'results':
				this.#gameContainer.style.display = 'unset';
				settingsDiv.style.display = "none";
				roundContainer.style.display = "none";
				resultsContainer.style.display = "unset";
				break;
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
		let request = await fetch('/ripguessr/game/purge', {
			method: 'DELETE'
		});

		window.location.reload();
	}

	/**
	 * Initialises a round by generating the form elements based on the given data.
	 * Stores the round data in the object too for 
	 * @param {Object} roundData A JSON object containing the fields that need to be answered.
	 */
	#initRound(roundData) {
		let roundContainer = this.#gameContainer.querySelector('#round');
		let form = roundContainer.querySelector('#round-form');
		let previewRip = true;
		form.innerHTML = '';
		form.onsubmit = e => this.#submitRound(e, form);
		this.#round = roundData.RoundNumber;
		roundData = roundData.RoundData;

		if (roundData != undefined) {
			this.#gameContainer.querySelector('#title').innerText = 'Round ' + this.#round;
			let title = this.#gameContainer.querySelector('#rip-name');

			if (roundData['_RipName'] != null) {
				title.innerText = "This round's rip is: " + roundData['_RipName'];
				if (roundData['_GameName'] != null) {
					title.innerText += ' - ' + roundData['_GameName'];
				}
			}

			for (let key in roundData) {
				let input = null;
				let label = null;
				switch (key) {
					// Easy difficulty
					case 'Jokes':
						input = new GuessInput('Jokes', 'jokes[]', roundData[key], '/ripguessr/search/jokes');
						break;
					// Standard Difficulty
					case 'GameName':
						input = new GuessInput('Game Name', 'game', false, '/search/games');
						previewRip = false;
						break;
					case 'RipName':
						input = new GuessInput('Rip Name', 'rip', false, '/search/rip-names');
						previewRip = false;
						break;
					// Hard difficulty
					case 'AlternateName':
						input = new GuessInput('Alternate Name', 'altName', false, '/search/rip-alt-names');
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

			// Set up the embedded player and the controls.
			let volumeSlider = roundContainer.querySelector('#volume');
			if (this.#player == null) {
				this.#prepareYTEmbed(roundData['_RipYouTubeID'], volumeSlider.value, previewRip);
			} else {
				this.#player.setSize(previewRip ? EMBED_WIDTH : 0, previewRip ? EMBED_HEIGHT : 0);
				this.#player.loadVideoById(roundData['_RipYouTubeID'], 0);
				this.#player.setVolume(volumeSlider.value);
			}
			volumeSlider.oninput = function (e) {
				this.#player.setVolume(e.target.value);
			}.bind(this);

			let btnPause = roundContainer.querySelector('#play-pause');
			btnPause.onclick = function (e) {
				// If paused, play
				if (this.#player.getPlayerState() == 1) {
					this.#player.pauseVideo();
					e.target.innerText = "Resume Playback"
					// If playing, pause
				} else if (this.#player.getPlayerState() == 2) {
					this.#player.playVideo();
					e.target.innerText = "Pause Playback"
				}
			}.bind(this);

			this.#roundData = roundData;
			this.#toggleGameDisplay('round');
		} else {
			this.#raiseCriticalError('Could not retrieve round data!');
		}
	}

	/**
	 * 
	 * @param {Event} event 
	 * @param {HTMLFormElement} form 
	 */
	async #submitRound(event, form) {
		event.preventDefault();
		let data = new FormData(form);

		let request = await fetch('/ripguessr/game/submit', {
			method: 'POST',
			body: data
		});

		if (request.ok) {
			let results = await request.json();
			this.#showResults(form.elements, results);
		}
	}

	/**
	 * Initialises the embedded video stream
	 * @param {String} ytID The ID of the video stream to embed
	 */
	#prepareYTEmbed(ytID, initVolume, display = false) {
		this.#player = new YT.Player('stream', {
			height: display ? EMBED_HEIGHT : '0',
			width: display ? EMBED_WIDTH : '0',
			videoId: ytID,
			host: 'https://www.youtube-nocookie.com',
			playerVars: {
				'playsinline': 1,
				'controls': 0,
				'disablePictureInPicture': 1
			},
			events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange
			}
		});

		function onPlayerReady(event) {
			event.target.playVideo();
			event.target.setVolume(initVolume);
		}

		function onPlayerStateChange(event) {
		}
	}

	/**
	 * Displays the results for the round.
	 * @param {HTMLFormControlsCollection} form The form controls of the round. These may be empty if the user did not submit them.
	 * @param {Object} results The results for the round.
	 */
	#showResults(form, results) {
		let resultsContainer = this.#gameContainer.querySelector('#results');

		// Ensure the results container exists, if for whatever reason it gets removed.
		if (resultsContainer != undefined) {
			let score = resultsContainer.querySelector('#score');
			let btnNextRound = resultsContainer.querySelector('#advance-round');
			btnNextRound.onclick = e => this.nextRound();

			score.innerText = results.Score;
			let answersContainer = resultsContainer.querySelector('#answers>ul');
			answersContainer.innerHTML = '';
			let answers = results.Results.Answers;
			let correct = results.Results.Correct;

			for (let key in form) {
				// Make sure only the form elements are checked.
				if (form[key] instanceof HTMLElement && form[key].id != '') {
					let answerResult = document.createElement('li');
					// If there are any corrections, <li> elements will be added containing the corrections.
					let answerKey = null;

					switch (form[key].id) {
						case 'jokes[]':
							answerResult.innerHTML = `<b>Jokes: </b>${correct['Jokes']}/${this.#roundData.Jokes}`;
							answerKey = 'Jokes';
							break;
						// Standard Difficulty
						case 'game':
							answerResult.innerHTML = `<b>Game Name: </b>${correct['GameName']}/${this.#roundData.GameName}`;
							answerKey = 'GameName';
							break;
						case 'rip':
							answerResult.innerHTML = `<b>Rip Name: </b>${correct['RipName']}/${this.#roundData.RipName}`;
							answerKey = 'RipName';
							break;
						// Hard difficulty
						case 'altName':
							answerResult.innerHTML = `<b>Alternate Name: </b>`;
							answerKey = 'AlternateName';
							break;
						case 'rippers[]':
							answerResult.innerHTML = `<b>Rippers: </b>${correct['Rippers']}/${this.#roundData.Rippers}`;
							answerKey = 'Rippers';
							break;
					}

					// Display the answers
					if (answerKey != null) {
						let correctValues = document.createElement('ul');
						for (let jokeId in answers[answerKey]) {
							let answer = document.createElement('li');
							answer.innerText = answers[answerKey][jokeId];
							correctValues.append(answer);
						}

						answerResult.appendChild(correctValues)
					}

					answersContainer.appendChild(answerResult);
				}
			}

			// Set the embedded player size
			this.#player.getIframe
			this.#player.setSize(EMBED_WIDTH, EMBED_HEIGHT);
		} else {
			this.#raiseCriticalError('Cannot find results container!');
		}

		this.#toggleGameDisplay('results');
	}

	/**
	 * Displays the final results of the game.
	 * @param {Object} resultsData The data object summarising the game
	 */
	#showFinalResults(resultsData) {
		let title = this.#gameContainer.querySelector('#title');
		title.innerText = 'Final Results';
		let resultsContainer = this.#gameContainer.querySelector('#results');
		let answersContainer = resultsContainer.querySelector('#answers');
		let scoreElement = resultsContainer.querySelector('#score');
		let btnNextRound = resultsContainer.querySelector('#advance-round');
		btnNextRound.innerText = 'New Game';
		btnNextRound.onclick = e => this.#resetGame();
		let totalScore = 0;
		let maxScore = 0;
		answersContainer.innerHTML = '';

		for (let roundNum in resultsData.Rounds) {
			let round = resultsData.Rounds[roundNum];
			let roundDiv = document.createElement('div');
			let roundHeader = document.createElement('h3');
			let roundList = document.createElement('ul');
			roundHeader.innerText = `Round ${parseInt(roundNum) + 1} - ${round.RipName} - ${round.GameName}`;

			// Add items for each rip
			let item = document.createElement('li');

			// Score
			item.innerHTML = `<b>Score:</b> ${round.Score}/${round.MaxScore}`;
			roundList.appendChild(item);

			// Page Link
			item = document.createElement('li');
			item.innerHTML = `<a href="/rips/${round.RipID}">View Rip details</a>`;
			roundList.appendChild(item);

			item = document.createElement('li');
			item.innerHTML = `<a href="https://youtube.com/watch?v=${round.YTID}" target="_blank">View on YouTube</a>`;
			roundList.appendChild(item);

			roundDiv.appendChild(roundHeader);
			roundDiv.appendChild(roundList);
			answersContainer.appendChild(roundDiv);

			totalScore += round.Score;
			maxScore += round.MaxScore;
		}

		scoreElement.innerHTML = `${totalScore}/${maxScore}`;

		// Stop the playback
		this.#player.pauseVideo();
		this.#player.setSize(0, 0);
		this.#gameContainer.querySelector('#rip-name').remove();

		this.#toggleGameDisplay('results');
	}

	/**
	 * Displays a modal stating that a critical error occurred.
	 * It prompts the user if they wish to reload the round to try again, or restart the game.
	 * @param {String} msg An error message to log in the console.
	 */
	#raiseCriticalError(msg) {
		// Ask if a new game should be started
		let modalFunctions = {
			'Reload and restart round': {
				function: this.#startGame.bind(this),
				background: '#00ff00'
			},
			'Reset Game': {
				function: function () {
					this.#resetGame();
					this.#toggleGameDisplay('settings');
				}.bind(this),
				colour: '#fff',
				background: '#ff0000'
			}
		}
		console.error(msg);
		let modal = new Modal("game-reload", "Uh-oh, this isn't supposed to happen...", "A critical error was encountered.\nYou can attempt to restart the last round played, or reset the game from scratch again.", null, null, false, false, modalFunctions);
		modal.open();
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

		element.innerHTML += `<label for="${id}" >${label}</label>`;
		if (multiAnswerCount > 0) {
			element.innerHTML += `<span class="count">(0/${multiAnswerCount})</span>`;
		}
		element.innerHTML += `<input id="${id}" type="search" autocomplete="off" search-url="${url}">`;
		if (multiAnswerCount > 0) {
			element.innerHTML += `<div class="selected"></div>`;
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

// The main game object. This will only initialise if in the "Play" page for the game.
let game = null;

function onYouTubeIframeAPIReady() {
	game = new Game();
}