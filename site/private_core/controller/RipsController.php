<?php

namespace RipDB;

require_once('Controller.php');
require_once('private_core/model/RipsModel.php');

class RipsController extends Controller
{
	const RIPS_PER_PAGE = 25;

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
		$recordCount = intval($_GET['c'] ?? self::RIPS_PER_PAGE);

		$page = empty($_GET['p'] ?? null) ? 1 : (int)$_GET['p'];
		$recordStart = (($page - 1) * $recordCount) + 1;

		// If the page number exceeds the number of rips, go to the highest page
		if ($recordStart > $ripCount) {
			$page = ceil($ripCount / $recordCount);
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
		$rips = [];
		$offset = $recordCount * ($page - 1);
		if (array_key_exists('search', $_GET)) {
			$rips = $this->model->searchRips(
				$recordCount,
				$offset,
				$_GET['search'] ?? null,
				$_GET['tags'] ?? [],
				$_GET['jokes'] ?? [],
				$useAltName
			);
		} else {
			$rips = $this->model->searchRips(
				$recordCount,
				$offset
			);
		}

		$this->setData('results', $rips);

		// Get search filters
		$this->setData('tags', $this->model->getTags());
		$this->setData('jokes', $this->model->getJokes());

		// Pagination values
		$recordStart = (($page - 1) * $recordCount) + 1;
		$recordEnd = $page * $recordCount;

		if ($recordEnd > $ripCount) {
			$recordEnd = $ripCount;
		}

		$this->setData('RecordStart', $recordStart);
		$this->setData('RecordEnd', $recordEnd);
		$this->setData('Page', $page);
		$this->setData('Count', $recordCount);
		$this->setData('RipCount', $ripCount);
	}
}
