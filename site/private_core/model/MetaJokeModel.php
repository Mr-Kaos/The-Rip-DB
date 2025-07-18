<?php

namespace RipDB\Model;

require('Model.php');

class MetaJokeModel extends Model implements ResultsetSearch
{
	const TABLE = 'MetaJokes';
	const COLUMNS = ['MetaJokeID', 'MetaJokeName', 'MetaJokeDescription', 'MetaID'];
	const VIEW = 'vw_MetaJokesDetailed';
	const VIEW_COLUMNS = ['MetaJokeID', 'MetaJokeName', 'MetaJokeDescription', 'MetaID', 'MetaName', 'AssociatedJokes'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null, array $metas = []): array
	{
		$qry = $this->db->table(self::VIEW)
			->columns(...self::VIEW_COLUMNS);

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('MetaJokeName', "%$name%");
		}

		// Apply meta search if metas are given.
		if (!empty($metas)) {
			foreach ($metas as $meta) {
				$qry->eq('MetaID', $meta);
			}
		}

		$qry->groupBy('MetaJokeID')
			->limit($count)
			->offset($offset);
		$metaJokes = $qry->findAll();
		$metaJokes = $this->setSubArrayValueToKey($metaJokes, 'MetaJokeID', false);

		// Get tags and metas from the resultset of rips.
		$associatedData = $this->db->table(self::VIEW)
			->columns('JokeID', 'JokeName', ...self::VIEW_COLUMNS)
			->asc('MetaJokeID')
			->notNull('JokeID')->findAll();
		$associatedData = $this->setSubArrayValueToKey2D($associatedData, 'MetaJokeID');

		foreach ($metaJokes as $id => &$metaJoke) {
			$this->groupJokeAssociateData($metaJoke, $associatedData[$id] ?? null);
		}

		return $metaJokes;
	}

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}

	public function getMetaJoke(int $id): array
	{
		$joke = $this->db->table(self::VIEW)->columns('MetaName', ...self::COLUMNS)->eq('MetaJokeID', $id)->findOne();
		$joke['Meta'] = [$joke['MetaID'] => $joke['MetaName']];
		return $joke;
	}

	/**
	 * Checks if the given meta exists. Used in validating metas.
	 */
	public function getMEtas(): array {
		return $this->db->table('Metas')->findAllByColumn('MetaID');
	}

	/**
	 * Associates tags, meta jokes and metas to the given joke record.
	 * 
	 * The joke record should be retrieved from vw_JokesDetailed or the Jokes table.  
	 * Passing the same joke through here multiple times will clear the previous associations made.
	 * 
	 * @param array &$joke A reference to the joke record to be associated with the additional data.
	 * @param ?array $associatedData An array of rows from vw_JokesDetailed that are associated to the given joke.
	 */
	private function groupJokeAssociateData(array &$metaJoke, ?array $associatedData): void
	{
		$metaJoke['Jokes'] = [];

		// Associate jokes to each meta joke.
		if (!empty($associatedData)) {
			foreach ($associatedData as $jokes) {
				// Add Jokes
				if ($jokes['JokeID'] !== null) {
					$metaJoke['Jokes'][$jokes['JokeID']] = [
						'JokeName' => $jokes['JokeName'],
					];
				}
			}
		}
	}
}
