<?php

namespace RipDB;

require('Model.php');

class RipsModel extends Model
{
	const TABLE = 'Rips';

	public function getRips(int $offset = 0, int $count = 25) {
		
	}

	public function getRipsByName(string $name, int $count, int $offset) {
		return $this->db->table(self::TABLE)
			->ilike('RipName', "%$name%")->limit($count)->offset($offset)->findAll();
	}

	public function getRipCount(){
		return $this->db->table(self::TABLE)->count();
	}
}
