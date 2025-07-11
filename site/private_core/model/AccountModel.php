<?php

namespace RipDB\Model;

require_once('Model.php');

class AccountModel extends Model
{
	const TABLE = 'Accounts';

	public function getAccountInfo(): array {
		return $this->db->table(self::TABLE)->columns('Username', 'Created')->findOne();
	}
}
