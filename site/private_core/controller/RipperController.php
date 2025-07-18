<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/RipperModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\RipperModel $model
 */
class RipperController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\RipperModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'rippers/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of rippers
				$offset = $this->getOffset($recordCount, '/rippers');
				$records = $this->model->search(
					$rowCount,
					$offset,
					null,
					$_GET['search'] ?? null,
				);

				$this->setData('results', $records);

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
				$this->setData('RecordCount', $recordCount);
				$this->setData('pagination', $this->buildPagination($recordCount, '/rippers'));
				break;
			case 'rippers/edit':
				$ripper = $this->model->getRipper($data['id']);
				if (empty($ripper)) {
					\RipDB\addNotification('That ripper does not exist.', \RipDB\NotificationPriority::Warning);
					\Flight::redirect('/rippers');
					die();
				}
				$this->setData('ripper', $ripper);
				break;
		}
	}

	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'rippers/new':
				$name = $this->validateString($_POST['name'], 'The ripper name is invalid.', 256);
				$validated['NewRipper'] = $this->validateFromList($name, $this->model->getAllRipperNames(), 'This ripper already exists.', true);

				$ripperId = 0;
				$result = $this->submitRequest($validated, 'usp_InsertRipper', '/rippers', 'Ripper successfully added!', $ripperId);
				break;
			case 'rippers/edit':
				$validated['InRipperID'] = $this->validateNumber($extraData['id']);
				$name = $this->validateString($_POST['name']);
				$validated['InRipperName'] = $this->validateFromList($name, $this->model->getAllRipperNames((int)$extraData['id']), 'This ripper already exists.', true);

				$result = $this->submitRequest($validated, 'usp_UpdateRipper', '/rippers', 'Ripper successfully updated!');
				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}

		return $result;
	}
}
