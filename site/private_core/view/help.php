<main>
	<h1>Help / FAQ</h1>
	<p>Unsure of something? This page might be able to help.</p>
	<section>
		<h2>How Are Rips Tagged?</h2>
		<p>Rips are tagged through the jokes they are associated with. Rips don't explicitly have tags linked to them, but instead have their jokes listed.</p>
		<p>Tags are used to find similar jokes, and consequently, rips. See the <a href="#relation-diagram">diagram below</a> for an illustration of how rips are tagged and relate to jokes.</p>
	</section>
	<section>
		<h2>How Are Jokes Tagged?</h2>
		<p>Jokes are what contain tags in the database. Tags are used to classify a joke and make it easier to find similar jokes, and thus similar rips.</p>
		<p>Jokes are tagged in two ways - <b>directly</b> and through <b>Joke Metas</b>.</p>
		<ul>
			<li><b>Direct tags</b> are associated directly to the joke itself and determine the most defining attributes of the joke.<br>Each joke must have exactly one standard tag set as its "primary" tag, which is its most prominent attribute.</li>
			<li><b>Joke Metas</b> are broader, more generalised jokes used to categorise them based on what the joke is or its origins.<br>Meta jokes themselves have their own tags too. See <a href="#tag-v-meta">Jokes vs Meta Jokes and Tags</a> below for more information.</li>
		</ul>
		<p>Below are some examples on how these are used.<br><i>Note that bolded tags are the primary tags.</i></p>
		<details class="example">
			<summary>Example Joke - &OpenCurlyDoubleQuote;Meet the Flintstones</a>&CloseCurlyDoubleQuote;</summary>
			<p>For example, the joke &OpenCurlyDoubleQuote;<a href="https://siivagunner.fandom.com/wiki/Meet_the_Flintstones">Meet the Flintstones</a>&CloseCurlyDoubleQuote; could have the following tags:</p>
			<b>Standard Tags</b><br>
			<ul>
				<li style="font-weight: bold;">Theme Song</li>
				<li>Song</li>
				<li>Cartoon</li>
			</ul>
			<b>Joke Metas</b>
			<ul>
				<li>The Flintstones<br><i>Animated Series Meta</i></li>
			</ul>
			<p>The primary tag of <b>Theme Song</b> is used as this joke in itself is a theme song. The other tags of <b>Song</b> and <b>Cartoon</b> are given as this joke is a song, and is from a cartoon - namely The Flintstones.</p>
			<p>The Meta Joke of <b>The Flintstones</b> is given as the joke is from the animated series "The Flintstones", hence the meta joke's falling under the "Animated Series" meta.</p>
		</details>
		<details class="example">
			<summary>Example Joke - &OpenCurlyDoubleQuote;Megalovania</a>&CloseCurlyDoubleQuote;</summary>
			<p>The joke &OpenCurlyDoubleQuote;<a href="https://siivagunner.fandom.com/wiki/Undertale">Megalovania</a>&CloseCurlyDoubleQuote; could could have these tags:</p>
			<b>Standard Tags</b><br>
			<ul>
				<li style="font-weight: bold;">VGM</li>
				<li>Song</li>
			</ul>
			<b>Joke Metas</b>
			<ul>
				<li>Undertale<br><i>Video Game Meta</i></li>
				<li>VGM<br><i>Music Meta</i></li>
			</ul>
			<p>The primary tag of <b>VGM</b> is used as this joke in itself is music from a video game. The other tag of <b>Song</b> is given as a more general tag.</p>
			<p>The Meta Joke of <b>Undertale</b> is given as the joke is from the video game "Undertale", hence the meta joke's falling under the "Video Game" meta.</p>
		</details>
		<details class="example">
			<summary>Example Joke - &OpenCurlyDoubleQuote;Deez Nuts</a>&CloseCurlyDoubleQuote;</summary>
			<p>The joke &OpenCurlyDoubleQuote;<a href="https://siivagunner.fandom.com/wiki/Deez_Nuts">Deez Nuts</a>&CloseCurlyDoubleQuote; could could have these tags:</p>
			<b>Standard Tags</b><br>
			<ul>
				<li style="font-weight: bold;">Meme</li>
				<li>Video</li>
			</ul>
			<b>Joke Metas</b>
			<ul>
				<li>Vine<br><i>Viral Video Meta</i></li>
			</ul>
			<p>The primary tag of <b>Meme</b> is used as this joke in itself is sourced from an internet meme. The other tag of <b>Video</b> is given as the meme originated from a video.</p>
			<p>The Meta Joke of <b>Vine</b> is given as the joke originated from the social media platform Vine. Since Vine was known for hosting many viral videos, its Meta is "Viral Video".</p>
		</details>
	</section>
	<section>
		<h2 id="tag-v-meta">Jokes vs Meta Jokes and Tags - What's the Difference?</h2>
		<p>Since many jokes share common elements, such as their source material, style/genre or medium, meta jokes (and their tags) are used to organise jokes into broader, yet specific groups.<br>
			The purpose of this is to provide the ability to find rips using broader search filters.</p>
		<p>For example, suppose someone wanted to find all rips that feature a song by the music artist Daft Punk. A meta joke of Daft Punk can be defined, which will contain more specific jokes and references to them, such as their songs or persona.</p>
		<p id="relation-diagram">The diagram below illustrates how jokes and meta jokes are related.</p>
		<img src="res/img/Object_Relations_Diagram.svg" width="1000px" style="overflow-x:scroll;max-width:90vw">
		<p>Notice how some jokes share multiple meta jokes. This allows for one joke to be found through multiple metas.</p>
		<p>For example, the joke "Gangnam Style" is categorised under the "Psy" and "K-POP" meta jokes, which fall under the "Music Artists" and "Music" metas respectively.</p>
	</section>
</main>