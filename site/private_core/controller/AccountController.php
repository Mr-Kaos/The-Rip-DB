<?php

namespace RipDB\Controller;

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
			$this->setData('subPage', $data['subPage'] ?? '');
			switch ($data['subPage'] ?? null) {
				case 'account':
					$this->setData('account', $this->model->getAccountInfo());
					break;
				case 'playlists':
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
					break;
			}
		} else {
			\RipDB\addNotification('You must login to access that page.', \RipDB\NotificationPriority::Warning);
			\Flight::redirect('/login');
			die();
		}
	}

	/**
	 * 
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
		}

		return $result;
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
}
