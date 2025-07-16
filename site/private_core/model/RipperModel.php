<?php

namespace RipDB\Model;

require('Model.php');

class RipperModel extends Model implements ResultsetSearch
{
	const TABLE = 'Rippers';
	const COLUMNS = ['RipperID', 'RipperName'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null): array
	{
		$qry = $this->db->table(self::TABLE)
			->select('Rippers.RipperID, RipperName, COUNT(RipID) RipCount')
			->asc('RipperID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('RipperName', "%$name%");
		}

		$qry->limit($count)
			->offset($offset)
			->join('RipRippers', 'RipperID', 'RipperID', 'Rippers')
			->groupBy('Rippers.RipperID', 'RipperName');
		$rippers = $qry->findAll();

		return $rippers;
	}

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}

	/**
	 * Retrieves a list of all ripper names that exist in the database. Used in validating new/existing rippers.
	 * @param ?int $excludeId If given, will exclude the ripper with this ID.
	 */
	public function getAllRipperNames(?int $excludeId = null): array
	{
		$qry = $this->db->table(self::TABLE);
		if ($excludeId !== null) {
			$qry = $qry->neq('RipperID', $excludeId);
		}
		return $qry->findAllByColumn('RipperName');
	}

	/**
	 * Retrieves the data for the specified ripper.
	 * @param int $id The ID of the ripper to retrieve.
	 * @return ?array An associative array containing the data of the ripper or null if the given ripper ID does not exist.
	 */
	public function getRipper(int $id): ?array
	{
		return $this->db->table(self::TABLE)->eq('RipperID', $id)->findOne();
	}
}
