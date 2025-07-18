<?php

namespace RipDB\Model;

require('Model.php');

class TagModel extends Model implements ResultsetSearch
{
	const TABLE = 'Tags';
	const COLUMNS = ['TagID', 'TagName'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null): array
	{
		$qry = $this->db->table(self::TABLE)
			->columns(...self::COLUMNS)
			->asc('TagID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('TagName', "%$name%");
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

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}

	/**
	 * Gets all tag names.
	 * Used for validating tag submission to ensure the submitted tag is not already in use.
	 */
	public function getAllTagNames(): array
	{
		return $this->db->table('Tags')->findAllByColumn('TagName');
	}

	/**
	 * Fetches the tag record with the given ID.
	 */
	public function getTag(int $id): ?array
	{
		return $this->db->table(self::TABLE)->eq('TagID', $id)->findOne();
	}

	private function getTagJokeCount($qry)
	{
		$counts = $this->db->table('Tags')
			->select('t.TagID, COUNT(JokeID) AS TagCount')
			->join('JokeTags', 'TagID', 'TagID')
			->joinSubquery($qry, 't', 'TagID', 'TagID')
			->groupBy('t.TagID')
			->findAll();

		return $this->setSubArrayValueToKey($counts, 'TagID');
	}

	private function getTagRipCount($qry)
	{
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
