<?php

namespace RipDB\Controller;

use RipDB\Model as m;

use const RipDB\AUTH_USER;

use function RipDB\checkAuth;

require_once('Controller.php');
require_once('private_core/model/PlaylistModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');
require_once('private_core/objects/IAsyncHandler.php');

/**
 * @property \RipDB\Model\PlaylistModel $model
 */
class PlaylistController extends Controller implements \RipDB\Objects\IAsyncHandler
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
			case 'playlist/view':
				$id = $data['id'] ?? null;
				if (!empty($id) && is_numeric($id)) {
					$playlist = $this->model->getPlaylist($id);
					
					// If the playlist is not public, check if the user viewing it is the owner
					if (($playlist['IsPublic'] == 0 && $playlist['Creator'] == $_SESSION[\RipDB\AUTH_USER]) || $playlist['IsPublic'] == 1) {
						$this->setData('playlist', $playlist);
						$this->setData('rips', $this->model->getRipDetails(json_decode($playlist['RipIDs'])));
					}
				}
				break;
		}
	}

	public function get(string $method, ?string $methodGroup = null): mixed
	{
		$result = null;

		switch ($methodGroup) {
			case 'getNewPlaylist':
				$result = $this->model->getNewPlaylist($_GET['id'] ?? 0, $_GET['name'] ?? '');
				break;
			case 'getPlaylist':
				// The user must be logged in to edit their playlist
				if (checkAuth()) {
					$result = $this->model->getPlaylistForEdit($_GET['code'] ?? '', $_SESSION[AUTH_USER]);
					if (empty($result)) {
						$result = 'The specified playlist does not exist.';
					}
				} else {
					$result = 'You must be logged in to edit a playlist.';
				}
				break;
			case 'checkUnclaimed':
				$result = checkAuth();
				if (checkAuth()) {
					$codes = $_GET['codes'] ?? '';
					$result = $this->model->checkUnclaimed(explode(',', $codes));
				}
				break;
			default:
				break;
		}

		return $result;
	}
	public function post(string $method, ?string $methodGroup = null): mixed
	{
		$result = null;

		switch ($methodGroup) {
			case 'claimPlaylists':
			case 'delete':
				// If an array is returned (i.e. an error), return the error messages.
				if (is_array($out = $this->validateRequest())) {
					$result = '';
					foreach ($out as $msg) {
						$result .= $msg->getMessage() . "\n";
					}
				} else {
					$result = true;
				}
				break;
			default:
				break;
		}

		return $result;
	}
	public function put(string $method, ?string $methodGroup = null): mixed
	{
		return null;
	}
	public function delete(string $method, ?string $methodGroup = null): mixed
	{
		return null;
	}

	/**
	 * Validates the submission of forms through the playlists pages.
	 */
	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'playlist/edit':
				$playlist = $this->model->getPlaylistForEdit($_POST['code'] ?? '', $_SESSION[AUTH_USER] ?? 0);
				if (empty($playlist)) {
					$result = [new \RipDB\Error('The playlist being edited does not belong to you.')];
					break;
				}
				$validated['InPlaylistId'] = $playlist['PlaylistID'];
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
				} else {
					$result = $this->submitRequest($validated, 'usp_UpdatePlaylist', '/rips', 'Playlist successfully updated!');
				}
				break;
			case 'claim':
				$codes = $_POST['ClaimCodes'] ?? null;
				if (!empty($codes) && checkAuth()) {
					$codes = $this->model->checkUnclaimed(explode(',', $codes));
					$validated['ClaimCodes'] = json_encode($codes);
					$validated['AccountID'] = $_SESSION[AUTH_USER];

					$result = $this->submitRequest($validated, 'usp_ClaimPlaylists', '', 'Playlists successfully claimed!');
				} else {
					$result = [new \RipDB\Error('Invalid Claim Code format given.')];
				}
				break;
			case 'delete':
				$playlist = $this->model->getPlaylistForEdit($_POST['code'] ?? '', $_SESSION[AUTH_USER] ?? 0);

				if (!empty($playlist)) {
					$validated['InPlaylistID'] = $playlist['PlaylistID'];
					$validated['AccountID'] = $_SESSION[AUTH_USER];

					$result = $this->submitRequest($validated, 'usp_DeletePlaylist', '', 'Playlists successfully deleted!');
				}else {
					$result = [new \RipDB\Error('The playlist does not exist.')];
				}

				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}

		return $result;
	}
}
