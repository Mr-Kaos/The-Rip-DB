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
		return $this->db->table(self::TABLE)
		->eq('PlaylistID', $id)
		->join('Accounts', 'AccountID', 'Creator')
		->findOne();
	}

	/**
	 * Gets all details from an array of rip IDs.
	 * @param array $ids The Rip IDs
	 */
	public function getRipDetails(array $ids)
	{
		return $this->db->table('Rips')
		->in('RipID', $ids)->findAll();
	}

	/**
	 * Fetches the playlist record with the given share code and account id.
	 * @param string $code The ShareCode of the playlist
	 * @param int $user The ID of the account that owns the playlist with the given share code.
	 * @return ?array If a playlist is found with the given code and user id, its record is returned. Else, null is returned.
	 */
	public function getPlaylistForEdit(string $code, int $userId): ?array
	{
		$playlist = $this->db->table(self::TABLE)
			->eq('ShareCode', $code)
			->eq('Creator', $userId)
			->findOne();

		if (!empty($playlist)) {
			$ids = json_decode($playlist['RipIDs'], true);

			$playlist['RipIDs'] = $ids;
			$playlist['RipNames'] = $this->db->table('Rips')
				->in('RipID', $ids)
				->findAllByColumn('RipName');
		}

		return $playlist;
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
