<?php

namespace RipDB\Controller;
use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/HomeModel.php');

class HomeController extends Controller
{
	public function __construct(string $page)
	{
		parent::__construct($page, new m\HomeModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$ripCount = $this->model->getRipCount();
		$this->setData('RipCount', $ripCount);
	}
}
