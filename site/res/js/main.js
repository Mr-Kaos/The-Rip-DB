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

const NotificationPriority = {
	Default: "default", // neutral (theme) coloured
	Success: 'success', // green coloured
	Warning: "warning", // yellow coloured
	Alert: "alert", // dark orange coloured
	Error: "error" // red coloured
}

/**
 * Returns the Notification container on the page. If it does not already exist, it is created.
 * @returns {HTMLDivElement}
 */
function getNotificationContainer() {
	let notifContainer = document.getElementById('Container_Notification');

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
	let notification = document.createElement("div");
	notification.innerText = message;
	notification.className = 'notif';

	// Apply colour to the notification
	switch (priority) {
		default:
			notification.style.background = 'var(--bg-accent)'
			notification.style.borderColor = 'var(--accent-1)';
			break;
		case NotificationPriority.Success:

			break;
		case NotificationPriority.Warning:

			break;
		case NotificationPriority.Alert:

			break;
		case NotificationPriority.Error:
			notification.style.background = '#ee6666'
			notification.style.borderColor = '#dd0030';
			notification.style.color = '#fff';
			break;
	}

	getNotificationContainer().append(notification);
}

/**
 * The base modal class
 */
class Modal {
	#bg;
	#id;
	#title;
	#width;
	#height;
	#openCheck = null
	#onClose = null
	#content = null;
	#allowClose = true;
	#allowResize;
	#open = false;
	#functions = {};


	/**
	 * 
	 * @param {String} id The ID of the modal
	 * @param {String} title The title name of the modal
	 * @param {String} text The content to be displayed in a modal.
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
	 * 		close: false
	 * 	}
	 * }
	 * ```  
	 * By default, all actions will close the modal when pressed. This can be overridden by setting the sub-key "close" to "false".
	 */
	constructor(id, title, text, width = null, height = null, allowResize = true, allowClose = true, functions = null) {
		this.#id = id;
		this.#title = title;
		this.#allowClose = allowClose;
		this.setWidth(width);
		this.setHeight(height);
		this.#functions = functions;
		if (allowClose == false && functions == null) {
			console.warn(`The modal (ID: ${id}) has closing disabled and has no functions assigned! This modal may not be closable by the user once opened!`);
		}

		this.#content = text;
		this.#allowResize = allowResize;
		//build the grey background
		this.#bg = document.createElement("div");
		this.#bg.id = this.#id;
		this.#bg.classList.add("modal-bg");
		this.#bg.classList.add("hidden");
	}

	/**
	 * Add a function to run once the modal is closed
	 * @param {Function} fun - the function to run when the modal is closed
	 */
	setCloseListener(fun) {
		this.#onClose = fun;
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
	 * @param {String} width a valid value for the CSS height attribute.
	 */
	setHeight(height) {
		height = this.#setSize(height, 'height');
		if (height != null) {
			this.#height = height;
		}
	}

	/**
	 * Validates a height value for the modals width/height. If a percentage, it is converted into pixels.
	 * @param {String} value The width.height value to validate.
	 * @param {String} type The text name of the attribute ("width" or "height"). Only used to log an error in case of an error.
	 * @return {null|String} The validated value or null if invalid.
	 */
	#setSize(value, type) {
		if (value instanceof String) {
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
			}
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
	 * Destroys the modal's content and hides it.
	 */
	close() {
		this.#bg.remove();
		this.#open = false;
		if (this.#onClose != null) {
			if (this.#onClose.length == 0) {
				this.#onClose(this);
			} else {
				this.#onClose(this);
			}
		}
	}

	/**
	 * Builds and opens the modal.
	 */
	open() {
		let modalWindow = document.createElement("div");
		modalWindow.classList.add("modal-window");

		modalWindow.appendChild(this.#buildModalTitleBar(this.#title));
		modalWindow.appendChild(this.#buildContent());
		this.#bg.appendChild(modalWindow);
		this.boundHandleClick = this.handleClick.bind(this);
		this.boundOpenModal = this.open.bind(this);

		this.#bg.addEventListener("mousedown", this.boundHandleClick);
		document.body.appendChild(this.#bg);

		this.#bg.classList.remove("hidden");
		this.#open = true;
		if (this.#openCheck != null) {
			if (this.#openCheck.length == 0) {
				this.#openCheck();
			} else {
				this.#openCheck(this);
			}
		}
	}

	/**
	 * Builds the content to be displayed in the modal's content container.
	 */
	#buildContent() {
		let contentDiv = document.createElement('div');
		contentDiv.className = 'content';
		contentDiv.innerHTML = this.#content;
		contentDiv.style.width = this.#width;
		contentDiv.style.height = this.#height;

		if (this.#allowResize) {
			contentDiv.style.resize = "both";
			contentDiv.style.overflow = "scroll";
			contentDiv.style.minWidth = '100%';
			contentDiv.style.minHeight = '100%';
			contentDiv.style.maxWidth = '100%';
			contentDiv.style.maxHeight = '87vh';
		}

		// If functions are given, build them
		if (this.#functions != null) {
			let btnDiv = document.createElement('div');
			btnDiv.className = 'modal-buttons';

			for (let btnName in this.#functions) {
				let data = this.#functions[btnName];
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

		return contentDiv;
	}

	/**
	 * Builds HTML for title bar of modal
	 * @param {string} title 
	 * @returns {bg} modalBar
	 */
	#buildModalTitleBar(title) {
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
}