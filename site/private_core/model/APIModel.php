<?php

namespace RipDB\Model;

require_once('Model.php');

class APIModel extends Model
{
	public function getRecords(string $table, string $idColumn, string $nameColumn, ?string $like, bool $random = false, ?string $whereCol = null, mixed $whereVal = null)
	{
		$result = [];

		// If random results are requested, retrieve 50 random values.
		// This is so the preview of results in the searchable dropdowns provides unique suggestions each time.
		if ($random) {
			$result = $this->db->execute("SELECT $idColumn AS ID, $nameColumn AS NAME FROM $table ORDER BY RAND() LIMIT 20")->fetchAll(\PDO::FETCH_ASSOC);
		} else {
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
			$result = $records->findAll();
		}

		return $result;
	}
}
