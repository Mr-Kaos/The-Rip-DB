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