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
		document.getElementById('data-ytURL')?.innerText,
		document.getElementById('data-YouTubeID')?.innerText,
		document.getElementById('data-Game')?.innerText,
		jokes,
		document.getElementById('data-Platforms')?.innerText.split(';'),
		document.getElementById('data-Rippers')?.innerText.split(';'),
		document.getElementById('data-Composers')?.innerText.split(';'),
		document.getElementById('data-MixName')?.innerText,
		document.getElementById('data-AltURL')?.href,
		document.getElementById('data-AltName')?.innerText,
		document.getElementById('data-Description')?.innerText,
		document.getElementById('data-WikiURL')?.href
	);
	wiki.displaySource();
}

/**
 * Parses A wiki's source content and loads it into the rip edit page's respective fields.
 * @param {string} input The input wiki source content to parse and place into inputs on the edit page.
 */
async function parseWikiContent(input) {
	let wikiSource = input ?? document.querySelector('#wiki_source').value;
	let inputName = document.getElementById('name');
	let inputMixName = document.getElementById('mixName');
	let inputAltName = document.getElementById('altName');
	let inputURL = document.getElementById('url');
	let inputDate = document.getElementById('date');
	let inputYTID = document.getElementById('ytId');
	let inputAltURL = document.getElementById('alturl');
	let inputLength = getInputById('length', TimestampElement);
	let inputDescription = document.getElementById('description');
	let inputGame = getInputById('search_game', SearchElement);;
	let inputWikiURL = document.getElementById('wikiUrl');
	let inputRippers = getInputById('rippers', InputTable);
	let inputJokes = getInputById('jokes', InputTable);
	let inputComposers = getInputById('search_composers[]', SearchElement);

	let wikiPage = await WikiPage.fromSource(wikiSource);
	if (wikiPage != undefined) {
		inputName.value = wikiPage.ripName ?? '';
		inputMixName.value = wikiPage.mixName ?? '';
		inputAltName.value = wikiPage.altName ?? '';
		inputURL.value = wikiPage.url ?? '';
		inputDate.value = wikiPage.uploadDate ?? null;
		inputYTID.value = wikiPage.ytID ?? '';
		inputAltURL.value = wikiPage.altURL ?? '';
		inputLength.setValue(wikiPage.length ?? '');
		inputDescription.value = wikiPage.description ?? '';
		inputGame.addPill(wikiPage.game.Name, wikiPage.game.ID);
		inputWikiURL.value = wikiPage.wikiURL ?? '';

		// composers
		for (let i = 0; i < wikiPage.composers.length; i++) {
			if (wikiPage.composers[i]?.ID != null) {
				inputComposers.addPill(wikiPage.composers[i].Name, wikiPage.composers[i].ID);
			}
		}
		// jokes
		inputJokes.clear();
		for (let i = 0; i < wikiPage.jokes.length; i++) {
			if (wikiPage.jokes[i]?.ID != null) {
				let row = inputJokes.addRow();
				let jokeInput = getInputById(`search_jokes[]_${row.id}`, SearchElement);
				let jokeStartTime = getInputById(`jokeStart[]_${row.id}`, TimestampElement);
				let jokeGenre = getInputById(`search_genres[]_${row.id}`, SearchElement);

				jokeInput.addPill(wikiPage.jokes[i].Name, wikiPage.jokes[i].ID);
				jokeStartTime.setValue(wikiPage.jokes[i].StartTime);
				if (wikiPage.jokes[i].GenreName != undefined) {
					jokeGenre.addPill(wikiPage.jokes[i].GenreName, wikiPage.jokes[i].GenreID);
				}
			}
		}
		// rippers
		inputRippers.clear();
		for (let i = 0; i < wikiPage.rippers.length; i++) {
			if (wikiPage.rippers[i]?.ID != null) {
				let row = inputRippers.addRow();
				let ripperInput = getInputById(`search_rippers[]_${row.id}`, SearchElement);
				ripperInput.addPill(wikiPage.rippers[i].Name, wikiPage.rippers[i].ID);
			}
		}
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
	wikiURL;
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
	 * @param {String} url The URL of the rip's YouTube video
	 * @param {String} ytId The ID of the rip's YouTube video
	 * @param {Object} game The name and IF of the game the rip is for. Must have two keys: ID and Name.
	 * @param {Array[Object]} jokes An array of objects detailing jokes. Each joke must have the key "Name" can have the following keys: `ID`, `StartTime`, `EndTime`, `GenreName`, `GenreID`, `Name` and `Comment`.
	 * @param {Array} platforms An array of platform names the rip is on.
	 * @param {Array} rippers An array of ripper names.
	 * @param {Array} composers An array of composer names.
	 * @param {String} mixName The rip's mix name.
	 * @param {String} altURL The rip's alternate (album release) URL.
	 * @param {String} altName The rip's alternate (album release) name.
	 * @param {String} description The description of the rip.
	 * @param {String} wikiURL The URL of the rip's wiki page.
	 */
	constructor(ripName, uploadDate, length, url, ytId, game, jokes = [], platforms = [], rippers = [], composers = [], mixName = null, altURL = null, altName = null, description = null, wikiURL = null) {
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
		// this.rippers = cleanseArray(rippers);
		this.rippers = rippers;
		// this.jokes = cleanseArray(jokes);
		this.jokes = jokes;
		// this.composers = cleanseArray(composers);
		this.composers = composers;
		this.ytID = ytId?.trim();
		this.platforms = cleanseArray(platforms);
		this.game = game;
		this.wikiURL = wikiURL?.trim();
	}

	/**
	 * Creates a WikiPage object from a wiki page's source.
	 * This iterates line by line through a pasted wiki page's source code (edit mode) and extracts its information.
	 * @param {String} source The wiki page's source containing the data for the rip.
	 * @return {WikiPage} A new wiki page.
	 */
	static async fromSource(source) {
		let lines = source?.split("\n");

		// Parsing flags
		let inHeading = true;
		let inJokes = false;
		let inTable = false;
		let tableHeaders = [];
		let tableColIndex = 0;

		// cached regex expressions
		var regexJokes = new RegExp(/^.*==.joke.*==/i);
		var regexTitle = new RegExp(/^.*==.*==/i);
		var regexTableHead = new RegExp(/!\s?(.*)/);

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
		var regexRipper = new RegExp(/author\s*=\s*(?:\[\[)?(.+?)(?:\]\])?\s?(?=\s*<ref>|$)/i); // Used to see if the line is a rippers line and matches any rippers that exist after "author=".
		var regexComposers = new RegExp(/composer\s*=\s*(?:\[\[)?(.+?)(?:\]\])?\s?(?=\s*<ref>|$)/i);
		var regexAltTrack = new RegExp(/track=\s*"\[(http[^\s]+)\s+(.+?)\]"/i);
		// var regexTime = /[0-9]{1,2}:[0-9]{1,2}(:[0-9]{2})?/;

		var regexJokeFrom = new RegExp(/"([^"]*)"(?:\s+)?(?:by|from)/ig); // Finds all names of songs or references enclosed by quotes and followed by "by" or "from".
		var regexJokeFromLink = new RegExp(/"\[(?:http[^\s]+)\s+(.+?)\]"(?:\s+)?(?:by|from)/ig); // Finds all names of songs or references that are a link and are enclosed by quotes and followed by "by" or "from".
		var regexJokeTo = new RegExp(/(?:\s+)?(?:to|with|of)\s?"(?:\[\[)?((?:[^[]|)*?)(?:\]\])?"/ig); // Finds all names of songs or references that come after "to", e.g. "melody changes to "joke"".
		var regexJokeTable = new RegExp(/(?:"[\[]{2}|")(.+?)(?:[\]]{2}|").?(?:\s?\((.+?)\))?/i); // 

		// initialiser variables
		let ripName = null;
		let uploadDate = null;
		let length = null;
		let ytId = null;
		let game = null;
		let jokes = [];
		let parsedJokes = [];
		let parsedJokeTimes = [];
		let parsedJokeSources = [];
		let parsedJokeGenres = [];
		let platforms = [];
		let rippers = [];
		let parsedRippers = [];
		let composers = [];
		let parsedComposers = [];
		let mixName = null;
		let altName = null;
		let altURL = null;
		let url = null;
		let desc = null;

		/**
		 * Parses a date string in the format of Month Day, Year, where the month is the full name of the month.
		 * @param {String} dateString The date from the parsed wiki page.
		 * @returns The date in the format of Y-m-d. Null if the date is invalid.
		 */
		function parseDateString(dateString) {
			let date = null;
			if (dateString != null) {
				let matches = dateString.match(/(\w+) (\d+),? (\d+)/).map(x => x.trim());
				const inMonth = matches[1];
				const inDay = matches[2];
				const inYear = matches[3];
				const monthMap = {
					jan: 0, feb: 1, mar: 2, apr: 3, may: 4, jun: 5,
					jul: 6, aug: 7, sep: 8, oct: 9, nov: 10, dec: 11,
					january: 0, february: 1, march: 2, april: 3, may: 4, june: 5,
					july: 6, august: 7, september: 8, october: 9, november: 10, december: 11
				};

				let month = monthMap[inMonth.toLowerCase()];
				let day = parseInt(inDay, 10);
				let year = parseInt(inYear, 10);
				let dateObj = new Date(year, month, day, 12, 0);

				if (!isNaN(dateObj.getTime())) {
					year = dateObj.getFullYear();
					month = String(dateObj.getMonth() + 1).padStart(2, '0');
					day = String(dateObj.getDate()).padStart(2, '0');
					date = `${year}-${month}-${day}`;
				}
			}

			return date;
		}

		/**
		 * Parses a joke table cell.
		 * @param {string} columnType The name of the column
		 * @param {string} data The column data to check
		 * @returns 
		 */
		function parseTableData(columnType, data) {
			columnType = columnType.toLowerCase();
			if (columnType.includes('joke')) {
				let matches = data.match(regexJokeTable);

				parsedJokes.push(matches != null ? matches[1] : null);
				parsedJokeGenres.push(matches != null ? matches[2] ?? null : null);
			} else if (columnType.includes('source')) {
				parsedJokeSources.push(data);
			} else if (columnType.includes('time')) {
				parsedJokeTimes.push(data);
			}
		}

		// Parse each line one at a time
		for (let i = 0; i < lines.length; i++) {
			if (!inJokes && lines[i].trim().startsWith('{{')) {
				inHeading = true;
			} else if (lines[i].trim().startsWith('{|')) {
				inTable = true;
			} else if (lines[i].trim().startsWith('|}')) {
				inTable = false;
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
					uploadDate = parseDateString(lines[i].match(regexUpload)[1] ?? null);
				} else if (regexLength.test(lines[i])) {
					length = (lines[i].match(regexLength)[1] ?? null);
				} else if (regexRipper.test(lines[i])) {
					// Cleanse the ripper string in case refs are used
					let cleansed = lines[i].replaceAll(regexRipperCleanse, '');
					let ripperMatches = [...cleansed.matchAll(regexRipperLinked)];
					if (ripperMatches.length > 0) {
						for (let i = 0; i < ripperMatches.length; i++) {
							parsedRippers.push(ripperMatches[i][1]);
						}
					} else {
						let ripper = cleansed.match(regexRipper);
						parsedRippers.push(ripper[1]);
					}
				} else if (regexAltTrack.test(lines[i])) {
					let matches = lines[i].match(regexAltTrack);
					altURL = (matches[1] ?? null);
					altName = (matches[2] ?? null);
				} else if (regexComposers.test(lines[i])) {
					parsedComposers = (lines[i].match(regexComposers)[1] ?? null)?.split(',');
				} else if (regexPlatform.test(lines[i])) {
					platforms = (lines[i].match(regexPlatform)[1] ?? '').split(',');
				} else if (regexDescription.test(lines[i])) {
					desc = (lines[i].match(regexDescription)[1] ?? '');
				}
			} else if (inJokes) {
				if (inTable) {
					// Get headings
					if (regexTableHead.test(lines[i])) {
						tableHeaders.push(lines[i].match(regexTableHead)[1]);
					}
					// ignore captions
					else if (lines[i].trim().startsWith('|+')) { }
					// reset row
					else if (lines[i].trim().startsWith('|-')) {
						tableColIndex = 0;
					}
					// Table data
					else if (lines[i].trim().startsWith('|')) {
						// Check if there are multiple columns in the line
						if (lines[i].trim().includes('||')) {
							let cols = lines[i].trim().substring(1).trim().split('||');
							for (let j = 0; j < cols.length; j++) {
								parseTableData(tableHeaders[j], cols[j]);
							}
						} else {
							parseTableData(tableHeaders[tableColIndex], lines[i].trim().substring(1));
							tableColIndex++;
						}
					}
				} else {
					// Find jokes that contain links first. Any links found will be removed from the string after
					let matches = [...lines[i].matchAll(regexJokeFromLink)];
					for (let i = 0; i < matches.length; i++) {
						parsedJokes.push(matches[i][1]);
					}
					lines[i] = lines[i].replaceAll(regexJokeFromLink, '');
					matches = [...lines[i].matchAll(regexJokeFrom)];
					for (let i = 0; i < matches.length; i++) {
						parsedJokes.push(matches[i][1]);
					}
					matches = [...lines[i].matchAll(regexJokeTo)];
					for (let i = 0; i < matches.length; i++) {
						parsedJokes.push(matches[i][1]);
					}
					// Clear any duplicates
					parsedJokes = [...new Set(parsedJokes)];
				}
			} else {
				// console.log("Neither...", lines[i]);
			}
		}

		// Find Game in database
		if (game != null) {
			let url = `/rips/find-game?game=${game}`;

			let request = await fetch(url);
			if (request.ok) {
				let response = await request.json();
				if (response?.length > 1) {
					console.warn("A game needs to be chosen from this list!", response);
				} else {
					game = response;
				}
			}
		}

		async function findDBKeyPairs(items, url, outputList) {
			// Find Jokes in the Database
			if (items.length > 0) {
				url = `${url}?`;
				for (let i = 0; i < items.length; i++) {
					url += `p[]=${encodeURI(items[i])}&`;
				}

				let request = await fetch(url, {
					method: 'GET'
				});
				if (request.ok) {
					let response = await request.json();
					// Parse each joke in the returned response. If a joke ID is given, add it to the jokes table input. Else, mark is for manual addition to the database.
					for (let name in response) {
						if (typeof (response[name]) == 'number') {
							outputList.push({ Name: name, ID: response[name] })
						} else {
							console.warn("NEED TO ADD JOKE TO DB:", name);
							// jokes.push({ Name: name, ...response[name] })
						}
					}
				}
			}
		}

		let jokeIDs = [];
		let genres = [];
		await findDBKeyPairs(parsedJokes, '/rips/find-jokes', jokeIDs);
		await findDBKeyPairs(parsedRippers, '/rips/find-rippers', rippers);
		await findDBKeyPairs(parsedComposers, '/rips/find-composers', composers);
		await findDBKeyPairs(parsedJokeGenres, '/rips/find-genres', genres);

		// Create jokes object and group with any timestamps
		for (let i = 0; i < parsedJokes.length; i++) {
			if (parsedJokes[i] != null && parsedJokes[i] != '') {
				let id = null;
				for (let joke in jokeIDs) {
					if (jokeIDs[joke].Name.toLowerCase() == parsedJokes[i]?.toLowerCase()) {
						id = jokeIDs[joke]['ID'];
						break;
					}
				}
				// check if a matching genre exists.
				let jokeObj = {
					'Name': parsedJokes[i],
					'StartTime': parsedJokeTimes[i],
					'ID': id
				}
				for (let genreID in genres) {
					if (parsedJokeGenres[i]?.toLowerCase() == genres[genreID].Name.toLowerCase()) {
						jokeObj.GenreID = genres[genreID].ID;
						jokeObj.GenreName = genres[genreID].Name;
						break;
					}
				}

				jokes.push(jokeObj);
			}
		}

		// console.log(ripName, uploadDate, length, url, ytId, game, jokes, platforms, rippers, composers, mixName, altURL, altName, desc);

		return new WikiPage(ripName, uploadDate, length, url, ytId, game, jokes, platforms, rippers, composers, mixName, altURL, altName, desc);
	}

	/**
	 * Reads the source of a Wiki page and parses it into a rip.
	 * @param {String} url The URL of the wiki page to retrieve.
	 */
	static async wikiSourceToRip(url) {
		url = URL.parse(url);
		if (url != null) {
			// Validate the url
			if (!url.search.includes('action=edit')) {
				if (!url.search.includes('?')) {
					url.search = '?';
				}
				url.search += 'action=edit';
			}

			let data = new FormData();
			data.append('url', url.href);

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
		let source = this.toSource();
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
		let source = `{{Rip
|image=
|link= ${this.ytID}
|playlist=
|playlist id=
|upload= ${this.uploadDate}
|length= ${this.length}
|author= ${this.rippers?.join(", ")}
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
