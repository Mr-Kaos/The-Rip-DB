<?php

namespace RipDB;

require_once('Controller.php');
require_once('private_core/model/RipsModel.php');
require_once('private_core/objects/Paginator.php');

class RipsController extends Controller
{
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new RipsModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$useAltName = ($_GET['use_secondary'] ?? 0) == 1;
		$ripCount = $this->model->getRipCount($useAltName);
		$rowCount = $this->getRowCount();
		$page = $this->getPageNumber();

		// Get records of rips
		$rips = [];
		$offset = $this->getOffset($ripCount, '/rips');
		$rips = $this->model->searchRips(
			$rowCount,
			$offset,
			$_GET['search'] ?? null,
			$_GET['tags'] ?? [],
			$_GET['jokes'] ?? [],
			$useAltName
		);

		$this->setData('results', $rips);

		// Get search filters
		$this->setData('tags', $this->model->getTags());
		$this->setData('jokes', $this->model->getJokes());

		// Pagination values
		$recordStart = (($page - 1) * $rowCount) + 1;
		$recordEnd = $page * $rowCount;

		if ($recordEnd > $ripCount) {
			$recordEnd = $ripCount;
		}

		$this->setData('RecordStart', $recordStart);
		$this->setData('RecordEnd', $recordEnd);
		$this->setData('Page', $page);
		$this->setData('Count', $rowCount);
		$this->setData('RipCount', $ripCount);
		$this->setData('pagination', $this->buildPagination($ripCount, '/rips/'));
	}
}
