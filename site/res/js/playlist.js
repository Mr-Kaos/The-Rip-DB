/**
 * playlist.js
 * Author: Mr Kaos
 * Created: 09/08/2025
 * 
 * Provides user-interaction features to playlists.
 */

function toggleRipPreview(id) {
	let preview = document.getElementById(id);
	if (preview != null) {
		let embed = preview.querySelector('iframe');
		if (preview.style.display == 'none') {
			preview.style.display = null;
			embed.src = `https://www.youtube-nocookie.com/embed/${id}`;
		} else {
			preview.style.display = 'none';
			embed.src = `about:blank`;
		}
	}
}