<?php

namespace RipDB;

require_once('Model.php');

class HomeModel extends Model
{
	const TABLE = 'Rips';

	/**
	 * Gets data about a specific rip.
	 */
	public function getRipCount()
	{
		return $this->db->table(self::TABLE)->count();
	}
}
