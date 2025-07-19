<?php

namespace RipDB\Model;

require('Model.php');

class MetaModel extends Model implements ResultsetSearch
{
	const TABLE = 'Metas';
	const COLUMNS = ['MetaID', 'MetaName', 'MetaDescription'];
	const VIEW = 'vw_MetasDetailed';
	const VIEW_COLUMNS = ['MetaID', 'MetaName', 'MetaDescription', 'AssociatedRips'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null, array $metas = []): array
	{
		$qry = $this->db->table(self::VIEW)
			->select("MetaID, MetaName, MetaDescription, SUM(AssociatedRips) AssociatedRips");

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('MetaName', "%$name%");
		}

		// Apply meta search if metas are given.
		if (!empty($metas)) {
			foreach ($metas as $meta) {
				$qry->eq('MetaID', $meta);
			}
		}

		$qry->groupBy('MetaID')
			->limit($count)
			->offset($offset);
		$meta = $qry->findAll();
		$meta = $this->setSubArrayValueToKey($meta, 'MetaID', false);

		// Get meta jokes from the resultset of metas.
		$associatedData = $this->db->table(self::VIEW)
			->columns('MetaJokeID', 'MetaJokeName', ...self::VIEW_COLUMNS)
			->asc('MetaID')
			->notNull('MetaJokeID')->findAll();
		$associatedData = $this->setSubArrayValueToKey2D($associatedData, 'MetaID');

		foreach ($meta as $id => &$metaJoke) {
			$this->groupJokeAssociateData($metaJoke, $associatedData[$id] ?? null);
		}

		return $meta;
	}

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}

	public function getMeta(int $id): array
	{
		$joke = $this->db->table(self::VIEW)->columns('MetaName', ...self::COLUMNS)->eq('MetaID', $id)->findOne();
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
		$metaJoke['MetaJokes'] = [];

		// Associate jokes to each meta joke.
		if (!empty($associatedData)) {
			foreach ($associatedData as $jokes) {
				// Add Jokes
				if ($jokes['MetaJokeID'] !== null) {
					$metaJoke['MetaJokes'][$jokes['MetaJokeID']] = [
						'MetaJokeName' => $jokes['MetaJokeName'],
					];
				}
			}
		}
	}
}
