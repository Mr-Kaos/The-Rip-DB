<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/MetaJokeModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\MetaJokeModel $model
 */
class MetaJokeController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\MetaJokeModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'meta-jokes/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of jokes
				$records = [];
				$offset = $this->getOffset($recordCount, '/meta-jokes');
				$records = $this->model->search(
					$rowCount,
					$offset,
					null,
					$_GET['search'] ?? null,
					$_GET['metas'] ?? [],
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
				$this->setData('pagination', $this->buildPagination($recordCount, '/meta-jokes'));

				$this->setData('metas', $this->model->getFilterResults('Meta', $_GET['metas'] ?? []));
				// If any filter is given, make sure the details element is open by setting its "open" attribute
				if (!empty($_GET['metas'] ?? null)) {
					$this->setData('open', 'open');
				} else {
					$this->setData('open', '');
				}

				break;
			case 'meta-jokes/new':
				break;
			case 'meta-jokes/edit':
				$metaJoke = $this->model->getMetaJoke($data['id']);
				if (empty($metaJoke)) {
					\RipDB\addNotification('That meta joke does not exist.', \RipDB\NotificationPriority::Warning);
					\Flight::redirect('/meta-jokes');
					die();
				}
				$this->setData('metaJoke', $metaJoke);
				break;
		}
	}

	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		$validated = [];
		// Validate data in order of stored procedure parameters.
		switch ($this->getPage()) {
			case 'meta-jokes/edit':
				$validated['InJokeID'] = $this->validateNumber($extraData['id']);
			case 'meta-jokes/new':
				$validated['InName'] = $this->validateString($_POST['name'], 'The given name is invalid.', 128);
				$validated['InDescription'] = $this->validateString($_POST['description'], 'The given description is invalid.', null, 1);
				$validated['InMetaID'] = $this->validateFromList($_POST['meta'], $this->model->getMetas(), 'The given meta does not exist in the database.');

				switch ($this->getPage()) {
					case 'meta-jokes/new':
						$jokeId = 0;
						$result = $this->submitRequest($validated, 'usp_InsertMetaJoke', '/meta-jokes', 'Meta Joke successfully added!', $jokeId);
						break;
					case 'meta-jokes/edit':
						$result = $this->submitRequest($validated, 'usp_UpdateMetaJoke', '/meta-jokes', 'Meta Joke successfully updated!');
						break;
				}
				break;
		}
		return $result;
	}
}
