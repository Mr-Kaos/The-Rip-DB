<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/JokeModel.php');
require_once('private_core/objects/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\JokeModel $model
 */
class JokeController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	const RIPS_PER_PAGE = 25;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\JokeModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'jokes':
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
				break;
			case 'new-joke':
				$this->setData('tags', $this->model->getTags());
				break;
		}
	}

	public function submitRequest(): array|string
	{
		$result = null;
		if ($this->getPage() == 'new-joke') {
			// Validate data in order of stored procedure parameters.
			$validated = [];
			$validated['JokeName'] = $this->validateString($_POST['name'], 'The given joke name is invalid.', 128);
			$validated['JokeDescription'] = $this->validateString($_POST['description'], 'The given description is invalid.', null, 1);
			$existingTags = $this->model->getTags();
			$validated['PrimaryTag'] = $this->validateFromList($_POST['primary'], $existingTags, 'The selected primary tag does not exist in the database.');
			$tags = $this->validateFromList($_POST['tags'], $existingTags, 'One or more of the given tags do not exist in the database.');
			$validated['TagsJSON'] = json_encode($tags, JSON_NUMERIC_CHECK);
			$metas = $this->validateFromList($_POST['metas'], $existingTags, 'One or more of the given meta tags do not exist in the database.');
			$validated['MetasJSON'] = json_encode($metas, JSON_NUMERIC_CHECK);

			$submission = $this->model->submitFormData($validated, 'usp_InsertJoke');
			if ($submission === true) {
				$result = '/jokes';
			} else {
				$result = $submission;
			}
		}
		return $result;
	}
}
