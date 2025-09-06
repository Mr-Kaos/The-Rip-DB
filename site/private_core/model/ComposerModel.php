<?php

namespace RipDB\Model;

require('Model.php');

class ComposerModel extends Model implements ResultsetSearch
{
	const TABLE = 'Composers';
	const COLUMNS = ['ComposerID', 'ComposerFirstName', 'ComposerLastName', 'ComposerFirstNameAlt', 'ComposerLastNameAlt'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null): array
	{
		$qry = $this->db->table(self::TABLE);
			// ->asc('ComposerID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->beginOr()
				->ilike('ComposerFirstName', "%$name%")
				->ilike('ComposerLastName', "%$name%")
				->ilike('ComposerFirstNameAlt', "%$name%")
				->ilike('ComposerLastNameAlt', "%$name%")
				->closeOr();
		}

		$qry->join('RipComposers', 'ComposerID', 'ComposerID', 'Composers')
			->select('Composers.' . implode(', Composers.', self::COLUMNS) . ', COUNT(RipID) RipCount')
			->groupBy('Composers.ComposerID');

		$qry->limit($count)
			->offset($offset);
		$composers = $qry->findAll();


		return $composers;
	}

	public function getCount(?string $name = null): int
	{
		$qry = $this->db->table(self::TABLE);

		if (!empty($name)) {
			$qry->beginOr()
				->ilike('ComposerFirstName', "%$name%")
				->ilike('ComposerLastName', "%$name%")
				->ilike('ComposerFirstNameAlt', "%$name%")
				->ilike('ComposerLastNameAlt', "%$name%")
				->closeOr();
		}

		return $qry->count();
	}

	/**
	 * Fetches the composer record with the given ID.
	 */
	public function getComposer(int $id): ?array
	{
		return $this->db->table(self::TABLE)->eq('ComposerID', $id)->findOne();
	}

	private function getComposerRipCount($qry)
	{
		$counts = $this->db->table('RipComposers')
			->select('COUNT(RipID) AS RipCount')
			->joinSubquery($qry, 't', 'ComposerID', 'ComposerID')
			->groupBy('t.ComposerID')
			->findAll();

		var_dump($counts);

		return $this->setSubArrayValueToKey($counts, 'ComposerID');
	}
}
