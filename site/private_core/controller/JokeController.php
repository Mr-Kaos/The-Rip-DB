<?php

namespace RipDB;

require_once('Controller.php');
require_once('private_core/model/JokeModel.php');
require_once('private_core/objects/Paginator.php');

class JokeController extends Controller
{
	use \Paginator;

	const RIPS_PER_PAGE = 25;

	public function __construct(string $page)
	{
		parent::__construct($page, new JokeModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$recordCount = $this->model->getJokeCount();
		$rowCount = $this->getRowCount();
		$page = $this->getPageNumber();

		// Get records of rips
		$rips = [];
		$offset = $this->getOffset($recordCount, '/jokes');
		$jokes = $this->model->searchJokes(
			$rowCount,
			$offset,
			$_GET['search'] ?? null,
			$_GET['tags'] ?? [],
			$_GET['metas'] ?? [],
		);

		$this->setData('results', $jokes);

		// Pagination values
		$recordStart = (($page - 1) * $rowCount) + 1;
		$recordEnd = $page * $rowCount;

		if ($recordEnd > $recordCount) {
			$recordEnd = $recordCount;
		}

		$this->setData('RecordStart', $recordStart);
		$this->setData('RecordEnd', $recordEnd);
		$this->setData('Page', $page);
		$this->setData('Count', $rowCount);
		$this->setData('JokeCount', $recordCount);
		$this->setData('pagination', $this->buildPagination($recordCount, '/jokes'));
	}
}
