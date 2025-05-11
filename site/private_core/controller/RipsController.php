<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/RipsModel.php');
require_once('private_core/objects/Paginator.php');

/**
 * @property \RipDB\Model\RipsModel $model
 */
class RipsController extends Controller
{
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\RipsModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		$useAltName = ($_GET['use_secondary'] ?? 0) == 1;
		$ripCount = $this->model->getRipCount(
			$_GET['search'] ?? null,
			$_GET['tags'] ?? [],
			$_GET['jokes'] ?? [],
			$_GET['games'] ?? [],
			$useAltName
		);
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
			$_GET['games'] ?? [],
			$_GET['rippers'] ?? [],
			$_GET['genres'] ?? [],
			$useAltName
		);
		$this->setData('results', $rips);

		// Get search filters
		$this->setData('tags', $this->model->getFilterResults('Tag', $_GET['tags'] ?? []));
		$this->setData('jokes', $this->model->getFilterResults('Joke', $_GET['jokes'] ?? []));
		$this->setData('games', $this->model->getFilterResults('Game', $_GET['games'] ?? []));
		$this->setData('rippers', $this->model->getFilterResults('Ripper', $_GET['rippers'] ?? []));
		$this->setData('genres', $this->model->getFilterResults('Genre', $_GET['genres'] ?? []));

		// If any filter is given, make sure the details element is open by setting its "open" attribute
		if (!empty($_GET['tags'] ?? null) || !empty($_GET['jokes'] ?? null) || !empty($_GET['games'] ?? null) || !empty($_GET['rippers'] ?? null)) {
			$this->setData('open', 'open');
		} else {
			$this->setData('open', '');
		}

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
		$this->setData('pagination', $this->buildPagination($ripCount, '/rips'));
	}
}
