<?php

namespace RipDB\Model;

require_once('Model.php');

class HomeModel extends Model
{
	const TABLE = 'Rips';

	/**
	 * Gets the number of rips that exist.
	 */
	public function getRipCount()
	{
		return $this->db->table(self::TABLE)->count();
	}
}
