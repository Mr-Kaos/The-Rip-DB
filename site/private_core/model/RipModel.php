<?php

namespace RipDB\Model;

require_once('Model.php');

class RipModel extends Model
{
	const TABLE = 'Rips';
	const VIEW = 'vw_RipsDetailed';
	const COLUMNS = ['RipID', 'RipName', 'RipAlternateName', 'RipDescription', 'RipDate', 'RipURL', 'RipLength'];

	/**
	 * Gets data about a specific rip.
	 */
	public function getRip(int $id)
	{
		$qry = $this->db->table(self::TABLE)
			->columns('RipID', 'RipName', 'RipDate', 'RipAlternateName', 'RipLength', 'RipURL', 'RipDescription', 'RipGame', 'GameName', 'RipChannel', 'ChannelName', 'ChannelURL')
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

	public function getRippers(bool $idOnly = false) {
		$values = $this->db->table('Rippers');
		if ($idOnly) {
			$values = $this->setSubArrayValueToKey($values->columns('RipperID')->findAll(), 'RipperID');
			return array_keys($values);
		} else {
			return $values->columns('RipperID', 'RipperName')->findAll();
		}
	}

	public function getChannels(bool $idOnly = false) {
		$values = $this->db->table('Channels');
		if ($idOnly) {
			$values = $this->setSubArrayValueToKey($values->columns('ChannelID')->findAll(), 'ChannelID');
			return array_keys($values);
		} else {
			return $values->columns('ChannelID', 'ChannelName')->findAll();
		}
	}

	public function getGames(bool $idOnly = false) {
		$values = $this->db->table('Games');
		if ($idOnly) {
			return $values->findAllByColumn('GameID');
		} else {
			return $values->columns('GameID', 'GameName')->findAll();
		}
	}

	public function getJokes(bool $idOnly = false) {
		$values = $this->db->table('Jokes');
		if ($idOnly) {
			return $values->findAllByColumn('JokeID');
		} else {
			return $values->columns('JokeID', 'JokeName')->findAll();
		}
	}

	public function getGenres(bool $idOnly = false) {
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
	 * @param ?string $name A string to query the rip by its name. The RipName or RipAlternateName fields use this string. The column is toggled by $useAltName.
	 * @param ?array $tags An array of tag IDs to query rips by.
	 * @param bool $useAltName If true and $name is given, it will find rips based on their alternate name. Defaults to the RipName column.
	 * @return array An array of rips found by the given search criteria.
	 */
	public function searchRips(int $count, int $offset, ?string $name = null, array $tags = [], array $jokes = [], array $games = [], array $rippers, array $genres = [], bool $useAltName = false)
	{
		$qry = $this->generateRipQuery(self::VIEW, $name, $tags, $jokes, $games, $rippers, $genres, $useAltName);
		$qry->groupBy('RipID')
			->limit($count)
			->offset($offset);
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

	public function getRipCount(?string $name = null, array $tags = [], array $jokes = [], array $games = [], bool $useAltName = false)
	{
		return $this->generateRipQuery(self::TABLE, $name, [], [], [], [], [], $useAltName)->count();
	}

	/**
	 * 
	 */
	private function generateRipQuery(string $table, ?string $name = null, array $tags = [], array $jokes = [], array $games = [], array $rippers = [], array $genres = [], bool $useAltName = false)
	{
		$qry = $this->db->table($table)
			->columns(...self::COLUMNS)
			->asc('RipID');

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

		return $qry;
	}

	/**
	 * Gets a resultset of the specified records from the specified table.
	 * @param string $source The name of the table to retrieve records from. Be sure to omit the "s".
	 */
	public function getFilterResults(string $source, array $ids) {
		$table = $source . 's';
		$result = [];
		if (!empty($ids)) {
			$result = $this->db->table($table)->in($source . 'ID', $ids)->findAll();
			$result = $this->resultsetToKeyPair($result, $source . 'ID', $source . 'Name');
		}
		return $result;
	}
}
