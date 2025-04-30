<?php

namespace RipDB;

require('Model.php');

class RipsModel extends Model
{
	const TABLE = 'Rips';
	const VIEW = 'vw_RipsDetailed';

	const COLUMNS = ['RipID', 'RipName', 'RipAlternateName', 'RipDescription', 'RipDate', 'RipURL', 'RipLength', 'JokeName'];

	public function getRips(int $count, int $offset, bool $useAltName = false)
	{
		return $this->db->table(self::VIEW)
			->columns(...self::COLUMNS)
			->groupBy('RipID')
			->limit($count)
			->offset($offset)
			->findAll();
	}

	public function getRipsByName(string $name, int $count, int $offset, bool $useAltName = false)
	{
		$col = $useAltName ? 'RipAlternateName' : 'RipName';

		return $this->db->table(self::VIEW)
			->ilike($col, "%$name%")
			->columns(...self::COLUMNS)
			->groupBy('RipID')
			->limit($count)
			->offset($offset)
			->findAll();
	}

	public function getRipCount(bool $useAltName = false)
	{
		if ($useAltName) {
			return $this->db->table(self::TABLE)->notNull('RipAlternateName')->count();
		} else {
			return $this->db->table(self::TABLE)->count();
		}
	}

	public function getSearchTags()
	{
		return $this->db->table('Tags')->findAll();
	}

	public function getSearchJokes()
	{
		return $this->db->table('Jokes')->columns('JokeID', 'JokeName')->findAll();
	}
}
