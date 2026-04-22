<?php

namespace RipDB\Controller;

use RipDB\Model as m;
use RipDB\DataValidator;

require_once('Controller.php');
require_once('private_core/model/PlatformModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\PlatformModel $model
 */
class PlatformController extends Controller
{
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\PlatformModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'platforms/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of rips
				$offset = $this->getOffset($recordCount, '/platforms');
				$platforms = $this->model->search(
					$rowCount,
					$offset,
					null,
					$_GET['search'] ?? null,
				);

				DataValidator::cleanseDatabaseDataForOutput($platforms);
				$this->setData('results', $platforms);

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
				$this->setData('PlatformCount', $recordCount);
				$this->setData('pagination', $this->buildPagination($recordCount, '/platforms'));
				break;
			case 'platforms/edit':
				$platform = $this->model->getPlatform($data['id']);
				if ($platform === null) {
					\Flight::redirect('/platforms');
					\RipDB\addNotification('The specified platform does not exist.', \RipDB\NotificationPriority::Warning);
					die();
				}
				DataValidator::cleanseDatabaseDataForOutput($platform);

				$this->setData('platform', $platform);
				break;
		}
	}

	/**
	 * Validates the submission of forms through the platforms pages.
	 */
	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'platforms/edit':
				$validated['InPlatformID'] = DataValidator::validateNumber($extraData['id']);
			case 'platforms/new':
				$validated['InPlatformName'] = DataValidator::validateFromList($_POST['name'], $this->model->getAllPlatformNames($extraData['id'] ?? null), 'The given value is already taken', true);

				if ($this->getPage() == 'platforms/new') {
					$newPlatform = 0;
					$result = $this->submitRequest($validated, 'usp_InsertPlatform', '/platforms', 'Platform successfully submitted!', $newPlatform);
				} else {
					$result = $this->submitRequest($validated, 'usp_UpdatePlatform', '/platforms', 'Platform successfully updated!');
				}
				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}

		return $result;
	}
}
