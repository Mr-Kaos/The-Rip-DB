<?php

namespace RipDB\Model;

require('Model.php');

class TagModel extends Model
{
	const TABLE = 'Tags';
	// const VIEW = 'vw_JokesDetailed';
	const COLUMNS = ['TagID', 'TagName', 'MetaOnly'];

	public function searchTags(int $count, int $offset, ?string $name = null, ?bool $metaOnly = null, array $metas = [])
	{
		$qry = $this->db->table(self::TABLE)
			->columns(...self::COLUMNS)
			->asc('TagID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('TagName', "%$name%");
		}

		// Filter to meta only if specified
		if (is_bool($metaOnly)) {
			$qry->eq('MetaOnly', $metaOnly);
		}

		$qry->limit($count)
			->offset($offset);
		$tags = $qry->findAll();
		$tags = $this->setSubArrayValueToKey($tags, 'TagID', false);


		
		$jokeCount = $this->getTagJokeCount($qry);
		$ripCount = $this->getTagRipCount($qry);
		foreach ($tags as &$tag) {
			$tag['JokeCount'] = $jokeCount[$tag['TagID']]['TagCount'];
			$tag['RipCount'] = $ripCount[$tag['TagID']]['RipCount'];
		}

		return $tags;
	}

	public function getTagCount()
	{
		return $this->db->table(self::TABLE)->count();
	}

	private function getTagJokeCount($qry) {
		$counts = $this->db->table('Tags')
			->select('t.TagID, COUNT(JokeID) AS TagCount')
			->join('JokeTags', 'TagID', 'TagID')
			->joinSubquery($qry, 't', 'TagID', 'TagID')
			->groupBy('t.TagID')
			->findAll();

		return $this->setSubArrayValueToKey($counts, 'TagID');
	}

	private function getTagRipCount($qry) {
		$counts = $this->db->table('Tags')
			->select('t.TagID, COUNT(RipID) AS RipCount')
			->join('JokeTags', 'TagID', 'TagID')
			->join('RipJokes', 'JokeID', 'JokeID', 'JokeTags')
			->joinSubquery($qry, 't', 'TagID', 'TagID')
			->groupBy('t.TagID')
			->findAll();

		return $this->setSubArrayValueToKey($counts, 'TagID');
	}
}
