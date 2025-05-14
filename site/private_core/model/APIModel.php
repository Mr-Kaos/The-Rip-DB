<?php

namespace RipDB\Model;

require_once('Model.php');

class APIModel extends Model
{
	public function getRecords(string $table, string $idColumn, string $nameColumn, ?string $like, ?string $whereCol = null, mixed $whereVal = null)
	{
		$records = $this->db->table($table)
			->columns("$idColumn AS ID", "$nameColumn AS NAME")
			->limit(50)
			->asc($nameColumn);

		if (!empty($like)) {
			$records->ilike($nameColumn, "%$like%");
		}

		if (!empty($whereCol)) {
			$records->eq($whereCol, $whereVal);
		}

		return $records->findAll();
	}
}
