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

function importFromWiki() {
	let template = document.getElementById("template-import").cloneNode(true);
	let input = template.querySelector('#wiki_source');
	let funcs = {
		'Cancel': {
			className: "btn-bad"
		},
		'Parse Page': {
			function: function () {
				parseWikiContent(input.value)
			},
			close: false
		}
	};
	template.style.display = null;
	let modal = new Modal("WikiImport", "Wiki Page Parser", template, "50%", '20%', true, true, funcs);
	modal.open();
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
		jokes,
		document.getElementById('data-Platforms')?.innerText,
		document.getElementById('data-Rippers')?.innerText,
		document.getElementById('data-Composers')?.innerText,
		document.getElementById('data-Genres')?.innerText,
		document.getElementById('data-MixName')?.innerText,
		document.getElementById('data-AltName')?.innerText
	);
	wiki.displaySource();
}

function parseWikiContent(input) {
	let wikiSource = input ?? document.querySelector('#wiki_source').value;
	let inputName = document.getElementById('name');
	let inputMixName = document.getElementById('mixName');
	let inputAltName = document.getElementById('altName');
	let inputURL = document.getElementById('url');
	let inputYTID = document.getElementById('ytId');
	let inputAltURL = document.getElementById('alturl');
	let inputLength = document.getElementById('length');
	let inputDescription = document.getElementById('description');
	let inputGame = document.getElementById('game');
	let inputWikiURL = document.getElementById('wikiUrl');
	// let inputRippers = document.getElementById('name');
	// let inputJokes = document.getElementById('name');

	let wikiPage = WikiPage.fromSource(wikiSource);
	if (wikiPage != undefined) {
		inputName.value = wikiPage.ripName ?? '';
		inputMixName.value = wikiPage.mixName ?? '';
		inputAltName.value = wikiPage.altName ?? '';
		inputURL.value = wikiPage.url ?? '';
		inputYTID.value = wikiPage.ytID ?? '';
		inputAltURL.value = wikiPage.altURL ?? '';
		inputLength.value = wikiPage.length ?? '';
		inputDescription.value = wikiPage.description ?? '';
		// inputGame.value = wikiPage.ripName;
		inputWikiURL.value = wikiPage.ripName ?? '';
	}
}

/**
 * Object to store a data required for a Wiki Page being exported.
 */
class WikiPage {
	title;
	ripName;
	mixName;
	altName;
	url;
	altURL;
	description;
	uploadDate;
	length;
	composers = [];
	rippers = [];
	jokes = [];
	ytID;
	platforms;
	game;
	mixName;
	genres = [];

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
	 * @param {Array} platforms The name of the platforms the rip is for
	 * @param {Array[Object]} jokes An array of objects detailing jokes. Each joke should have the following keys: `Time` and `Joke`. Optionally the `Comment` key can be specified.
	 * @param {Array} rippers An array of ripper names.
	 * @param {Array} composers An array of composer names.
	 * @param {String} mixName The rip's mix name.
	 * @param {String} altName The rip's alternate (album release) name.
	 * @param {String} altURL The rip's alternate (album release) URL.
	 */
	constructor(ripName, uploadDate, length, url, ytId, game, jokes = [], platforms = [], rippers = [], composers = [], genres = [], mixName = null, altURL = null, altName = null, description = null) {
		function cleanseArray(items) {
			if (items != null) {
				items = Array.isArray(items) ? items : items?.split(';');
				for (let i = 0; i < items.length; i++) {
					items[i] = items[i].trim();
				}
			}
			return items;
		}

		this.ripName = ripName?.trim();
		this.mixName = ((mixName == "") ? null : mixName)?.trim();
		this.altName = altName?.trim();
		this.url = url?.trim();
		this.altURL = altURL?.trim();
		this.uploadDate = uploadDate?.trim();
		this.description = description?.trim();
		if (length?.substring(0, 3) == "00:") {
			length = length.substring(3);
		}
		this.length = length?.trim();
		// rippers = Array.isArray(rippers) ? rippers : rippers?.split(';');
		// for (let i = 0; i < rippers.length; i++) {
		// 	rippers[i] = rippers[i].trim();
		// }
		this.rippers = cleanseArray(rippers);
		this.jokes = cleanseArray(jokes);
		this.composers = cleanseArray(composers);
		this.ytID = ytId?.trim();
		this.platforms = cleanseArray(platforms);
		this.game = game?.trim();
	}

	/**
	 * Creates a WikiPage object from a wiki page's source.
	 * @param {String} source The wiki page's source containing the data for the rip.
	 * @return {WikiPage} A new wiki page.
	 */
	static fromSource(source) {
		let lines = source?.split("\n");

		// Parsing flags
		let inHeading = false;
		let inJokes = false;

		// cached regex expressions
		var regexJokes = new RegExp(/^.*==.joke.*==/i);
		var regexTitle = new RegExp(/^.*==.*==/i);
		var regexLink = new RegExp(/link.*=?.([A-Za-z0-9_\-]{11})/i);
		var regexRipNameBraces = new RegExp(/music\s*=\s*(.*?)\s*\(([^)]+)\)\s*$/i);
		var regexRipName = new RegExp(/music\s*=\s*(.*)/i);
		var regexGame = new RegExp(/playlist\s*=\s*(.+?)(?:\s*;|$)/i);
		var regexUpload = new RegExp(/upload.*=(.*)/i);
		var regexLength = new RegExp(/length.*=(.*)/i);
		var regexPlatform = new RegExp(/platform.*=(.*)/i);
		var regexPlaylist = new RegExp(/playlist.*=(.*)/i);
		var regexDescription = new RegExp(/catchphrase.*=(.*)/i);
		var regexRipperCleanse = new RegExp(/<ref\b[^>]*>(.*?)<\/ref>/ig); // Used to remove all refs in a ripper line
		var regexRipperLinked = new RegExp(/(?:\[\[)?((?:[^[]|)*?)(?:\]\])/ig); // Finds rippers enclosed between a pair of two square braces
		var regexRipper = new RegExp(/author\s*=\s*(?:\[\[)?(.+?)(?:\]\])?\s?(?=\s*<ref>|$)/ig); // Used to see if the line is a rippers line and matches any rippers that exist after "author=".
		var regexComposers = new RegExp(/composer\s*=\s*(?:\[\[)?(.+?)(?:\]\])?\s?(?=\s*<ref>|$)/i);
		var regexAltTrack = new RegExp(/track=\s*"\[(http[^\s]+)\s+(.+?)\]"/i);

		var regexJokeFrom = new RegExp(/"([^"]*)"(?:\s+)?(?:by|from)/ig); // Finds all names of songs or references enclosed by quotes and followed by "by" or "from".
		var regexJokeFromLink = new RegExp(/"\[(?:http[^\s]+)\s+(.+?)\]"(?:\s+)?(?:by|from)/ig); // Finds all names of songs or references that are a link and are enclosed by quotes and followed by "by" or "from".
		var regexJokeTo = new RegExp(/(?:\s+)?(?:to|with|of)\s?"(?:\[\[)?((?:[^[]|)*?)(?:\]\])?"/ig); // Finds all names of songs or references that come after "to", e.g. "melody changes to "joke"".

		// initialiser variables
		let ripName = null;
		let uploadDate = null;
		let length = null;
		let ytId = null;
		let game = null;
		let jokes = [];
		let platforms = [];
		let rippers = [];
		let composers = null;
		let genres = null;
		let mixName = null;
		let altName = null;
		let altURL = null;
		let url = null;
		let desc = null;

		for (let i = 0; i < lines.length; i++) {
			if (!inJokes && lines[i].trim().startsWith('{{')) {
				inHeading = true;
			} else if (lines[i].trim().startsWith('}}')) {
				inHeading = false;
			} else if (regexJokes.test(lines[i])) {
				inJokes = true;
			} else if (regexTitle.test(lines[i]) && inJokes) {
				inJokes = false;
			}

			// If in the heading, find all heading related data.
			if (inHeading) {
				if (regexLink.test(lines[i])) {
					ytId = (lines[i].match(regexLink)[1] ?? null);
					url = `https://youtube.com/watch?v=${ytId}`;
				} else if (regexGame.test(lines[i])) {
					game = (lines[i].match(regexGame)[1] ?? null);
				} else if (regexRipNameBraces.test(lines[i])) {
					let matches = lines[i].match(regexRipNameBraces);
					ripName = (matches[1] ?? null);
					mixName = (matches[2] ?? null);
				} else if (regexRipName.test(lines[i])) {
					ripName = (lines[i].match(regexRipName)[1] ?? null);
				} else if (regexUpload.test(lines[i])) {
					uploadDate = (lines[i].match(regexUpload)[1] ?? null);
				} else if (regexLength.test(lines[i])) {
					length = (lines[i].match(regexLength)[1] ?? null);
				} else if (regexRipper.test(lines[i])) {
					// Cleanse the ripper string in case refs are used
					let cleansed = lines[i].replaceAll(regexRipperCleanse, '');
					let ripperMatches = [...cleansed.matchAll(regexRipperLinked)];
					if (ripperMatches.length > 0) {
						for (let i = 0; i < ripperMatches.length; i++) {
							rippers.push(ripperMatches[i][1]);
						}
					} else {
						let ripper = cleansed.match(regexRipper);
						rippers.push(ripper[1]);
					}
				} else if (regexAltTrack.test(lines[i])) {
					let matches = lines[i].match(regexAltTrack);
					altURL = (matches[1] ?? null);
					altName = (matches[2] ?? null);
				} else if (regexComposers.test(lines[i])) {
					composers = (lines[i].match(regexComposers)[1] ?? null);
				} else if (regexPlatform.test(lines[i])) {
					platforms = (lines[i].match(regexPlatform)[1] ?? '').split(',');
				} else if (regexDescription.test(lines[i])) {
					desc = (lines[i].match(regexDescription)[1] ?? '');
				}
			} else if (inJokes) {
				// Find jokes that contain links first. Any links found will be removed from the string after
				let matches = [...lines[i].matchAll(regexJokeFromLink)];
				for (let i = 0; i < matches.length; i++) {
					jokes.push(matches[i][1]);
				}
				lines[i] = lines[i].replaceAll(regexJokeFromLink, '');
				matches = [...lines[i].matchAll(regexJokeFrom)];
				for (let i = 0; i < matches.length; i++) {
					jokes.push(matches[i][1]);
				}
				matches = [...lines[i].matchAll(regexJokeTo)];
				for (let i = 0; i < matches.length; i++) {
					jokes.push(matches[i][1]);
				}

				jokes = [...new Set(jokes)];
			} else {
				// console.log("Neither...", lines[i]);
			}
		}

		console.log(ripName, uploadDate, length, url, ytId, game, jokes, platforms, rippers, composers?.split(','), genres, mixName, altURL, altName, desc);

		return new WikiPage(ripName, uploadDate, length, url, ytId, game, jokes, platforms, rippers, composers?.split(','), genres, mixName, altURL, altName, desc);
	}

	/**
	 * Reads the source of a Wiki page and parses it into a rip.
	 * @param {String} url The URL of the wiki page to retrieve.
	 */
	static async wikiSourceToRip(url) {
		url = URL.parse(url);
		if (url != null) {
			console.log(url);

			// Validate the url
			if (!url.search.includes('action=edit')) {
				if (!url.search.includes('?')) {
					url.search = '?';
				}
				url.search += 'action=edit';
			}

			let data = new FormData();
			data.append('url', url.href);

			// let response = await fetch('/rips/import', {
			// 	body: data,
			// 	method: 'POST'
			// });

			let response = await fetch(url.href);

			if (response.ok) {
				let result = response.body;
				console.log(result);
			}
		}
	}

	/**
	 * Generates the source for the wiki page.
	 * @return {String} The generated source.
	 */
	toSource() {
		let pageSource = "";

		pageSource += this.#buildMetadataSource();
		pageSource += this.#buildOverviewSource();
		pageSource += this.#buildJokesSource();

		return pageSource;
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

		let modal = new Modal("WikiSourcePreview", "Wiki Page Generator", sourceContainer, "50%", '50%', true, true, funcs);
		modal.open();
	}

	/**
	 * Builds the metadata content for the right-hand panel of a wiki page.
	 */
	#buildMetadataSource() {
		console.log(this.mixName);
		let source = `{{Rip
|image=
|link= ${this.ytID}
|playlist=
|playlist id=
|upload= ${this.uploadDate}
|length= ${this.length}
|author= ${this.rippers.join(", ")}
|album=
|track= ${this.altName ?? ''}
|music= ${this.ripName} ${(this.mixName != null) ? '(' + this.mixName + ')' : ''}
|composer= ${this.composers.join(", ")}
|platform= ${this.platforms.join(", ")}
|catchphrase=
}}\n\n`
		return source;
	}

	/**
	 * Builds the overview section of the wiki page.
	 */
	#buildOverviewSource() {
		let source = `"'''${this.ripName}${(this.mixName != null) ? ' (' + this.mixName + ')' : ''} - ${this.game}'''" is a high quality rip of ${this.ripName} from ${this.game}.\n\n`;

		return source;
	}

	/**
	 * Builds the jokes section of the wiki page.
	 * @param {String} tableClass The CSS classname to use for the wiki's table. Defaults to "wiki-table".
	 */
	#buildJokesSource(tableClass = "wiki-table") {
		let source = "==Jokes==\n";

		if (this.jokes.length > 0) {
			source = `==Jokes==\n{| class="${tableClass}"\n!Time\n!Joke\n`;

			for (let i = 0; i < this.jokes.length; i++) {
				source += `|-\n|${(this.jokes[i]?.Time ?? "??:??")}\n|${this.jokes[i]?.Joke}\n`;
			}
			source += `|}`;
		} else {
			source += "This rip needs to be documented!\n";
		}

		return source;
	}
}
