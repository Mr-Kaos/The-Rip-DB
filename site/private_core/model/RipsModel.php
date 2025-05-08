<?php

namespace RipDB;

require('Model.php');

class RipsModel extends Model
{
	const TABLE = 'Rips';
	const VIEW = 'vw_RipsDetailed';
	const COLUMNS = ['RipID', 'RipName', 'RipAlternateName', 'RipDescription', 'RipDate', 'RipURL', 'RipLength'];

	/**
	 * Returns a resultset of rips based on the given search criteria.
	 * @param int $count The number of rips to retrieve
	 * @param int $offset How many records to offset the resultset by.
	 * @param ?string $name A string to query the rip by its name. The RipName or RipAlternateName fields use this string. The column is toggled by $useAltName.
	 * @param ?array $tags An array of tag IDs to query rips by.
	 * @param bool $useAltName If true and $name is given, it will find rips based on their alternate name. Defaults to the RipName column.
	 * @return array An array of rips found by the given search criteria.
	 */
	public function searchRips(int $count, int $offset, ?string $name = null, array $tags = [], array $jokes = [], bool $useAltName = false)
	{
		$qry = $this->db->table(self::VIEW)
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

		$qry->groupBy('RipID')
			->limit($count)
			->offset($offset);
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

		return $rips;
	}

	public function getRipCount(bool $useAltName = false)
	{
		if ($useAltName) {
			return $this->db->table(self::TABLE)->notNull('RipAlternateName')->count();
		} else {
			return $this->db->table(self::TABLE)->count();
		}
	}

	/**
	 * Finds all jokes that are contained in the given resultset of rips.
	 * @return A 2D array where each key is the ID of the joke and the values are the columns from the Jokes Table, along with its tags.
	 */
	private function getRipJokes($ripQuery)
	{
		$qry = $this->db->table('Jokes')
			->columns('r.RipID, RipJokes.JokeID', 'JokeName')
			->join('RipJokes', 'JokeID', 'JokeID')
			->joinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipJokes');

		$tags = $this->getJokeTags($qry);
		$jokes = $qry->findAll();

		$tags = $this->setSubArrayValueToKey2D($tags, 'JokeID');

		foreach ($jokes as &$joke) {
			$joke['Tags'] = [];
			foreach ($tags[$joke['JokeID']] as $tag) {
				$joke['Tags'][$tag['TagID']] = $tag['TagName'];
			}
		}

		return $jokes;
	}

	private function getJokeTags($qry)
	{
		return $this->db->table('Tags')
			->columns('j.JokeID', 'JokeTags.TagID', 'TagName')
			->join('JokeTags', 'TagID', 'TagID')
			->joinSubquery($qry, 'j', 'JokeID', 'JokeID', 'JokeTags')
			->findAll();
	}

	private function getRipRippers($ripQuery)
	{
		$qry = $this->db->table('Rippers')
			->columns('r.RipID, RipRippers.RipperID', 'RipperName')
			->join('RipRippers', 'RipperID', 'RipperID')
			->joinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipRippers');

		return $qry->findAll();
	}

	public function getTags(array $ids): array
	{
		$result = [];
		if (!empty($ids)) {
			$result = $this->db->table('Tags')->in('TagID', $ids)->findAll();
			$result = $this->resultsetToKeyPair($result, 'TagID', 'TagName');
		}

		return $result;
	}

	public function getJokes(array $ids)
	{
		$result = [];
		if (!empty($ids)) {
			$result = $this->db->table('Jokes')->in('JokeID', $ids)->findAll();
			$result = $this->resultsetToKeyPair($result, 'JokeID', 'JokeName');
		}
		return $result;
	}

	public function getRippers()
	{
		return $this->db->table('Rippers')->columns('RipperID', 'RipperName')->findAll();
	}
}
