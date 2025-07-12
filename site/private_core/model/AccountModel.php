<?php

namespace RipDB\Model;

require_once('Model.php');

class AccountModel extends Model
{
	const TABLE = 'Accounts';
	const ILLEGAL_NAMES = ['admin', 'Admin'];

	public function getAccountInfo(): array
	{
		return $this->db->table(self::TABLE)->columns('Username', 'Created')->findOne();
	}

	/**
	 * Checks if the given username is in use or is valid.
	 * @return bool True if it is in use, false if not.
	 */
	public function checkValidUsername(?string $username): bool
	{
		$valid = true;
		if (!empty($username) && array_search($username, self::ILLEGAL_NAMES) === false) {
			$valid = !$this->db->table(self::TABLE)->eq('Username', $username)->exists();
		} else {
			$valid = false;
		}
		return $valid;
	}
}
