<?php

namespace RipDB\Controller;

use DateTime;
use RipDB\Error;
use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/AccountModel.php');
require_once('private_core/controller/LoginController.php');
require_once('private_core/objects/DataValidators.php');
require_once('private_core/objects/IAsyncHandler.php');
require_once('private_core/objects/pageElements/Paginator.php');

/**
 * @property \RipDB\Model\AccountModel $model
 */
class AccountController extends Controller implements \RipDB\Objects\IAsyncHandler
{
	use \RipDB\DataValidator;
	use \Paginator;

	/** The maximum number of API requests that can be made to the YouTube API per day. */
	const YT_API_MAX_DAILY_REQUESTS = 10000;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\AccountModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		// Ensure the user is authenticated before proceeding
		if (\RipDB\checkAuth()) {
			$activePage = 'account';
			switch ($this->getPage()) {
				case 'account/edit':
					$this->setData('account', $this->model->getAccountInfo());
					break;
				case 'account/playlists':
					$recordCount = $this->model->getCount($_SESSION[\RipDB\AUTH_USER], $_GET['search'] ?? null);
					$rowCount = $this->getRowCount();
					$page = $this->getPageNumber();

					// Get records of rips
					$offset = $this->getOffset($recordCount, '/account/playlists');
					$playlists = $this->model->search(
						$rowCount,
						$offset,
						null,
						$_SESSION[\RipDB\AUTH_USER],
						$_GET['search'] ?? null,
					);

					$this->setData('results', $playlists);

					// Pagination values
					$recordStart = (($page - 1) * $rowCount) + 1;
					$recordEnd = $page * $rowCount;

					if ($recordEnd > $recordCount) {
						$recordEnd = $recordCount;
					}

					$this->setData('RecordStart', $recordStart);
					$this->setData('RecordEnd', $recordEnd);
					$this->setData('Page', $page);
					$this->setData('Count', $rowCount);
					$this->setData('TagCount', $recordCount);
					$this->setData('pagination', $this->buildPagination($recordCount, '/account/playlists'));
					$activePage = 'playlists';
					break;
			}
			$this->setData('activePage', $activePage);
		} else {
			\RipDB\addNotification('You must login to access that page.', \RipDB\NotificationPriority::Warning);
			\Flight::redirect('/login');
			die();
		}
	}


	public function get(string $method, ?string $methodGroup = null): mixed
	{
		$result = null;
		switch ($methodGroup) {
			case 'check':
				if ($method == 'user') {
					$result = $this->model->checkValidUsername($_GET['username'] ?? null);
				}
				break;
		}
		return $result;
	}
	public function post(string $method, ?string $methodGroup = null): mixed
	{
		return null;
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
	 * Validates forms submissions through the account pages.
	 */
	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		$validated = [];

		switch ($this->getPage()) {
			case 'account/edit';
				switch ($_GET['mode']) {
					case 'username':
						$validated['InAccountId'] = $_SESSION[\RipDB\AUTH_USER];
						$validated['NewUsername'] = $this->validateString($_POST['username'], 'The given username is invalid', 32, 3, '/' . LoginController::USERNAME_REGEX . '/');
						$validated['InPassword'] = $this->validateString($_POST['password'], 'The given password is invalid.', 64, 6);

						$result = $this->submitRequest($validated, 'usp_UpdateAccountUsername', '/account', 'Successfully updated username!');
						break;
					case 'password':
						$validated['InAccountId'] = $_SESSION[\RipDB\AUTH_USER];
						$validated['OldPassword'] = $this->validateString($_POST['password'], 'The given password is invalid.', 64, 6);
						$newPass = $this->validateString($_POST['password-new'], 'The given password is invalid.', 64, 6);
						$newPass2 = $this->validateString($_POST['password-new2'], 'The given password is invalid.', 64, 6);

						if ($newPass == $newPass2 && !($newPass instanceof Error || $newPass2 instanceof Error)) {
							$validated['NewPassword'] = $newPass;
							$validated['NewPassword2'] = $newPass2;
							$result = $this->submitRequest($validated, 'usp_UpdateAccountPassword', '/account', 'Successfully updated password!');
						} else {
							$result = [new Error('The passwords do not match')];
						}
						break;
					case 'delete':
						$validated['InAccountId'] = $_SESSION[\RipDB\AUTH_USER];
						$pass = $this->validateString($_POST['password-check'], 'The given password is invalid.', 64, 6);
						$pass2 = $this->validateString($_POST['password-check2'], 'The given password is invalid.', 64, 6);

						if ($pass == $pass2 && !($pass instanceof Error || $pass2 instanceof Error)) {
							$validated['InPassword'] = $pass;
							$result = $this->submitRequest($validated, 'usp_DeleteAccount', '/', 'Successfully deleted account.');
						} else {
							$result = [new Error('The passwords do not match')];
						}
						break;
				}
				break;
			case 'playlists/claim':
				if ($this->model->checkClaimCode($_POST['code'] ?? '')) {
					$validated['ClaimCodes'] = '["' . strtoUpper($_POST['code']) . '"]';
					$validated['AccountID'] = $_SESSION[\RipDB\AUTH_USER];

					$result = $this->submitRequest($validated, 'usp_ClaimPlaylists', '/account/playlists', 'Successfully claimed playlist!');
				} else {
					$result = [new Error('This claim code has expired or has already been claimed.')];
				}
				break;
			case 'playlists/import':
				// Ensure the YouTube API key has been set up
				include_once('private_core/config/keys.php');

				if (defined('YouTube_Key') && constant('YouTube_Key') !== null) {
					$url = $this->validateString($_POST['playlist_url'], 'Invalid Playlist URL.', null, null, '/https:\/\/.*youtu.*\?.*list=([a-zA-Z_\-0-9]{34}).*/');

					if (!$url instanceof Error) {
						preg_match('/https:\/\/.*youtu.*\?.*list=([a-zA-Z_\-0-9]{34}).*/', $url, $matches);
						$listId = $matches[1];
						$MAX_cURLs = 25; // The maximum number of cURLs requests that can be made before aborting. Multiply this number by 50 to determine how many videos can be fetched.

						$onLastPage = false;
						$loops = 0;
						$nextPageToken = null;
						$videos = [];

						$apiCount = $this->updateAPICount();
						if ($apiCount !== false) {
							if ($apiCount < self::YT_API_MAX_DAILY_REQUESTS) {
								// Fetch the videos
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

								while (!$onLastPage && $loops < $MAX_cURLs) {
									$apiURL = 'https://www.googleapis.com/youtube/v3/playlistItems?key=' . constant('YouTube_Key') . '&playlistId=' . $listId . '&part=snippet%2C+id&maxResults=50';
									if ($nextPageToken !== null) {
										$apiURL .= '&pageToken=' . $nextPageToken;
									}
									curl_setopt($ch, CURLOPT_URL, $apiURL);
									$response = curl_exec($ch);
									$json = json_decode($response);

									$videos = array_merge($videos, $this->parseYoutubePlaylistJSON($json));

									$nextPageToken = $json->nextPageToken ?? null;
									if (is_null($nextPageToken)) {
										$onLastPage = true;
									}

									$loops++;
								}

								$this->updateAPICount($loops);
							} else {
								error_log('The site has exceeded the maximum allowed API Request limit of ' . self::YT_API_MAX_DAILY_REQUESTS . '. Please wait for the count to reset.');
								$result = [new Error('The YouTube API cannot not be used at this time. Please try again later.')];
							}
						} else {
							$result = [new Error('A server error occurred.')];
						}

						// Filter videos to ones that exist in the database
						$rips = $this->model->findRipsByYouTubeID($videos);
						if (count($rips) > 0) {
							// Get the playlist name from YouTube
							$apiURL = 'https://www.googleapis.com/youtube/v3/playlists?key=' . constant('YouTube_Key') . '&id=' . $listId . '&part=snippet,status';
							curl_setopt($ch, CURLOPT_URL, $apiURL);
							$response = curl_exec($ch);
							$json = json_decode($response);

							$validated['InPlaylistName'] = empty($json->items[0]->snippet->title) ? 'Unnamed Playlist' : $json->items[0]->snippet->title;
							$validated['InPlaylistDescription'] = $json->items[0]->snippet->description;
							$validated['Rips'] = json_encode($rips);
							$validated['AccountId'] = $_SESSION[\RipDB\AUTH_USER];
							$validated['Public'] = $json->items[0]->status->privacyStatus == 'public';
							$playlistId = 0;

							$this->submitRequest($validated, 'usp_InsertPlaylist', '/account/playlists', 'Successfully imported ' . count($rips) . ' rips to your playlist "' . $validated['InPlaylistName'] . '".', $playlistId);
						}
						curl_close($ch);
					}
				} else {
					error_log("The YouTube API key is not configured in the site's config!");
					$result = [new Error('The YouTube API could not be accessed at this time.')];
				}
				break;
		}

		return $result;
	}

	/**
	 * Parses the JSON returned by YouTube's API and returns the video IDs of each video in the playlist.
	 * @param JSON $json The JSON response from the YouTUbe API to parse
	 * @return array|Error An array of YouTube video IDs or an Error if the YouTube API returned an error.
	 */
	private function parseYoutubePlaylistJSON($json): array|Error
	{
		$videos = [];

		if (($json->error ?? null) != null) {
			error_log("YouTube API Error " . $json->error->code . ": " . $json->error->message);
			$videos = new Error("An error occurred while using the YouTube API.");
		} else {
			// var_dump($parsed);
			foreach ($json->items as $video) {
				array_push($videos, $video->snippet->resourceId->videoId);
			}
		}

		return $videos;
	}

	/**
	 * Reads or writes to the api_count file in the config directory to track how many daily requests are being made.
	 * If request counts are given, the file is written to. If none are given, the file is read from and the count returned.
	 * If the date the log file was reset is greater than 24 hours, the count and date is reset to the current time.
	 * @param ?int $additionalRequests The number of requests made to add to the current count. If null, the file will only be read from.
	 * @return int|false The current number of requests made. False if the count could not be saved
	 */
	private function updateAPICount(?int $additionalRequests = null): int|false
	{
		$countFile = 'private_core/config/api_count.txt';
		$datetime_format = 'Y-m-d H:i:s';
		$todaysRequestCount = 0;
		$lastDay = null;

		// Check if the count file exists. If it does not, create it.
		if (!file_exists($countFile)) {
			if (($fh = fopen($countFile, 'w')) !== false) {
				$lastDay = new DateTime();
				fwrite($fh, date_format($lastDay, $datetime_format));
				fwrite($fh, "\n0");
				fclose($fh);
			} else {
				$todaysRequestCount = false;
			}
		}
		// If the file exists, get the number of requests performed per day and date stored.
		else {
			if (($fh = fopen($countFile, 'rw')) !== false) {
				$i = 0;
				while (($line = fgets($fh)) !== false) {
					$line = trim($line);
					if ($i == 0) {
						$lastDay = DateTime::createFromFormat($datetime_format, $line);
					} elseif ($i == 1) {
						$todaysRequestCount = (int)$line;
					}
					$i++;
				}

				// If the date was malformed or not detected, rewrite it
				if ($lastDay == false) {
					fwrite($fh, date_format(new DateTime(), $datetime_format));
					fwrite($fh, "\n$todaysRequestCount");
				}
				// If the date is more than 24 hours ago, reset it to now and reset the count.
				elseif (date_diff(new DateTime(), $lastDay)->d >= 1) {
					$lastDay = new DateTime();
					$todaysRequestCount = 0;
				}
				fclose($fh);
			} else {
				$todaysRequestCount = false;
			}
		}

		// If requests are made,
		if (!empty($additionalRequests)) {
			$todaysRequestCount += $additionalRequests;
			if (($fh = fopen($countFile, 'rw')) !== false) {
				fwrite($fh, date_format($lastDay, $datetime_format));
				fwrite($fh, "\n" . $todaysRequestCount);
				fclose($fh);
			} else {
				$todaysRequestCount = false;
			}
		}

		return $todaysRequestCount;
	}
}
