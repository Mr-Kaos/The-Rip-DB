<?php

namespace RipDB\Model;

require('Model.php');

class ChannelModel extends Model implements ResultsetSearch
{
	const TABLE = 'Channels';
	const COLUMNS = ['ChannelID', 'ChannelName'];

	public function search(int $count, int $offset, ?array $sort, ?string $name = null): array
	{
		$qry = $this->db->table(self::TABLE)
			->select('ChannelID, ChannelName, ChannelURL, ChannelDescription, COUNT(RipID) RipCount')
			->asc('ChannelID');

		// Apply name search if name is given.
		if (!empty($name)) {
			$qry->ilike('ChannelName', "%$name%");
		}

		$qry->limit($count)
			->offset($offset)
			->join('Rips', 'RipChannel', 'ChannelID')
			->groupBy('ChannelID', 'ChannelName', 'ChannelURL', 'ChannelDescription');
		$channels = $qry->findAll();

		return $channels;
	}

	public function getCount(): int
	{
		return $this->db->table(self::TABLE)->count();
	}

	/**
	 * Retrieves a list of all channel names that exist in the database. Used in validating new/existing channels.
	 * @param ?int $excludeId If given, will exclude the channel with this ID.
	 */
	public function getAllChannelNames(?int $excludeId = null): array
	{
		$qry = $this->db->table(self::TABLE);
		if ($excludeId !== null) {
			$qry = $qry->neq('ChannelID', $excludeId);
		}
		return $qry->findAllByColumn('ChannelName');
	}

	/**
	 * Retrieves the data for the specified channel.
	 * @param int $id The ID of the channel to retrieve.
	 * @return ?array An associative array containing the data of the channel or null if the given channel ID does not exist.
	 */
	public function getChannel(int $id): ?array
	{
		return $this->db->table(self::TABLE)->eq('ChannelID', $id)->findOne();
	}
}
