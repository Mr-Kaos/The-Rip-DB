<?php

namespace RipDB\Model;

require('Model.php');

class PlatformModel extends Model implements ResultsetSearch
{
	const TABLE = 'Platforms';
	const COLUMNS = ['PlatformID', 'PlatformName'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null): array
	{
		$qry = $this->db->table(self::TABLE)
			->columns(...self::COLUMNS)
			->asc('PlatformID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('PlatformName', "%$name%");
		}

		$qry->limit($count)
			->offset($offset);
		$platforms = $qry->findAll();
		$platforms = $this->setSubArrayValueToKey($platforms, 'PlatformID', false);

		return $platforms;
	}

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}

	/**
	 * Gets all platform names.
	 * Used for validating platform submission to ensure the submitted platform is not already in use.
	 */
	public function getAllPlatformNames(): array
	{
		return $this->db->table('Platforms')->findAllByColumn('PlatformName');
	}

	/**
	 * Fetches the platform record with the given ID.
	 */
	public function getPlatform(int $id): ?array
	{
		return $this->db->table(self::TABLE)->eq('PlatformID', $id)->findOne();
	}
}
