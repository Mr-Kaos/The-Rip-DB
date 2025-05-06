<?php

namespace RipDB;

require('Model.php');

class JokeModel extends Model
{
	const TABLE = 'Jokes';
	const VIEW = 'vw_JokesDetailed';
	const COLUMNS = ['JokeID', 'JokeName', 'JokeDescription'];

	public function searchJokes(int $count, int $offset, ?string $name = null, array $tags = [], array $metas = [])
	{
		$qry = $this->db->table(self::VIEW)
			->columns(...self::COLUMNS)
			->asc('JokeID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('JokeName', "%$name%");
		}

		// Apply tag search if tags are given.
		if (!empty($tags)) {
			foreach ($tags as $tag) {
				$qry->eq('TagID', $tag);
			}
		}

		// Apply tag search if tags are given.
		if (!empty($metas)) {
			foreach ($metas as $meta) {
				$qry->eq('MetaJokeID', $meta);
			}
		}

		$qry->groupBy('JokeID')
			->limit($count)
			->offset($offset);
		$jokes = $qry->findAll();
		$jokes = $this->setSubArrayValueToKey($jokes, 'JokeID', false);


		// Get tags and metas from the resultset of rips.
		$tags = $this->getJokeTags($qry);
		// $metas = $this->getJokeMetas($qry);
		$counts = $this->getJokeRipCount($qry);
		foreach ($jokes as &$joke) {
			$joke['Tags'] = [];
			foreach ($tags[$joke['JokeID']] as $tag) {
				$joke['Tags'][$tag['TagID']] = ['TagName' => $tag['TagName'], 'IsPrimary' => $tag['IsPrimary']];
			}

			$joke['RipCount'] = $counts[$joke['JokeID']]['RipCount'];
		}

		return $jokes;
	}

	public function getJokeCount()
	{
		return $this->db->table(self::TABLE)->count();
	}

	private function getJokeTags($qry)
	{
		$tags = $this->db->table('Tags')
			->columns('j.JokeID', 'JokeTags.TagID', 'TagName', 'IsPrimary')
			->join('JokeTags', 'TagID', 'TagID')
			->joinSubquery($qry, 'j', 'JokeID', 'JokeID', 'JokeTags')
			->desc('IsPrimary')
			->findAll();

		return $this->setSubArrayValueToKey2D($tags, 'JokeID');
	}

	private function getJokeRipCount($qry) {
		$counts = $this->db->table('Jokes')
			->select('r.JokeID, COUNT(RipID) AS RipCount')
			->join('RipJokes', 'JokeID', 'JokeID')
			->joinSubquery($qry, 'r', 'JokeID', 'JokeID')
			->groupBy('r.JokeID')
			->findAll();

		return $this->setSubArrayValueToKey($counts, 'JokeID');
	}
}
