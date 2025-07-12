<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');

/**
 * @property \RipDB\Model\HomeModel $model
 */
class ErrorController extends Controller
{
	public function __construct(string $page)
	{
		parent::__construct($page);
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$image = null;
		$msg = 'An uncaught exception has occurred.';
		switch ($data['error']) {
			case 'db':
				$image = '/res/img/db-error.png';
				$msg = 'A critical database error has occurred and your request cannot be completed.';
				break;
			case 'db-conf':
				$msg = 'The database connection has not been configured.';
				break;
		}

		$this->setData('image', $image);
		$this->setData('msg', $msg);
	}
}
