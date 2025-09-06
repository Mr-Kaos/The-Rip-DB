<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/ComposerModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\ComposerModel $model
 */
class ComposerController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\ComposerModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'composers/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of rips
				$offset = $this->getOffset($recordCount, '/composers');
				$composers = $this->model->search(
					$rowCount,
					$offset,
					null,
					$_GET['search'] ?? null,
				);

				$this->setData('results', $composers);

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
				$this->setData('ComposerCount', $recordCount);
				$this->setData('pagination', $this->buildPagination($recordCount, '/composers'));
				break;
			case 'composers/edit':
				$composer = $this->model->getComposer($data['id']);
				if ($composer === null) {
					\Flight::redirect('/composers');
					\RipDB\addNotification('The specified composer does not exist.', \RipDB\NotificationPriority::Warning);
					die();
				}
				$this->setData('composer', $composer);

				break;
		}
	}

	/**
	 * Validates the submission of forms through the composers pages.
	 */
	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'composers/edit':
				$validated['InComposerID'] = $this->validateNumber($extraData['id']);
			case 'composers/new':
				$validated['FirstName'] = $this->validateString($_POST['first-name'], 'The first name is invalid.', 256, 1);
				$validated['LastName'] = $this->validateString($_POST['last-name'], 'The last name is invalid.', 256);
				$validated['FirstNameAlt'] = $this->validateString($_POST['first-name-alt'], 'The alt first name is invalid.', 256);
				$validated['LastNameAlt'] = $this->validateString($_POST['last-name-alt'], 'The alt last name is invalid.', 256);

				if ($this->getPage() == 'composers/new') {
					$newComposer = 0;
					$result = $this->submitRequest($validated, 'usp_InsertComposer', '/composers', 'Composer successfully added!', $newComposer);
				} else {
					$result = $this->submitRequest($validated, 'usp_UpdateComposer', '/composers', 'Composer successfully updated!');
				}
				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}
		
		return $result;
	}
}
