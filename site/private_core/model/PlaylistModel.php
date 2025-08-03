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

	/**
	 * Gets the codes for a newly created playlist. A name and id is required to verify that the playlist was created by the user making the request.
	 * @param string $playlistId The ID of the playlist to get. Is a string due to being retrieved by a GET variable.
	 * @param string $name The name of the playlist created.
	 * @return array The Share Code and Claim code (if the playlist was created without a login)
	 */
	public function getNewPlaylist(string $playlistId, string $name): ?array
	{
		return $this->db->table('Playlists')
			->join('AnonymousPlaylists', 'PlaylistID', 'PlaylistID')
			->columns('ShareCode', 'ClaimCode')
			->eq('Playlists.PlaylistID', $playlistId)
			->eq('PlaylistName', $name)->findOne();
	}

	/**
	 * Checks if the given anonymous playlist codes exist and returns the ones that do.
	 * @return array An array of playlist claim codes that exist and can be claimed.
	 */
	public function checkUnclaimed(array $codes): array
	{
		return $this->db->table('AnonymousPlaylists')
			->in('ClaimCode', $codes)
			->findAllByColumn('ClaimCode');
	}
}
