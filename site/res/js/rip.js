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

/**
 * Object to store a data required for a Wiki Page being exported.
 */
class WikiPage {
	#title;
	#ripName;
	#mixName;
	#altName;
	#uploadDate;
	#length;
	#composers = [];
	#rippers = [];
	#jokes = [];
	#ytID;
	#platform;
	#game;
	#genres = [];

	#linkRippers = false;
	#linkJokes = false;
	#linkComposers = false;

	// Boolean flags
	#addLinksToJokes = false;

	/**
	 * 
	 * @param {String} ripName The name of the rip
	 * @param {String} uploadDate The date the the rip was uploaded
	 * @param {String} length The duration of the rip
	 * @param {String} ytId The ID of the rip's YouTube video
	 * @param {String} game The name of the game the rip is for
	 * @param {String} platform The name of the platform the rip is for
	 * @param {Array[Object]} jokes An array of objects detailing jokes. Each joke must have the following keys: `Time` and `Joke`. Optionally the `Comment` key can be specified.
	 * @param {Array} rippers An array of ripper names.
	 * @param {Array} composers An array of composer names.
	 * @param {String} mixName The rip's mix name.
	 * @param {String} altName The rip's alternate (album release) name.
	 */
	constructor(ripName, uploadDate, length, ytId, game, platform, jokes, rippers = [], composers = [], genres = [], mixName = null, altName = null) {
		this.#ripName = ripName;
		this.#mixName = mixName;
		this.#altName = altName;
		this.#uploadDate = uploadDate;
		if (length.substring(0, 3) == "00:") {
			length = length.substring(3);
		}
		this.#length = length;
		this.#rippers = Array.isArray(rippers) ? rippers : rippers.split(';');;
		this.#jokes = Array.isArray(jokes) ? jokes : jokes.split(';');;
		this.#composers = Array.isArray(composers) ? composers : composers.split(';');
		this.#ytID = ytId;
		this.#platform = platform;
		this.#game = game;
	}

	/**
	 * Reads the source of a Wiki page and parses it into a rip.
	 */
	static wikiSourceToRip() {

	}

	/**
	 * Generates the source for the wiki page.
	 * @return {String} The generated source.
	 */
	generate() {
		let pageSource = "";

		pageSource += this.#buildMetadataSource();
		pageSource += this.#buildOverviewSource();
		pageSource += this.#buildJokesSource();

		return pageSource
	}

	/**
	 * Opens a modal that displays the generated source for the wiki page.
	 */
	displaySource() {
		let source = this.generate();
		let sourceContainer = document.createElement('textarea');
		sourceContainer.style.width = "95%";
		sourceContainer.style.height = "90%";
		sourceContainer.style.margin = "auto";
		sourceContainer.style.display = null;
		sourceContainer.value = source;
		let funcs = {
			'Copy Source': {
				function: function () {
					sourceContainer.select();
					sourceContainer.setSelectionRange(0, 99999);

					if (window.getSelection) {
						var range = document.createRange();
						range.selectNode(sourceContainer);
						window.getSelection().removeAllRanges();
						window.getSelection().addRange(range);
					}

					navigator.clipboard.writeText(sourceContainer.value);
				},
				close: false
			}
		}

		let modal = new Modal("WikiSourcePreview", "Wiki Page Generator", sourceContainer, "50%", '50%', true, TransformStreamDefaultController, funcs);
		modal.open();
	}

	/**
	 * Builds the metadata content for the right-hand panel of a wiki page.
	 */
	#buildMetadataSource() {
		let source = `{{Rip 
|image= 
|link= ${this.#ytID}
|playlist= 
|playlist id= 
|upload= ${this.#uploadDate}
|length= ${this.#length}
|author= ${this.#rippers.join(", ")}
|album= 
|track= ${this.#altName ?? ''}
|music= ${this.#ripName} ${(this.#mixName == null) ? '(' + this.#mixName + ')' : ''}
|composer= ${this.#composers.join(", ")}
|platform= ${this.#platform}
|catchphrase= 
}}\n\n`
		return source;
	}

	/**
	 * Builds the overview section of the wiki page.
	 */
	#buildOverviewSource() {
		let source = `"'''${this.#ripName} ${this.#mixName} - ${this.#game}'''" is a high quality rip of ${this.#ripName} from ${this.#game}.\n\n`;

		return source;
	}

	/**
	 * Builds the jokes section of the wiki page.
	 * @param {String} tableClass The CSS classname to use for the wiki's table. Defaults to "wiki-table".
	 */
	#buildJokesSource(tableClass = "wiki-table") {
		let source = "==Jokes==\n";

		if (this.#jokes.length > 0) {
			source = `==Jokes==\n{| class="${tableClass}"\n!Time\n!Joke\n`;

			for (let i = 0; i < this.#jokes.length; i++) {
				source += `|-\n|${(this.#jokes[i]?.Time ?? "??:??")}\n|${this.#jokes[i]?.Joke}\n`;
			}
			source += `|}`;
		} else {
			source += "This rip needs to be documented!\n";
		}

		return source;
	}
}

/**
 * Generates the source for a wiki page and displays it to the user.
 */
function generateWikiPage() {
	let jokesSource = document.getElementById("data-Jokes");
	let jokes = [];
	// Parse jokes form the table. (might need to find a way to determine if the joke has a wiki page so the links can be generated for it)
	for (let i = 0; i < jokesSource.childElementCount; i++) {
		jokes.push({
			'Time': jokesSource.children[i].children[0].innerText == "" ? null : jokesSource.children[i].children[0].innerText,
			'Joke': jokesSource.children[i].children[1].innerText == "" ? null : jokesSource.children[i].children[1].innerText,
			'Comment': jokesSource.children[i].children[2].innerText == "" ? null : jokesSource.children[i].children[2].innerText
		});
	}

	let wiki = new WikiPage(
		document.getElementById('data-RipName')?.innerText,
		document.getElementById('data-UploadDate')?.innerText,
		document.getElementById('data-Length')?.innerText,
		document.getElementById('data-YouTubeID')?.innerText,
		document.getElementById('data-Game')?.innerText,
		document.getElementById('data-Platform')?.innerText,
		jokes,
		document.getElementById('data-Composers')?.innerText,
		document.getElementById('data-Genres')?.innerText,
		document.getElementById('data-MixName')?.innerText,
		document.getElementById('data-AltName')?.innerText
	);
	wiki.displaySource();
}
