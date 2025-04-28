<?php

namespace RipDB;

require('Controller.php');
require('private_core/model/RipsModel.php');

class RipsController extends Controller
{
	public function __construct()
	{
		parent::__construct(new RipsModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(): void
	{
		$this->setData('results', $this->search($_GET['search']));

	}

	private function search(string $name)
	{
		return $this->model->getRipsByName($name);
	}
}
