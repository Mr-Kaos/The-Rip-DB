<?php

namespace RipDB;

require('Model.php');

class RipsModel extends Model
{
	const TABLE = 'Rips';
	const VIEW = 'vw_RipsDetailed';
	const COLUMNS = ['RipID', 'RipName', 'RipAlternateName', 'RipDescription', 'RipDate', 'RipURL', 'RipLength'];

	/**
	 * Default rip retrieval for use without search.
	 */
	public function getRips(int $count, int $offset)
	{
		return $this->db->table(self::VIEW)
			->columns(...self::COLUMNS)
			->groupBy('RipID')
			->limit($count)
			->offset($offset)
			->findAll();
	}

	/**
	 * Returns a resultset of rips based on the given search criteria.
	 * 
	 */
	public function searchRips(string $name, ?array $tags, int $count, int $offset, bool $useAltName = false)
	{
		$col = $useAltName ? 'RipAlternateName' : 'RipName';

		$qry = $this->db->table(self::VIEW)
			->ilike($col, "%$name%")
			->columns(...self::COLUMNS);

		if (!empty($tags)) {
			foreach ($tags as $tag) {
				$qry->eq('TagID', $tag);
			}
		}

		$qry->groupBy('RipID')
			->limit($count)
			->offset($offset);
		$rips = $qry->findAll();
		$rips = $this->setSubArrayValueToKey($rips, 'RipID', false);
		$jokes = $this->getRipJokes($qry);
		$rippers = $this->getRipRippers($qry);

		// Apply jokes to rips
		foreach ($jokes as $joke) {
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

	public function getTags()
	{
		return $this->db->table('Tags')->findAll();
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

	private function getRipRippers($ripQuery) {
		$qry = $this->db->table('Rippers')
			->columns('r.RipID, RipRippers.RipperID', 'RipperName')
			->join('RipRippers', 'RipperID', 'RipperID')
			->joinSubquery($ripQuery, 'r', 'RipID', 'RipID', 'RipRippers');

		return $qry->findAll();
	}

	public function getJokes()
	{
		return $this->db->table('Jokes')->columns('JokeID', 'JokeName')->findAll();
	}

	public function getRippers()
	{
		return $this->db->table('Rippers')->columns('RipperID', 'RipperName')->findAll();
	}
}
