<?php

namespace RipDB\Model;

require('Model.php');

class PlaylistModel extends Model
{
	const TABLE = 'Playlists';

	/**
	 * Fetches the playlist record with the given ID.
	 */
	public function getPlaylist(int $id): ?array
	{
		return $this->db->table(self::TABLE)->eq('PlaylistID', $id)->findOne();
	}

	/**
	 * Checks if the given Rip IDs exist in the database. If any don't, they are removed.
	 * @param array $ripIDs The IDs of rips to check if they exist.
	 */
	public function getValidRips(array $ripIDs): array
	{
		return $this->db->table('Rips')->in('RipID', $ripIDs)->findAllByColumn('RipID');
	}

	/**
	 * Gets the sharing and saving  codes for the specified playlist.
	 * 
	 */
	public function getCodes(int $playlistId): array
	{
		return $this->db->table('Playlists')
			->join('AnonymousPlaylists', 'PlaylistID', 'PlaylistID')
			->columns('ShareCode', 'ClaimCode')->eq('Playlists.PlaylistID', $playlistId)->findOne();
	}
}
