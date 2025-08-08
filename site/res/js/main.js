/**
 * Main JS file
 */

// Apply dark theme if the user prefers dark-mode
if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
	if (getCookie('theme') == null) {
		document.cookie = "theme=dark;expires=Session;Path=/";
		window.location.reload();
	}
}

function getCookie(name) {
	let cookie = null;
	name = name + "=";
	let decodedCookie = decodeURIComponent(document.cookie);
	let cookies = decodedCookie.split(';');
	for (let i = 0; i < cookies.length; i++) {
		let c = cookies[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			cookie = c.substring(name.length, c.length);
			break;
		}
	}
	return cookie;
}

/**
 * Sets a cookie.
 * @param {String} name The name of the cookie to set
 * @param {String} value The value of the cookie
 * @param {Number} daysToExpiry The number of days from now until the cookie expires
 */
function setCookie(name, value, daysToExpiry) {
	let date = new Date();
	date.setTime(date.getTime() + (daysToExpiry * 24 * 60 * 60 * 1000));
	document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
}

/**
 * Deletes a cookie.
 * @param {String} name The name of the cookie to delete.
 */
function deleteCookie(name) {
	document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00;path=/`;
}

const NotificationPriority = {
	Default: "default", // neutral (theme) coloured
	Success: 'success', // green coloured
	Warning: "warning", // yellow coloured
	Alert: "alert", // dark orange coloured
	Error: "error", // red coloured

	getByString(val) {
		switch (val) {
			case 'Success':
				return this.Success;
			case 'Warning':
				return this.Warning;
			case 'Alert':
				return this.Alert;
			case 'Error':
				return this.Error;
			case 'Default':
			default:
				return this.Default;
		}
	},
	cases() {
		return [this.Success, this.Warning, this.Alert, this.Error, this.Default];
	}
}

/**
 * Returns the Notification container on the page. If it does not already exist, it is created.
 * @returns {HTMLDivElement}
 */
function getNotificationContainer() {
	let notifContainer = document.querySelector('.notification-container');

	if (notifContainer == undefined) {
		notifContainer = document.createElement('div');
		notifContainer.className = 'notification-container';

		document.body.append(notifContainer);
	}
	return notifContainer;
}

/**
 * Displays a basic pop-up notification message.
 * @param {String} message 
 * @param {NotificationPriority} priority 
 */
function displayNotification(message, priority) {
	const NOTIFICATION_FADE_TIMEOUT = 5000; // standard number of millis for notification to fade out.
	const MILLIS_PER_CHAR = 25; // If the message is longer than 30 chars, millis are added per character in the message.

	let notification = document.createElement('div');
	notification.innerText = message;
	notification.className = 'notif';
	notification.style.background = 'var(--bg-accent)'
	notification.style.borderColor = 'var(--accent-1)';

	let btnClose = document.createElement('span');
	btnClose.className = 'close';
	btnClose.innerHTML = '&times;';
	btnClose.onclick = e => notification.remove();
	notification.appendChild(btnClose);

	// Apply colour to the notification
	switch (priority) {
		case NotificationPriority.Success:
			notification.style.background = '#c5fd80'
			notification.style.borderColor = '#5add00'
			break;
		case NotificationPriority.Warning:
			notification.style.background = '#fffaaa'
			notification.style.borderColor = '#ffdd5a';
			break;
		case NotificationPriority.Alert:
			notification.style.background = '#ffa700'
			notification.style.borderColor = '#ee6666';
			break;
		case NotificationPriority.Error:
			notification.style.background = '#ee6666'
			notification.style.borderColor = '#dd0030';
			notification.style.color = '#fff';
			break;
	}

	getNotificationContainer().append(notification);

	// Depending on the length of the text, adjust how long it takes before the alert fades out.
	let delay = NOTIFICATION_FADE_TIMEOUT;
	if (notification.innerText.length > 30) {
		delay += notification.innerText.length * MILLIS_PER_CHAR;
	}
	setTimeout(fadeOut, delay, notification);

	/**
	 * 
	 * @param {Element} element The notification element to fade out.
	 */
	function fadeOut(element) {
		element.style.opacity = 1;
		let interval = setInterval(function () {
			element.style.opacity -= 0.01;

			if (element.style.opacity <= 0) {
				removeNotification(element);
				clearInterval(interval, NOTIFICATION_FADE_TIMEOUT);
			}
		}, 10, element);

	}

	/**
	 * Removes the given notification element from the DOM.
	 * @param {Element} element 
	 */
	function removeNotification(element) {
		element.remove();
	}
}

/**
 * Manages the displaying of messages beside a specified element, typically inputs.
 * If no message is given (i.e. message is null), it is assumed to remove any messages associated with the given element.
 * Else if a message is given, it is assumed that the specified message is to be displayed for the given element.
 * If a message already exists on the given element, the message is replaced with the next one given.
 * 
 * @param {Element} element The element to append or remove an error message from.
 * @param {String} message The message to append to the specified element.
 * @param {NotificationPriority} alertType The type of alert to display beside the error message.
 */
function displayErrorMessage(element, message = null, alertType = NotificationPriority.Error) {

	if (element !== null) {
		if (message == null) {
			removeErrorMessage(element);
		} else {
			appendErrorMessage(element, message, alertType);
		}
	} else {
		console.warn('Could not display or hide error message as the target element is null.');
	}

	/**
	 * Appends a span element after the specified element with the specified message.
	 * Used to alert the user of invalid inputs if one is made.
	 * Also adds a red outline to the associated fieldset where the error occurs.
	 * 
	 * @param {Element} element The element to append the message beside. Should be an input element.
	 * @param {String} message The message to be appended next to the element.
	 * @param {NotificationPriority} alertType Optional. The type of alert to present to the user.
	 */
	function appendErrorMessage(element, message, alertType = NotificationPriority.Error) {
		let associatedFieldset;
		let msgElement = document.getElementById(element.id + "_MSG");
		element.classList.add('highlight');
		clearHighlight(element);
		element.classList.add(alertType);

		// Make sure the message element being appended does not already exist
		if (msgElement === null) {
			msgElement = document.createElement("span");
			msgElement.id = element.id + "_MSG";
			msgElement.innerText = message;
			element.insertAdjacentElement('afterend', msgElement);
		} else {
			msgElement.innerText = message;
		}

		// Disabled as highlighting the whole fieldset felt too distracting. Might remove completely later.
		// // Find the associated fieldset of the input and style it accordingly.
		// if ((associatedFieldset = getInputFieldset(element)) !== undefined) {
		// 	if (alertType == NotificationPriority.Alert || alertType == NotificationPriority.Error || alertType == NotificationPriority.Warning) {
		// 		associatedFieldset.classList.add('highlight');
		// 		associatedFieldset.classList.add(alertType);
		// 		associatedFieldset.classList.remove('clear');
		// 	} else {
		// 		clearHighlight(associatedFieldset);
		// 	}
		// }
	}

	/**
	 * Removes an error message from an input element if one exists.
	 * @param {Element} element 
	 * @returns {Boolean} True if an error message exists and is removed. Else returns false.
	 */
	function removeErrorMessage(element) {
		let removed = false;
		let msgElement = document.getElementById(element.id + "_MSG");
		if (msgElement !== null) {
			msgElement.remove();
			removed = true;
		}
		clearHighlight(element);

		return removed;
	}

	/**
	 * Removes any highlight colours from the given element.
	 * @param {HTMLElement} element The element tor remove any highlights from
	 */
	function clearHighlight(element) {
		let notifTypes = NotificationPriority.cases();
		for (let i = 0; i < notifTypes.length; i++) {
			element.classList.remove(notifTypes[i]);
		}
	}

	/**
	 * Finds the fieldset that is the parent of the given input element and returns it.
	 * @param {Element} input The input element to find its fieldset for.
	 * @returns {Element|null} The fieldset if found. Else null.
	 */
	function getInputFieldset(input) {
		let fieldset = undefined;
		const MAX_ITERATIONS = 5;
		let i = 0;

		if (input !== null) {
			while (i < MAX_ITERATIONS && fieldset == null) {
				if (input.parentElement !== null) {
					if (input.parentElement.tagName == 'FIELDSET') {
						fieldset = input.parentElement;
					} else {
						input = input.parentElement;
					}
				}
				i++;
			}
		}
		return fieldset;
	}
}


class IModal {
	#bg;
	#id;
	#allowClose = true;
	#functions = {};
	#width;
	#height;
	allowResize;
	content = null;
	isOpen = false;
	#onClose = null
	#onOpen = null
	title;

	/**
	 * 
	 * @param {String} id The ID of the modal
	 * @param {String} title The title name of the modal
	 * @param {HTMLElement|String} text The content to be displayed in a modal.
	 * @param {Number} width The initial width of the modal
	 * @param {Number} height The initial height of the modal
	 * @param {Boolean} allowResize Determines if the modal is allowed to be resized.
	 * @param {Boolean} allowClose Determines if the modal is allowed to be closed. Useful if the user needs to confirm an action with the `functions` parameter.  
	 * If this is set to false and no functions are given, a warning will be displayed stating that the modal may be unclosable, i.e. permanently on-screen.
	 * @param {Object} functions A key-pair set of JavaScript functions that will be generated as buttons in the modal. Example:  
	 * ```js
	 * {
	 * 	Yes: {
	 * 		function: myFunction,
	 * 		colour: '#00ff00'
	 * 	},
	 * 	Dummy: {
	 * 		function: console.log('lmao'),
	 * 		close: false,
	 * 		className: 'my-css-class'
	 * 	}
	 * }
	 * ```  
	 * By default, all actions will close the modal when pressed. This can be overridden by setting the sub-key "close" to "false".
	 */
	constructor(id, title, text, width = null, height = null, allowResize = true, allowClose = true, functions = null) {
		this.#id = id;
		this.title = title;
		this.#allowClose = allowClose;
		this.setWidth(width);
		this.setHeight(height);
		this.#functions = functions;
		if (allowClose == false && functions == null) {
			console.warn(`The modal (ID: ${id}) has closing disabled and has no functions assigned! This modal may not be closable by the user once opened!`);
		}

		this.content = text;
		this.allowResize = allowResize;
		//build the grey background
		this.#bg = document.createElement("div");
		this.#bg.id = this.#id;
		this.#bg.classList.add("modal-bg");
		this.#bg.classList.add("hidden");
	}

	/**
	 * Add a function to run when the modal is closed
	 * @param {Function} func - the function to run
	 */
	setCloseListener(func) {
		this.#onClose = func;
	}

	/**
	 * Add a function to run when the modal is opened
	 * @param {Function} func - the function to run
	 */
	setOpenListener(func) {
		this.#onOpen = func;
	}

	/**
	 * Sets the width of the modal's content. If a percentage, it is converted into pixels.
	 * @param {String} width a valid value for the CSS width attribute.
	 */
	setWidth(width) {
		width = this.#setSize(width, 'width');
		if (width != null) {
			this.#width = width;
		}
	}

	/**
	 * @param {String} width A valid value for the CSS height attribute.
	 */
	setHeight(height) {
		height = this.#setSize(height, 'height');
		if (height != null) {
			this.#height = height;
		}
	}

	getWidth() {
		return this.#width;
	}

	getHeight() {
		return this.#height;
	}

	/**
	 * Validates a height value for the modals width/height. If a percentage, it is converted into pixels.
	 * @param {String} value The width.height value to validate.
	 * @param {String} type The text name of the attribute ("width" or "height"). Only used to log an error in case of an error.
	 * @return {null|String} The validated value or null if invalid.
	 */
	#setSize(value, type) {
		if (typeof(value) ==  'string') {
			if (value.endsWith('%')) {
				let windowHeight = window.innerHeight;
				try {
					let percent = "0." + value.split('%')[0];
					value = (windowHeight * (percent)) + 'px';
				}
				catch {
					console.error(`Invalid CSS ${type} given to modal. Will not set given value.`);
					value = null;
				}
			} else if (isNaN(parseInt(value)) && !value.endsWith('px')) {
				value = null;
				console.error(`Invalid CSS ${type} given to modal. Will not set given value.`);
			} else if (!value.endsWith('px')) {
				value = value + 'px';
			}
		} else if (!isNaN(value)) {
			value = value + 'px';
		}
		return value;
	}

	/**
	 * This allows the user to click on the grey background to close the modal. Currently adds hidden class to anything inside the modal that is clicked. 
	 */
	handleClick(e) {
		if (this.#allowClose) {
			if (e.target.className === 'modal-bg') {
				this.close();
			} else if (e.target.className === 'modal-close') {
				this.close();
			}
		}
	}

	/**
	 * @returns {HTMLElement} The modal's background element (the eldest parent).
	 */
	getBG() {
		return this.#bg;
	}

	getFunctions() {
		return this.#functions;
	}

	/**
	 * Builds HTML for title bar of modal
	 * @param {string} title 
	 * @returns {bg} modalBar
	 */
	buildModalTitleBar(title) {
		let modalTitle = document.createElement("h3");
		modalTitle.innerHTML = title;
		this.modalTitleHeading = modalTitle;
		let modalTitleBar = document.createElement("div");
		modalTitleBar.className = "modal-title-bar";
		modalTitleBar.appendChild(modalTitle);

		if (this.#allowClose) {
			let modalClose = document.createElement("span");
			modalClose.className = "modal-close";
			modalClose.innerHTML = "&times;";
			modalTitleBar.appendChild(modalClose);
		}

		return modalTitleBar;
	}

	/**
	 * Replaces the existing content with new content.
	 * @param {String} content The content to replace the existing content with.
	 */
	setContent(content) {
		this.content = content;
	}

	/**
	 * Builds and opens the modal.
	 */
	async open() {
		this.getBG().innerHTML = '';
		let modalWindow = document.createElement("div");
		modalWindow.classList.add("modal-window");

		modalWindow.appendChild(this.buildModalTitleBar(this.title));
		let contentDiv = await this.constructContainer();

		if (contentDiv != null) {
			// If functions are given, build them
			let funcs = this.getFunctions();
			if (funcs != null) {
				let btnDiv = document.createElement('div');
				btnDiv.className = 'modal-buttons';

				for (let btnName in funcs) {
					let data = funcs[btnName];
					let btn = document.createElement('button');
					let canClose = true;
					btn.type = "button";
					btn.innerText = btnName;

					for (let attr in data) {
						switch (attr) {
							case 'function':
								btn.onclick = data[attr];
								break;
							case 'colour':
								btn.style.color = data[attr];
								break;
							case 'className':
								btn.className = data[attr];
								break;
							case 'background':
								btn.style.background = data[attr];
								break;
							case 'close':
								canClose = data[attr];
								break;
						}
					}

					if (canClose) {
						btn.addEventListener('click', this.close.bind(this));
					}

					btnDiv.append(btn);
				}
				contentDiv.append(btnDiv);
			}

			if (this.allowResize) {
				contentDiv.style.resize = "both";
				contentDiv.style.overflow = "scroll";
				contentDiv.style.minWidth = '100%';
				contentDiv.style.minHeight = '100%';
				contentDiv.style.maxWidth = '100%';
				contentDiv.style.maxHeight = '87vh';
			}

			if (this.#width != null) {
				contentDiv.style.width = this.#width;
			}
			if (this.#height != null) {
				contentDiv.style.height = this.#height;
			}

			modalWindow.appendChild(contentDiv);
		}
		this.getBG().appendChild(modalWindow);
		this.boundHandleClick = this.handleClick.bind(this);
		this.boundOpenModal = this.open.bind(this);

		this.getBG().addEventListener("mousedown", this.boundHandleClick);
		document.body.appendChild(this.getBG());

		this.getBG().classList.remove("hidden");

		if (this.#onOpen != null) {
			this.#onOpen();
		}
		this.isOpen = true;
	}

	/**
	 * Destroys the modal's content and hides it.
	 */
	async close() {
		let closeValue = null;
		this.getBG().remove();
		this.isOpen = false;
		if (this.#onClose != null) {
			if (this.#onClose.length == 0) {
				closeValue = await this.#onClose(this);
			} else {
				closeValue = await this.#onClose(this);
			}
		}
		return closeValue
	}

	/**
	 * Abstract method to construct the modal's contents.
	 */
	async constructContainer() {
		console.error('The construct method has not been implemented!');
	}
}
/**
 * The base modal class
 */
class Modal extends IModal {

	constructor(id, title, text, width = null, height = null, allowResize = true, allowClose = true, functions = null) {
		super(id, title, text, width, height, allowResize, allowClose, functions)
	}

	/**
	 * Builds the content to be displayed in the modal's content container.
	 */
	async constructContainer() {
		let contentDiv = document.createElement('div');
		contentDiv.className = 'content';
		if (typeof (this.content) == 'object') {
			contentDiv.innerHTML = '';
			contentDiv.append(this.content);
		} else {
			contentDiv.innerHTML = this.content;
		}
		contentDiv.style.width = this.getWidth;
		contentDiv.style.height = this.getHeight;

		return contentDiv;
	}
}

/**
 * This type of modal embeds a form from another page in the site into a modal, which when submitted returns the submitted value.
 */
class FormModal extends IModal {
	#srcPage;
	#formId;
	#form;
	#submissionResponse; // The response to the submission.

	/**
	 * Builds the page modal.
	 * @param {String} id The ID of the modal
	 * @param {String} title The title name of the modal
	 * @param {String} text The content to be displayed in a modal.
	 * @param {String} formSrc The page to retrieve the form from.
	 * @param {String} formId The ID of the form in the formSrc page to retrieve.
	 * @param {Number} width The initial width of the modal
	 * @param {Number} height The initial height of the modal
	 * @param {Boolean} allowResize Determines if the modal is allowed to be resized.
	 * @param {Boolean} allowClose Determines if the modal is allowed to be closed. Useful if the user needs to confirm an action with the `functions` parameter.  
	 */
	constructor(id, title, formSrc, formId, width = null, height = null, allowResize = true, allowClose = true) {
		super(id, title, '', width, height, allowResize, allowClose);
		this.#srcPage = formSrc;
		this.#formId = formId;
		this.#submissionResponse = null;
		this.setOpenListener(this.#setFocus);
	}

	/**
	 * Builds the content to be displayed in the modal's content container.
	 * Performs a fetch request to retrieve the form from the modal's srcPage attribute
	 */
	async constructContainer() {
		let request = await fetch(this.#srcPage, {
			method: 'GET'
		});

		return new Promise(async (resolve, reject) => {
			if (request.ok) {
				// Get the form from the page
				let page = await request.text();
				let parser = new DOMParser();
				let doc = parser.parseFromString(page, 'text/html');
				let form = doc.getElementById(this.#formId);

				if (form != null) {
					this.#form = form;
					setupCustomInputs(form);
					// Setup submission listener.
					form.onsubmit = async function (e) {
						e.preventDefault();

						let data = new FormData(form);

						let submission = await fetch(this.#srcPage, {
							method: 'POST',
							body: data,
							headers: {
								"Accept": "application/json"
							}
						});

						if (submission.ok) {
							this.#submissionResponse = await submission.json();
						}
					}.bind(this);
					resolve(form);
				} else {
					reject(`No form with ID "${this.#formId}" exists on the specified page.`);
				}
			} else reject('Failed to get form');
		});
	}

	/**
	 * Asynchronous function that retrieves the submitted response from the form.
	 * @returns Promise The ID of the inserted record and an alias name for it.
	 */
	async onSubmit() {
		return new Promise((resolve, reject) => {
			let interval = setInterval(function () {
				if (this.#submissionResponse !== null) {
					clearInterval(interval);

					// if an error message is in the response, display it
					if (this.#submissionResponse['_Error'] != undefined) {
						displayNotification(this.#submissionResponse['_Error'], NotificationPriority.Error);
						this.close();
						return reject(this.#submissionResponse['_Error']);
					}
					else if (this.#submissionResponse['_Message'] != undefined) {
						displayNotification(this.#submissionResponse['_Message'], NotificationPriority.Success);
						this.close();
						return resolve(this.#submissionResponse);
					}
				}
			}.bind(this), 100);
		});
	}

	/**
	 * Focuses onto the first input element.
	 */
	#setFocus() {
		let firstInput = this.#form.querySelector('input,select,textarea');
		if (firstInput != null) {
			firstInput.focus();
		}
	}
}

/**
 * Checks to see if any notifications were created by the server and displays them
 */
function checkForNotifications() {
	let serverNotifDiv = document.getElementById('server-notifs');

	if (serverNotifDiv != undefined) {
		let notifs = serverNotifDiv.querySelectorAll('p');

		for (let i = 0; i < notifs.length; i++) {
			let priority = NotificationPriority.getByString(notifs[i].getAttribute('priority'));

			displayNotification(notifs[i].innerText, priority);
		}
	}
}
window.addEventListener('load', checkForNotifications);
