<?php

namespace RipDB;

require_once('Model.php');

class APIModel extends Model
{
	public function getTags(?string $query) {
		$tags = $this->db->table('Tags')
			->columns('TagID AS ID', 'TagName AS NAME')
			->limit(50)
			->asc('TagName');

		if(!empty($query)) {
			$tags->ilike('TagName', "%$query%");
		}

		return $tags->findAll();
	}

	public function getJokes(?string $query) {
		$tags = $this->db->table('Jokes')
			->columns('JokeID AS ID', 'JokeName AS NAME')
			->limit(50)
			->asc('TagName');

		if(!empty($query)) {
			$tags->ilike('JokeName', "%$query%");
		}

		return $tags->findAll();
	}
}
