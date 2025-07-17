<?php

namespace RipDB\Model;

require('Model.php');

class JokeModel extends Model implements ResultsetSearch
{
	const TABLE = 'Jokes';
	const COLUMNS = ['JokeID', 'JokeName', 'JokeDescription'];
	const VIEW = 'vw_JokesDetailed';
	const VIEW_COLUMNS = ['JokeID', 'TagID', 'TagName', 'IsPrimary', 'MetaJokeID', 'MetaJokeName', 'MetaID', 'MetaName'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null, array $tags = [], array $metas = [], array $metaJokes = []): array
	{
		$qry = $this->db->table(self::VIEW)
			->columns('RipCount', ...self::COLUMNS)
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

		// Apply meta search if metas are given.
		if (!empty($metas)) {
			foreach ($metas as $meta) {
				$qry->eq('MetaID', $meta);
			}
		}

		// Apply meta joke search if meta jokes are given.
		if (!empty($metaJokes)) {
			foreach ($metaJokes as $meta) {
				$qry->eq('MetaJokeID', $meta);
			}
		}

		$qry->groupBy('JokeID')
			->limit($count)
			->offset($offset);
		$jokes = $qry->findAll();
		$jokes = $this->setSubArrayValueToKey($jokes, 'JokeID', false);

		// Get tags and metas from the resultset of rips.
		$associatedData = $this->db->table(self::VIEW)
			->columns(...self::VIEW_COLUMNS)
			->asc('JokeID')->findAll();
		$associatedData = $this->setSubArrayValueToKey2D($associatedData, 'JokeID');

		foreach ($jokes as $jokeId => &$joke) {
			$this->groupJokeAssociateData($joke, $associatedData[$jokeId] ?? null);
		}

		return $jokes;
	}

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}

	public function getTags()
	{
		return $this->db->table('Tags')->findAllByColumn('TagID');
	}

	public function getMetaJokes()
	{
		return $this->db->table('MetaJokes')->findAllByColumn('MetaJokeID');
	}

	public function getJoke(int $id): array
	{
		$joke = $this->db->table(self::TABLE)->columns(...self::COLUMNS)->eq('JokeID', $id)->findOne();

		$associatedData = $this->db->table(self::VIEW)
			->columns(...self::VIEW_COLUMNS)
			->eq('JokeID', $id)
			->findAll();
		$associatedData = $this->setSubArrayValueToKey2D($associatedData, 'JokeID');

		$this->groupJokeAssociateData($joke, $associatedData[$id]);

		// Prepare the tags for use in the input elements
		$joke['OtherTags'] = [];
		foreach ($joke['Tags'] as $tag) {
			if ($tag['IsPrimary']) {
				$joke['PrimaryTagID'] = [$tag['TagID'] => $tag['TagName']];
			} else {
				array_push($joke['OtherTags'], ['Tag' => [$tag['TagID'] => $tag['TagName']]]);
			}
		}

		// Prepare the meta jokes for use in the input elements
		if (!empty($joke['MetaJokes'])) {
			$metaJokes = $joke['MetaJokes'];
			$joke['MetaJokes'] = [];
			foreach ($metaJokes as $mjID => $mj) {
				array_push($joke['MetaJokes'], ['Meta' => [$mjID => $mj['MetaJokeName']]]);
			}
		}

		return $joke;
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
	private function groupJokeAssociateData(array &$joke, ?array $associatedData): void
	{
		$joke['Tags'] = [];
		$joke['MetaJokes'] = [];

		// Associate tags, meta jokes and metas to each joke.
		if (!empty($associatedData)) {
			foreach ($associatedData as $tagMetas) {
				// Add tags
				if (!empty($tagMetas['TagID'])) {
					// Create the tags array, if it does not already exist.
					$joke['Tags'][$tagMetas['TagID']] = ['TagID' => $tagMetas['TagID'], 'TagName' => $tagMetas['TagName'], 'IsPrimary' => $tagMetas['IsPrimary']];
				}

				// Add meta Jokes
				if ($tagMetas['MetaJokeID'] !== null) {
					$joke['MetaJokes'][$tagMetas['MetaJokeID']] = [
						'MetaJokeName' => $tagMetas['MetaJokeName'],
					];

					// Add metas to meta jokes
					if (isset($joke['MetaJokes'][$tagMetas['MetaJokeID']]['Metas'])) {
						$joke['MetaJokes'][$tagMetas['MetaJokeID']]['Metas'] = [];
					}
					// There can only be one meta per meta joke, but in case this gets changed in the future, it will be stored in an associative array.
					$joke['MetaJokes'][$tagMetas['MetaJokeID']]['Metas'][$tagMetas['MetaID']] = $tagMetas['MetaName'];
				}
			}
		}
	}
}
