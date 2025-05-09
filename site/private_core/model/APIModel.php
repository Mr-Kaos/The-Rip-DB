<?php

namespace RipDB\Model;

require_once('Model.php');

class APIModel extends Model
{
	public function getRecords(string $table, string $idColumn, string $nameColumn, ?string $query) {
		$records = $this->db->table($table)
			->columns("$idColumn AS ID", "$nameColumn AS NAME")
			->limit(50)
			->asc($nameColumn);

		if(!empty($query)) {
			$records->ilike($nameColumn, "%$query%");
		}

		return $records->findAll();
	}
}
