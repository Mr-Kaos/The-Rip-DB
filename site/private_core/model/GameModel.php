<?php

namespace RipDB\Model;

require('Model.php');

class GameModel extends Model implements ResultsetSearch
{
	const TABLE = 'Games';
	const COLUMNS = ['GameID', 'GameName', 'IsFake'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null): array
	{
		$qry = $this->db->table(self::TABLE)
			->select('GameID, GameName, IsFake, COUNT(RipID) RipCount')
			->asc('GameID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('GameName', "%$name%");
		}

		$qry->limit($count)
			->offset($offset)
			->join('Rips', 'RipGame', 'GameID')
			->groupBy('GameID', 'GameName', 'IsFake');
		$games = $qry->findAll();

		return $this->setSubArrayValueToKey($games, 'GameID', false);;
	}

	/**
	 * Retrieves a list of all game names that exist in the database. Used in validating new/existing games.
	 * @param ?int $excludeId If given, will exclude the game with this ID.
	 */
	public function getAllGameNames(?int $excludeId = null): array
	{
		$qry = $this->db->table(self::TABLE);
		if ($excludeId !== null) {
			$qry = $qry->neq('GameID', $excludeId);
		}
		return $qry->findAllByColumn('GameName');
	}

	/**
	 * Retrieves the data for the specified game.
	 * @param int $id The ID of the game to retrieve.
	 * @return ?array An associative array containing the data of the game or null if the given game ID does not exist.
	 */
	public function getGame(int $id): ?array
	{
		return $this->db->table(self::TABLE)->eq('GameID', $id)->findOne();
	}

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}
}
