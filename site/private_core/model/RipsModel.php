<?php

namespace RipDB;

require('Model.php');

class RipsModel extends Model
{
	const TABLE = 'Rips';

	public function getRip(int $id)
	{
		return $this->db->table(self::TABLE)
			->eq('RipID', $id);
	}

	public function getRips(int $offset = 0, int $count = 25) {
		
	}

	public function getRipsByName(string $name) {
		return $this->db->table(self::TABLE)
			->eq('RipName', $name)->findAll();
	}
}
