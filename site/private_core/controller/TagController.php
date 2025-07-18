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
			case 'tags/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of rips
				$offset = $this->getOffset($recordCount, '/tags');
				$tags = $this->model->search(
					$rowCount,
					$offset,
					null,
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
			case 'tags/edit':
				$tag = $this->model->getTag($data['id']);
				if ($tag === null) {
					\Flight::redirect('/tags');
					\RipDB\addNotification('The specified tag does not exist.', \RipDB\NotificationPriority::Warning);
					die();
				}
				$this->setData('tag', $tag);

				break;
		}
	}

	/**
	 * Validates the submission of forms through the tags pages.
	 */
	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'tags/edit':
				$validated['InTagID'] = $this->validateNumber($extraData['id']);
			case 'tags/new':
				$validated['InTagName'] = $this->validateFromList($_POST['name'], $this->model->getAllTagNames($extraData['id'] ?? null), 'The given value is already taken', true);

				if ($this->getPage() == 'tags/new') {
					$newTag = 0;
					$result = $this->submitRequest($validated, 'usp_InsertTag', '/tags', 'Tag successfully submitted!', $newTag);
				} else {
					$result = $this->submitRequest($validated, 'usp_UpdateTag', '/tags', 'Tag successfully updated!');
				}
				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}
		
		return $result;
	}
}
