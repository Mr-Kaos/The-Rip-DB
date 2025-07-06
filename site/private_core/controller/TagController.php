<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/TagModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\TagModel $model
 */
class TagController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	const RIPS_PER_PAGE = 25;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\TagModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'tags':
				$recordCount = $this->model->getTagCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of rips
				$offset = $this->getOffset($recordCount, '/tags');
				$tags = $this->model->searchTags(
					$rowCount,
					$offset,
					$_GET['search'] ?? null,
				);

				$this->setData('results', $tags);

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
				$this->setData('TagCount', $recordCount);
				$this->setData('pagination', $this->buildPagination($recordCount, '/tags'));
				break;
			case 'new-tag':
				break;
		}
	}

	public function submitRequest(?array $extraData = null): array|string
	{
		$result = [];
		if ($this->getPage() == 'new-tag') {
			$validated['TagName'] = $this->validateFromList($_POST['name'], $this->model->getAllTagNames(), 'The given value is already taken', true);
		} else {
			$result = [new \RipDB\Error('Invalid form submission.')];
		}

		$newTag = 0;
		$submission = $this->model->submitFormData($validated, 'usp_InsertTag', $newTag);
		if ($submission == true) {
			$result = '/tags';
		} else {
			$result = $submission;
		}
		return $result;
	}
}
