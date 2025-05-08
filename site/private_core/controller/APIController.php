<?php

namespace RipDB;

require_once('Controller.php');
require_once('private_core/model/APIModel.php');

class APIController extends Controller
{
	public function __construct(string $page)
	{
		parent::__construct($page, new APIModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$search = $_GET['q'] ?? null;
		$result = null;

		switch ($this->getPage()) {
			case 'tags':
				$result = $this->model->getTags($search);
				break;
			case 'jokes':
				$result = $this->model->getJokes($search);
				break;
		}

		$this->setData('Result', $result);
	}
}
