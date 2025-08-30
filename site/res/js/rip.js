/**
 * rip.js
 * Helper file for the rip page
 */
"use strict"

/**
 * Sets the starting time for the embedded YouTube video to play.
 * @param {Number} start The start time in seconds for when the YouTube embed should play.
 */
function setYouTubeTimestamp(start) {
	let embed = document.getElementById('yt-embed');

	if (embed != undefined) {
		start = timestampToSeconds(start);
		let split = embed.src.split('?');
		let url = split[0];
		embed.contentWindow.location = url + "?start=" + start;
	}

	function timestampToSeconds(timestamp) {
		let split = timestamp.split(':');
		let hrs = 0;
		let mins = 0;
		let secs = 0;
		let total = 0;

		// If timestamp includes hours:
		if (split.length == 3) {
			hrs = split[0];
			mins = split[1];
			secs = split[2];
		}
		// If timestamp is only minutes and seconds:
		else {
			mins = split[0];
			secs = split[1];
		}

		total = (parseInt(hrs) * 3600) + (parseInt(mins) * 60) + parseInt(secs);
		return total;
	}
}

/**
 * Analyses the given URL to check if its a YouTube URL and has a video ID.
 * If one is found, it set the YouTube Video ID input to the video ID.
 * @param {String} url The Rips URL to analyse for a youtube video ID.
 */
function getYouTubeID(url) {
	// Only search if the url contains "youtu" in it (for youtube.com or youtu.be)
	if (url.includes('youtu')) {
		let id = null;
		let re = /http[s]{0,1}.*youtu.*\?.*v=([A-Za-z0-9_\-]{11})/;
		let matches = re.exec(url);

		if (matches.length > 0) {
			id = matches[1];
		}
		// In case the link given is a youtu.be link, scan it.
		else {
			re = /http[s]{0,1}.*youtu\.be\/([A-Za-z0-9_\-]{11})/
			matches = re.exec(url);
			if (matches.length > 0) {
				id = matches[1];
			}
		}

		if (id != null) {
			let ytID = document.getElementById('ytId');
			ytID.value = id;
		}
	}
}