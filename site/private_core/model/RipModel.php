<?php

namespace RipDB\Model;

require_once('Model.php');

class RipModel extends Model
{
	const TABLE = 'Rips';
	const VIEW = 'vw_RipsDetailed';
	const COLUMNS = ['RipID', 'RipName', 'RipAlternateName', 'RipDescription', 'RipDate', 'RipURL', 'RipAlternateURL', 'RipYouTubeID', 'RipLength', 'RipGame', 'GameName', 'RipChannel', 'ChannelName', 'ChannelURL'];
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
		$qry = $this->db->table(self::TABLE)
			->columns(...self::COLUMNS)
			->eq('RipID', $id)
			->join('Games', 'GameID', 'RipGame')
			->join('Channels', 'ChannelID', 'RipChannel');

		// Get jokes and rips from the resultset of rips.
		$ripJokes = $this->getRipJokes($qry);
		$rippers = $this->getRipRippers($qry);
		$genres = $this->getRipGenres($qry);
		$rip = $qry->findOne();

		// Apply jokes to rips
		foreach ($ripJokes as $joke) {
			if (!isset($rip['Jokes'])) {
				$rip['Jokes'] = [];
			}

			$rip['Jokes'][$joke['JokeID']] = $joke;
		}

		// Apply rippers to rips
		foreach ($rippers as $ripper) {
			if (!isset($rip['Rippers'])) {
				$rip['Rippers'] = [];
			}

			$rip['Rippers'][$ripper['RipperID']] = $ripper;
		}

		// Apply genres to rips
		foreach ($genres as $genre) {
			if (!isset($rip['Genres'])) {
				$rip['Genres'] = [];
			}

			$rip['Genres'][$genre['GenreID']] = $genre;
		}
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
		$values = $this->db->table('Channels');
		if ($idOnly) {
			$values = $this->setSubArrayValueToKey($values->columns('ChannelID')->findAll(), 'ChannelID');
			return array_keys($values);
		} else {
			return $values->columns('ChannelID', 'ChannelName')->findAll();
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

	/**
	 * Finds all jokes that are contained in the given resultset of rips.
	 * @return A 2D array where each key is the ID of the joke and the values are the columns from the Jokes Table, along with its tags.
	 */
	private function getRipJokes($ripQuery)
	{
		$qry = $this->db->table('Jokes')
			->columns('r.RipID, RipJokes.JokeID', 'JokeName', 'JokeTimestamps', 'JokeComment')
			->join('RipJokes', 'JokeID', 'JokeID')
			->innerJoinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipJokes');

		$tags = $this->getJokeTags($qry);
		$jokes = $qry->findAll();

		$tags = $this->setSubArrayValueToKey2D($tags, 'JokeID');

		foreach ($jokes as &$joke) {
			$joke['Tags'] = [];
			foreach ($tags[$joke['JokeID']] as $tag) {
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

	private function getRipGenres($ripQuery)
	{
		return $this->db->table('Genres')
			->columns('g.RipID, RipGenres.GenreID', 'GenreName')
			->join('RipGenres', 'GenreID', 'GenreID')
			->innerJoinSubquery($ripQuery, 'g', 'RipID', 'RipID', 'RipGenres')
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
	public function searchRips(int $count, int $offset, ?array $sort, ?string $name = null, array $tags = [], array $jokes = [], array $games = [], array $rippers = [], array $genres = [], array $metaJokes = [], array $metas = [], ?int $channel = null, bool $useAltName = false)
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
		$genres = $this->getRipGenres($qry);

		// Apply jokes to rips
		foreach ($ripJokes as $joke) {
			$ripId = $joke['RipID'];

			if (!isset($rips[$ripId]['Jokes'])) {
				$rips[$ripId]['Jokes'] = [];
			}

			$rips[$ripId]['Jokes'][$joke['JokeID']] = $joke;
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
		foreach ($genres as $genre) {
			$ripId = $genre['RipID'];

			if (!isset($rips[$ripId]['Genres'])) {
				$rips[$ripId]['Genres'] = [];
			}

			$rips[$ripId]['Genres'][$genre['GenreID']] = $genre;
		}

		return $rips;
	}

	public function getRipCount(?string $name = null, array $tags = [], array $jokes = [], array $games = [], array $rippers = [], array $genres = [], array $metaJokes = [], array $metas = [], ?int $channel = null, bool $useAltName = false)
	{
		$where = '';
		$params = [];

		// Apply name filter if name is given.
		if (!empty($name)) {
			if ($useAltName) {
				$where .= ' AND RipAlternateName = ?';
			} else {
				$where .= ' AND RipName = ?';
			}
			array_push($params, $name);
		}

		// Apply tag filter if tags are given.
		if (!empty($tags)) {
			$temp = array_fill(0, count($tags), '?');
			$where .= ' AND (`TagID` = ' . implode(' AND `TagID` = ', $temp) . ')';
			array_push($params, ...$tags);
		}

		// Apply joke filter if jokes are given.
		if (!empty($jokes)) {
			$temp = array_fill(0, count($jokes), '?');
			$where .= ' AND (`JokeID` = ' . implode(' AND `JokeID` = ', $temp) . ')';
			array_push($params, ...$jokes);
		}

		// Apply game filter if games are given.
		if (!empty($games)) {
			$temp = array_fill(0, count($games), '?');
			$where .= ' AND (`RipGame` = ' . implode(' OR `RipGame` = ', $temp) . ')';
			array_push($params, ...$games);
		}

		// Apply game filter if rippers are given.
		if (!empty($rippers)) {
			$temp = array_fill(0, count($rippers), '?');
			$where .= ' AND (`RipperID` = ' . implode(' OR `RipperID` = ', $temp) . ')';
			array_push($params, ...$rippers);
		}

		// Apply genre filter if genres are given.
		if (!empty($genres)) {
			$temp = array_fill(0, count($genres), '?');
			$where .= ' AND (`GenreID` = ' . implode(' OR `GenreID` = ', $temp) . ')';
			array_push($params, ...$genres);
		}

		// Apply meta joke filter if genres are given.
		if (!empty($metaJokes)) {
			$temp = array_fill(0, count($metaJokes), '?');
			$where .= ' AND (`MetaJokeID` = ' . implode(' OR `MetaJokeID` = ', $temp) . ')';
			array_push($params, ...$metaJokes);
		}

		// Apply meta filter if genres are given.
		if (!empty($metas)) {
			$temp = array_fill(0, count($metas), '?');
			$where .= ' AND (`MetaID` = ' . implode(' OR `MetaID` = ', $temp) . ')';
			array_push($params, ...$metas);
		}

		// Apply channel filter if a channel is given.
		if (!empty($channel)) {
			$where .= ' AND (`RipChannel` = ?)';
			array_push($params, $channel);
		}

		$qry = "SELECT COUNT(*) AS `count` FROM (SELECT RipID FROM `vw_RipsDetailed` ";
		if (!empty($where)) {
			// remove the leading AND
			$where = substr($where, 4);
			$qry .= "WHERE $where";
		}
		$qry .= ' GROUP BY RipID) a';

		$result = $this->db->execute($qry, $params)->fetch();
		return $result['count'];
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
			$qry->ilike($useAltName ? 'RipAlternateName' : 'RipName', "%$name%");
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
}
