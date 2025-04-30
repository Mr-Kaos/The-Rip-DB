<?php

namespace RipDB;

require('Controller.php');
require('private_core/model/RipsModel.php');

class RipsController extends Controller
{
	const RIPS_PER_PAGE = 25;

	public function __construct()
	{
		parent::__construct(new RipsModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$useAltName = ($_GET['use_secondary'] ?? 0) == 1;
		$ripCount = $this->model->getRipCount($useAltName);

		$page = empty($_GET['p']) ? 1 : (int)$_GET['p'];
		$recordStart = (($page - 1) * self::RIPS_PER_PAGE) + 1;

		// If the page number exceeds the number of rips, go to the highest page
		if ($recordStart > $ripCount) {
			$page = ceil($ripCount / 25);
			$request = $_GET;
			if (!array_key_exists('p', $request)) {
				$request['p'] = 1;
			} else {
				$request['p'] = $page;
			}

			header('location:/rips?' . http_build_query($request));
			die();
		}


		// Get records of rips
		if (array_key_exists('search', $_GET)) {
			$this->setData('results', $this->search($_GET['search'] ?? null, ($page - 1), $useAltName));
		} else {
			$this->setData('results', $this->model->getRips(self::RIPS_PER_PAGE, ($page - 1)));
		}

		// Get search filters
		$this->setData('tags', $this->model->getSearchTags());
		$this->setData('jokes', $this->model->getSearchJokes());

		// Pagination values
		$recordStart = (($page - 1) * self::RIPS_PER_PAGE) + 1;
		$recordEnd = $page * self::RIPS_PER_PAGE;

		if ($recordEnd > $ripCount) {
			$recordEnd = $ripCount;
		}

		$this->setData('RecordStart', $recordStart);
		$this->setData('RecordEnd', $recordEnd);
		$this->setData('Page', $page);
	}

	private function search(?string $name, int $offset, bool $useAltName)
	{
		$offset = self::RIPS_PER_PAGE * $offset;
		return $this->model->getRipsByName($name, self::RIPS_PER_PAGE, $offset, $useAltName);
	}
}
