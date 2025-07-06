<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/LoginModel.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\LoginModel $model
 */
class LoginController extends Controller
{
	use \RipDB\DataValidator;
	public function __construct(string $page)
	{
		parent::__construct($page, new m\LoginModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void {}

	/**
	 * 
	 */
	public function submitRequest(?array $extraData = null): array|string
	{
		$result = [];
		$validated = [];
		$loginId = 0;

		switch ($this->getPage()) {
			case 'login';
				$validated['InUsername'] = $this->validateString($_POST['username'], 'The given username is invalid', 32, 3, '/^(?=.{3,32}$)[a-zA-Z0-9._+-~]+$/');
				$validated['InPassword'] = $this->validateString($_POST['password'], 'The given password is invalid.', 64, 6);
				
				$submission = $this->model->submitFormData($validated, 'usp_SelectLogin', $loginId);
				if ($submission === true && $loginId != 0) {
					$_SESSION[\RipDB\AUTH_USER] = $loginId;
					\RipDB\addNotification('Successfully logged in!', \RipDB\NotificationPriority::Success);
					$result = '/';
				} else {
					$result = $submission;
				}

				break;
			case 'login-new':
				if (!empty($_POST['password'] ?? null) && !empty($_POST['password2'] ?? null)) {
					if ($_POST['password'] == $_POST['password2']) {
						$validated['NewUsername'] = $this->validateString($_POST['username'], 'The given username is not valid', 32, 3, '/^(?=.{3,32}$)[a-zA-Z0-9._+-~]+$/');
						$validated['NewPassword'] = $this->validateString($_POST['password'], 'The given password is not valid.', 64, 6);

						$submission = $this->model->submitFormData($validated, 'usp_InsertLogin', $loginId);
						if ($submission === true) {
							\RipDB\addNotification('Successfully created account!', \RipDB\NotificationPriority::Success);
							$_SESSION[\RipDB\AUTH_USER] = $loginId;
							$result = '/';
						} else {
							$result = $submission;
						}
					} else {
						$result = [new \RipDB\Error("The given passwords do not match!")];
					}
				} else {
					$result = [new \RipDB\Error("Passwords must be given and match!")];
				}
				break;
			case 'logout':
				unset($_SESSION[\RipDB\AUTH_USER]);
				\RipDB\addNotification('You have been logged out.', \RipDB\NotificationPriority::Success);
				$result = '/';
				break;
		}

		return $result;
	}
}
