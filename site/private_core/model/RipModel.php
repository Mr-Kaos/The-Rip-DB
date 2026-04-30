<?php

namespace RipDB\Model;

require_once('Model.php');

class RipModel extends Model implements ResultsetSearch
{
	const TABLE = 'Rips';
	const VIEW = 'vw_RipsDetailed';
	const COLUMNS = ['RipID', 'RipName', 'RipAlternateName', 'RipDescription', 'RipDate', 'RipURL', 'RipAlternateURL', 'RipYouTubeID', 'RipLength', 'RipGame', 'GameName', 'RipChannel', 'ChannelName', 'ChannelURL', 'MixName', 'RipWikiURL', 'ChannelWikiURL'];
	const SORT_RipName = 'Name';
	const SORT_RipAlternateName = 'AltName';
	const SORT_RipDescription = 'Description';
	const SORT_RipDate = 'Date';
	const SORT_RipLength = 'Length';
	const SORT_RipChannel = 'Channel';

	/**
	 * Gets data about a specific rip.
	 */
	public function getRip(int $id)
	{
		$qry = $this->db
			->table(self::VIEW)
			->columns(...self::COLUMNS)
			->eq('RipID', $id);

		// Get jokes and rips from the resultset of rips.
		$ripJokes = $this->getRipJokes($qry);
		$rippers = $this->getRipRippers($qry);
		$composers = $this->getRipComposers($qry);
		$rip = $qry->findOne();


		// Apply jokes to rips
		$rip['Jokes'] = [];
		foreach ($ripJokes as $joke) {
			$rip['Jokes'][$joke['JokeID']] = $joke;
		}

		// Apply rippers to rips
		$rip['Rippers'] = [];
		foreach ($rippers as $ripper) {
			$rip['Rippers'][$ripper['RipperID']] = $ripper;
		}

		// Apply composers to rips
		$rip['Composers'] = [];
		foreach ($composers as $composer) {
			$rip['Composers'][$composer['ComposerID']] = $composer;
		}

		// Apply game platforms to rip
		$rip['Platforms'] = $this->getRipPlatforms($rip['RipGame']);

		return $rip;
	}

	public function getRippers(bool $idOnly = false)
	{
		$values = $this->db->table('Rippers');
		if ($idOnly) {
			$values = $this->setSubArrayValueToKey($values->columns('RipperID')->findAll(), 'RipperID');
			return array_keys($values);
		} else {
			return $values->columns('RipperID', 'RipperName')->findAll();
		}
	}

	public function getChannels(bool $idOnly = false)
	{
		$values = $this->db->table('Channels')
			->columns('ChannelID', 'ChannelName')
			->eq('IsActive', 1)
			->findAll();
		if ($idOnly) {
			$values = $this->setSubArrayValueToKey($values, 'ChannelID');
			return array_keys($values);
		} else {
			return $values;
		}
	}

	public function getGames(bool $idOnly = false)
	{
		$values = $this->db->table('Games');
		if ($idOnly) {
			return $values->findAllByColumn('GameID');
		} else {
			return $values->columns('GameID', 'GameName')->findAll();
		}
	}

	public function getJokes(bool $idOnly = false)
	{
		$values = $this->db->table('Jokes');
		if ($idOnly) {
			return $values->findAllByColumn('JokeID');
		} else {
			return $values->columns('JokeID', 'JokeName')->findAll();
		}
	}

	public function getGenres(bool $idOnly = false)
	{
		$values = $this->db->table('Genres');
		if ($idOnly) {
			return $values->findAllByColumn('GenreID');
		} else {
			return $values->columns('GenreID', 'GenreName')->findAll();
		}
	}

	public function getComposers(bool $idOnly = false)
	{
		$values = $this->db->table('vw_Composers');
		if ($idOnly) {
			return $values->findAllByColumn('ComposerID');
		} else {
			return $values->columns('ComposerID', 'ComposerName')->findAll();
		}
	}

	/**
	 * Returns a boolean indicating if the channel associated to the rip has a wiki or not.
	 */
	public function channelHasWiki(int $channelId): bool
	{
		return $this->db->table('Channels')
			->eq('ChannelID', $channelId)
			->notNull('WikiURL')
			->exists();
	}

	/**
	 * Finds all jokes that are contained in the given resultset of rips.
	 * @return A 2D array where each key is the ID of the joke and the values are the columns from the Jokes Table, along with its tags.
	 */
	private function getRipJokes($ripQuery)
	{
		$qry = $this->db->table('Jokes')
			->columns('r.RipID, RipJokes.JokeID', 'JokeName', 'JokeTimestamps', 'JokeComment', 'GenreName', 'Genres.GenreID')
			->join('RipJokes', 'JokeID', 'JokeID')
			->join('Genres', 'GenreID', 'GenreID', 'RipJokes')
			->innerJoinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipJokes');

		$tags = $this->getJokeTags($qry);
		$jokes = $qry->findAll();

		$tags = $this->setSubArrayValueToKey2D($tags, 'JokeID');

		foreach ($jokes as &$joke) {
			$joke['Tags'] = [];
			foreach ($tags[$joke['JokeID']] ?? [] as $tag) {
				$id = $tag['TagID'];
				unset($tag['TagID']);
				$joke['Tags'][$id] = $tag;
			}
		}

		return $jokes;
	}

	private function getJokeTags($qry)
	{
		return $this->db->table('Tags')
			->columns('j.JokeID', 'JokeTags.TagID', 'TagName', 'IsPrimary')
			->join('JokeTags', 'TagID', 'TagID')
			->innerJoinSubquery($qry, 'j', 'JokeID', 'JokeID', 'JokeTags')
			->findAll();
	}

	private function getRipRippers($ripQuery)
	{
		return $this->db->table('Rippers')
			->columns('r.RipID, RipRippers.RipperID', 'RipperName', 'Alias')
			->join('RipRippers', 'RipperID', 'RipperID')
			->innerJoinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipRippers')
			->findAll();
	}

	private function getRipPlatforms($gameID): array
	{
		$gameRows = $this->db->table('vw_GamesDetailed')
			->eq('GameID', $gameID)
			->findAll();

		$gameData = [];
		foreach ($gameRows as $row) {
			if (!empty($row['PlatformID'])) {
				$gameData[$row['PlatformID']] = $row['PlatformName'];
			}
		}
		return $gameData;
	}

	private function getRipComposers($ripQuery)
	{
		return $this->db->table('vw_Composers')
			->columns('g.RipID, RipComposers.ComposerID', 'ComposerName')
			->join('RipComposers', 'ComposerID', 'ComposerID')
			->innerJoinSubquery($ripQuery, 'g', 'RipID', 'RipID', 'RipComposers')
			->findAll();
	}

	/**
	 * Returns a resultset of rips based on the given search criteria.
	 * @param int $count The number of rips to retrieve
	 * @param int $offset How many records to offset the resultset by.
	 * @param ?array $sort An array of columns to sort by and their direction. E.g. 'RipName-A' for RipName ASC, or 'RipName-D' for sorting results by rip name descending.  
	 * The order the columns are given in determines the order the sort is applied.
	 * @param ?string $name A string to query the rip by its name. The RipName or RipAlternateName fields use this string. The column is toggled by $useAltName.
	 * @param ?array $tags An array of tag IDs to query rips by.
	 * @param bool $useAltName If true and $name is given, it will find rips based on their alternate name. Defaults to the RipName column.
	 * @return array An array of rips found by the given search criteria.
	 */
	public function search(int $count, int $offset, ?array $sort, ?string $name = null, array $tags = [], array $jokes = [], array $games = [], array $rippers = [], array $genres = [], array $metaJokes = [], array $metas = [], ?int $channel = null, bool $useAltName = false): array
	{
		$qry = $this->generateRipQuery(self::VIEW, $name, $tags, $jokes, $games, $rippers, $genres, $metaJokes, $metas, $channel, $useAltName);
		$qry->groupBy('RipID')
			->limit($count)
			->offset($offset);

		// Prepare sorting
		if (!empty($sort)) {
			foreach ($sort as $colAlias) {
				// Split the column name to get the alias and the sort direction.
				// Column is index 0, sort direction is index 1.
				$split = explode('-', $colAlias);
				if (!empty($split[1]) ?? null) {
					switch ($split[0]) {
						case self::SORT_RipName:
							$this->quickSort($qry, 'RipName', $split[1]);
							break;
						case self::SORT_RipAlternateName:
							$this->quickSort($qry, 'RipAlternateName', $split[1]);
							break;
						case self::SORT_RipLength:
							$this->quickSort($qry, 'RipLength', $split[1]);
							break;
						case self::SORT_RipDate:
							$this->quickSort($qry, 'RipDate', $split[1]);
							break;
					}
				}
			}
		}
		$rips = $qry->findAll();

		$rips = $this->setSubArrayValueToKey($rips, 'RipID', false);

		// Get jokes and rips from the resultset of rips.
		$ripJokes = $this->getRipJokes($qry);
		$rippers = $this->getRipRippers($qry);

		// Apply jokes to rips
		foreach ($ripJokes as $joke) {
			$ripId = $joke['RipID'];

			if (!isset($rips[$ripId]['Jokes'])) {
				$rips[$ripId]['Jokes'] = [];
				$rips[$ripId]['Genres'] = [];
			}

			$rips[$ripId]['Jokes'][$joke['JokeID']] = $joke;
			if (!empty($joke['GenreID'])) {
				$rips[$ripId]['Genres'][$joke['GenreID']] = ["GenreID" => $joke['GenreID'], "GenreName" => $joke['GenreName']];
			}
		}

		// Apply rippers to rips
		foreach ($rippers as $ripper) {
			$ripId = $ripper['RipID'];

			if (!isset($rips[$ripId]['Rippers'])) {
				$rips[$ripId]['Rippers'] = [];
			}

			$rips[$ripId]['Rippers'][$ripper['RipperID']] = $ripper;
		}

		// Apply genres to rips
		// foreach ($genres as $genre) {
		// 	$ripId = $genre['RipID'];

		// 	if (!isset($rips[$ripId]['Genres'])) {
		// 		$rips[$ripId]['Genres'] = [];
		// 	}

		// 	$rips[$ripId]['Genres'][$genre['GenreID']] = $genre;
		// }

		return $rips;
	}

	public function getCount(?string $name = null, array $tags = [], array $jokes = [], array $games = [], array $rippers = [], array $genres = [], array $metaJokes = [], array $metas = [], ?int $channel = null, bool $useAltName = false): int
	{
		$qry = $this->generateRipQuery(self::VIEW, $name, $tags, $jokes, $games, $rippers, $genres, $metaJokes, $metas, $channel, $useAltName)
			->columns('RipID')
			->groupBy('RipID');

		$count = $this->db->execute('SELECT COUNT(*) cnt FROM (' . $qry->buildSelectQuery() . ')a', $qry->getValues())
			->fetch(\PDO::FETCH_ASSOC);

		return $count['cnt'] ?? 0;
	}

	public function getRandomRip()
	{
		return $this->db->execute("SELECT RipID FROM Rips ORDER BY RAND() LIMIT 1")->fetch()['RipID'];
	}

	/**
	 * 
	 */
	private function generateRipQuery(string $table, ?string $name = null, array $tags = [], array $jokes = [], array $games = [], array $rippers = [], array $genres = [], array $metaJokes = [], array $metas = [], ?int $channel = null, bool $useAltName = false)
	{
		$qry = $this->db->table($table)
			->columns(...self::COLUMNS);

		// Apply name search if name is given.
		if (!empty($name)) {
			if ($useAltName) {
				$qry->ilike('RipAlternateName', "%$name%");
			} else {
				$qry->beginOr()
					->ilike('FullRipName', "%$name%")
					->ilike('GameName', "%$name%")
					->closeOr();
			}
		}

		// Apply tag search if tags are given.
		if (!empty($tags)) {
			foreach ($tags as $tag) {
				$qry->eq('TagID', $tag);
			}
		}

		// Apply joke search if jokes are given.
		if (!empty($jokes)) {
			foreach ($jokes as $joke) {
				$qry->eq('JokeID', $joke);
			}
		}

		// Apply game search if games are given.
		if (!empty($games)) {
			$qry->beginOr();
			foreach ($games as $game) {
				$qry->eq('RipGame', $game);
			}
			$qry->closeOr();
		}

		// Apply game search if rippers are given.
		if (!empty($rippers)) {
			foreach ($rippers as $ripper) {
				$qry->eq('RipperID', $ripper);
			}
		}

		// Apply genre search if genres are given.
		if (!empty($genres)) {
			foreach ($genres as $genre) {
				$qry->eq('GenreID', $genre);
			}
		}

		// Apply meta joke search if genres are given.
		if (!empty($metaJokes)) {
			foreach ($metaJokes as $metaJoke) {
				$qry->eq('MetaJokeID', $metaJoke);
			}
		}

		// Apply meta search if genres are given.
		if (!empty($metas)) {
			foreach ($metas as $meta) {
				$qry->eq('MetaID', $meta);
			}
		}

		// Apply channel filter if a channel is given.
		if (!empty($channel)) {
			$qry->eq('RipChannel', $channel);
		}

		return $qry;
	}

	/**
	 * Finds a game or games that match the given string.
	 * First attempts to find an exact match, if none was found, finds all games that contain the given text.
	 */
	public function findGamesByName(string $name): array
	{
		$results = [];
		$results = $this->db->table("Games")
			->columns('GameID ID', 'GameName Name')
			->ilike('GameName', "$name")
			->findOne();

		if (empty($results)) {
			$results = $this->db->table("Games")
				->columns('GameID ID', 'GameName Name')
				->ilike('GameName', "%$name%")
				->findAll();
		}

		return $results;
	}

	/**
	 * Finds all jokes that contain or match the given strings.
	 */
	public function findJokesByName(array $names): array
	{
		$qry = $this->db->table('Jokes')
			->columns('JokeID ID', 'JokeName Name')
			->beginOr();

		// Lowercase all searched joke names.
		foreach ($names as $key => &$name) {
			$name = strtolower(trim($name));
			if (!empty($name)) {
				$qry->ilike('JokeName', "%$name%");
			} else {
				unset($names[$key]);
			}
		}

		return $this->restructureAsyncQueryResults($qry, $names);
	}

	/**
	 * Finds composers with the given names.
	 */
	public function findComposersByName(array $names)
	{
		$qry = $this->db->table('vw_Composers')
			->columns('ComposerID ID', 'ComposerName Name');

		// Lowercase all searched composer names.
		$qry->beginOr();
		foreach ($names as $key => &$name) {
			$name = strtolower(trim($name));
			if (!empty($name)) {
				$qry->ilike('ComposerName', "%$name%");
				$qry->ilike('AltName', "%$name%");
			} else {
				unset($names[$key]);
			}
		}

		return $this->restructureAsyncQueryResults($qry, $names);
	}

	/**
	 * Finds rippers with the given names.
	 */
	public function findRippersByName(array $names)
	{
		$qry = $this->db->table('Rippers')
			->columns('RipperID ID', 'RipperName Name')
			->beginOr();

		// Lowercase all searched ripper names.
		foreach ($names as $key => &$name) {
			$name = strtolower(trim($name));
			if (!empty($name)) {
				$qry->ilike('RipperName', "%$name%");
			} else {
				unset($names[$key]);
			}
		}

		return $this->restructureAsyncQueryResults($qry, $names);
	}

	/**
	 * Returns up to 250 genres for the client to parse against.
	 */
	public function getGenresForImport()
	{
		// A 250 genre limit is set to prevent large amounts of data being sent to the user. There will likely never be close to 250 genres (let alone 50), so this is just a precaution.
		$genres = $this->db->table('Genres')
			->columns('GenreID ID', 'GenreName Name')
			->limit(250)
			->findAll();

		return $this->resultsetToKeyPair($genres, 'Name', 'ID');
	}

	private function restructureAsyncQueryResults(\PicoDB\Table $qry, array $names): array
	{
		$results = [];
		// If all the names are empty, do not query.
		if (!empty($names)) {
			$matches = $qry->closeOr()->findAll();

			$results = array_combine($names, array_fill(0, count($names), []));

			foreach ($matches as $row) {
				// Exact match
				if (($idx = array_search(trim(strtolower($row['Name'])), $names)) !== false) {
					// Replace the searched name with the actual name name (not lowercased)
					unset($results[$names[$idx]]);
					$results[$row['Name']] = $row['ID'];
					unset($names[$idx]);
				} else {
					foreach ($names as $name) {
						if (str_contains(strtolower($row['Name']), $name)) {
							array_push($results[$name], $row);
						}
					}
				}
			}
		}
		return $results;
	}
}
