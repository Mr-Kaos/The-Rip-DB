<?php

namespace RipDB\Controller;

use RipDB\Error;
use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/AccountModel.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\AccountModel $model
 */
class AccountController extends Controller
{
	use \RipDB\DataValidator;
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

		$this->setData('subPage', $data['subPage'] ?? '');
		switch ($data['subPage'] ?? null) {
			case 'account':
				$this->setData('account', $this->model->getAccountInfo());
				break;
		}
	}

	/**
	 * 
	 */
	public function submitRequest(?array $extraData = null): array|string
	{
		$result = [];
		$validated = [];

		switch ($this->getPage()) {
			case 'account';
				switch ($_GET['mode']) {
					case 'username':

						break;
					case 'password':
						$validated['InAccountId'] = $_SESSION[\RipDB\AUTH_USER];
						$validated['OldPassword'] = $this->validateString($_POST['password'], 'The given password is invalid.', 64, 6);
						$newPass = $this->validateString($_POST['password-new'], 'The given password is invalid.', 64, 6);
						$newPass2 = $this->validateString($_POST['password-new2'], 'The given password is invalid.', 64, 6);

						if ($newPass == $newPass2 && !($newPass instanceof Error || $newPass2 instanceof Error)) {
							$validated['NewPassword'] = $newPass;
							$validated['NewPassword2'] = $newPass2;

							$submission = $this->model->submitFormData($validated, 'usp_UpdateAccountPassword');
							if ($submission === true) {
								\RipDB\addNotification('Successfully updated password!', \RipDB\NotificationPriority::Success);
								$result = '/account';
							} else {
								$result = $submission;
							}
						} else {
							$result = [new Error('The passwords do not match')];
						}
						break;
				}
				break;
		}

		return $result;
	}
}
