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