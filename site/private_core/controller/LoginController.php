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
	const USERNAME_REGEX = '^(?=.{3,32}$)(?!.*[{}\\|\/;,\[\]^@])[a-zA-Z0-9._+-~]+$';
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
	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		$validated = [];
		$loginId = 0;

		switch ($this->getPage()) {
			case 'login/login';
				$validated['InUsername'] = $this->validateString($_POST['username'], 'The given username is invalid', 32, 3, '/' . self::USERNAME_REGEX . '/');
				$validated['InPassword'] = $this->validateString($_POST['password'], 'The given password is invalid.', 64, 6);

				$result = $this->submitRequest($validated, 'usp_SelectLogin', '/', 'Successfully logged in!', $loginId);

				if (is_string($result)) {
					$_SESSION[\RipDB\AUTH_USER] = $loginId;
				} elseif ($result === false) {
					$result = '/login';
				}
				break;
			case 'login/new':
				if (!empty($_POST['password'] ?? null) && !empty($_POST['password2'] ?? null)) {
					if ($_POST['password'] == $_POST['password2']) {
						$validated['NewUsername'] = $this->validateString($_POST['username'], 'The given username is not valid', 32, 3, '/' . self::USERNAME_REGEX . '/');
						$validated['NewPassword'] = $this->validateString($_POST['password'], 'The given password is not valid.', 64, 6);

						$result = $this->submitRequest($validated, 'usp_InsertLogin', '/', 'Successfully created account!', $loginId);

						if (is_string($result)) {
							$_SESSION[\RipDB\AUTH_USER] = $loginId;
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
