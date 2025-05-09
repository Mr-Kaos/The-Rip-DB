<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/APIModel.php');

class APIController extends Controller
{
	public function __construct(string $page)
	{
		parent::__construct($page, new m\APIModel());
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
				$result = $this->model->getRecords('Tags', 'TagID', 'TagName', $search);
				break;
			case 'jokes':
				$result = $this->model->getRecords('Jokes', 'JokeID', 'JokeName', $search);
				break;
			case 'metas':
				$result = $this->model->getRecords('MetaJokes', 'MetaJokeID', 'MetaJokeName', $search);
				break;
		}

		$this->setData('Result', $result);
	}
}
