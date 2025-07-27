<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/PlaylistModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\PlaylistModel $model
 */
class PlaylistController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\PlaylistModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'playlists/edit':
				$playlist = $this->model->getPlaylist($data['id']);
				if ($playlist === null) {
					\Flight::redirect('/playlists');
					\RipDB\addNotification('The specified playlist does not exist.', \RipDB\NotificationPriority::Warning);
					die();
				}
				$this->setData('playlist', $playlist);

				break;
		}
	}

	/**
	 * Validates the submission of forms through the playlists pages.
	 */
	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'playlist/edit':
				$validated['InPlaylistID'] = $this->validateNumber($extraData['id']);
			case 'playlist/new':
				$validated['InPlaylistName'] = $this->validateString($_POST['name'], 'The playlist name is not valid.', 128, 1);
				// Make sure all rips given exist
				$rips = $this->validateArray($_POST['rips'], 'validateNumber', [], 'An invalid rip was given.', false);
				if (is_array($rips)) {
					$validated['Rips'] = json_encode($this->model->getValidRips($rips), JSON_NUMERIC_CHECK);
				} else {
					$validated['Rips'] = $rips;
				}

				// If the user is logged in, assign it to their account.
				$accountId = null;
				$public = $_POST['public'] ?? false;
				if (\RipDB\checkAuth()) {
					$accountId = $_SESSION[\RipDB\AUTH_USER];
				} else {
					$public = false;
				}
				$validated['AccountID'] = $accountId;
				$validated['Public'] = $this->validateBool($public);

				if ($this->getPage() == 'playlist/new') {
					$playlistId = 0;
					$result = $this->submitRequest($validated, 'usp_InsertPlaylist', '/playlists', 'Playlist successfully submitted!', $playlistId);
					// If submission was successful, and the the playlist is not linked to a user, get the save code
					
					// $result = array_merge($validated, $this->model->getCodes($playlistId));
					// var_dump($result);
					// die();
					// $result['ShareCode'] = $this->model->getCodes($playlistId);

				// } else {
					// $result = $this->submitRequest($validated, 'usp_UpdatePlaylist', '/playlists', 'Playlist successfully updated!');
				}
				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}
		
		return $result;
	}
}
