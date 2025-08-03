<?php

namespace RipDB\Model;

require_once('Model.php');

class AccountModel extends Model implements ResultsetSearch
{
	const TABLE = 'Accounts';
	const ILLEGAL_NAMES = ['admin', 'Admin'];

	public function getAccountInfo(): ?array
	{
		$account = $this->db->table(self::TABLE)->columns('Username', 'Created')->eq('AccountID', $_SESSION[\RipDB\AUTH_USER])->findOne();
		// If the account is somehow not found (most likely due to the session value being invalid), kill the session to retry.
		if (is_null($account)) {
			session_destroy();
			\Flight::redirect('/');
		}
		return $account;
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

	/**
	 * Gets the user's playlists.
	 * @param int $userId The ID of the user's account.
	 */
	public function getPlaylists(int $userId): ?array
	{
		return $this->db->table('vw_Playlists')
			->eq('Creator', $userId)
			->findAll();
	}

	/**
	 * Searches the playlists view for the user's playlists. If somehow no user id is set in the session, the user ID of 0 is used.
	 */
	public function search(int $count, int $offset, ?array $sort, int $userId = 0, ?string $name = null): array
	{
		$qry = $this->db->table('vw_Playlists')
			->eq('Creator', $userId)
			->asc('PlaylistName');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('PlaylistName', "%$name%");
		}

		$qry->limit($count)
			->offset($offset);
		$playlists = $qry->findAll();

		return $playlists;
	}

	public function getCount(int $userId = 0, ?string $name = null): int
	{
		$qry = $this->db->table('vw_Playlists')
			->eq('Creator', $userId);

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('PlaylistName', "%$name%");
		}

		return $qry->count();
	}
}
