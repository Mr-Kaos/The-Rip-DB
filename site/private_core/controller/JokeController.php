<?php

namespace RipDB\Controller;

use RipDB\Model as m;
use RipDB\DataValidator;

require_once('Controller.php');
require_once('private_core/model/JokeModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\JokeModel $model
 */
class JokeController extends Controller
{
	use \Paginator;

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
			case 'jokes/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of jokes
				$jokes = [];
				$offset = $this->getOffset($recordCount, '/jokes');
				$jokes = $this->model->search(
					$rowCount,
					$offset,
					null,
					$_GET['search'] ?? null,
					$_GET['tags'] ?? [],
					$_GET['metas'] ?? [],
					$_GET['metajokes'] ?? [],
				);

				DataValidator::cleanseDatabaseDataForOutput($jokes);
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

				$this->setData('metas', $this->model->getFilterResults('Meta', $_GET['metas'] ?? []));
				$this->setData('metaJokes', $this->model->getFilterResults('MetaJoke', $_GET['metajokes'] ?? []));
				// If any filter is given, make sure the details element is open by setting its "open" attribute
				if (!empty($_GET['metas'] ?? null)) {
					$this->setData('open', 'open');
				} else {
					$this->setData('open', '');
				}

				break;
			case 'jokes/edit':
				$this->setData('tags', $this->model->getTags());
				if (isset($data['id']) && is_numeric($data['id'])) {
					$joke = $this->model->getJoke($data['id']);
					if (empty($joke)) {
						\RipDB\addNotification('That joke does not exist.', \RipDB\NotificationPriority::Warning);
						\Flight::redirect('/jokes');
						die();
					}
					DataValidator::cleanseDatabaseDataForOutput($joke);

					$this->setData('joke', $joke);
					$this->setData('heading', 'Edit Joke');
				} else {
					$this->setData('heading', 'New Joke');
				}
				break;
		}
	}

	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		$validated = [];
		// Validate data in order of stored procedure parameters.
		switch ($this->getPage()) {
			case 'jokes/edit':
				$id = $extraData['id'] ?? null;
				if ($id !== null) {
					$validated['InJokeID'] = DataValidator::validateNumber($extraData['id']);
				}
				$validated['InJokeName'] = DataValidator::validateString($_POST['name'], 'The given joke name is invalid.', 128);
				$validated['InJokeDescription'] = DataValidator::validateString($_POST['description'], 'The given description is invalid.', null);

				if (isset($_POST['primary']) && is_numeric($_POST['primary'])) {
					$existingTags = $this->model->getTags();
					$validated['PrimaryTag'] = DataValidator::validateFromList(intval($_POST['primary']), $existingTags, 'The selected primary tag does not exist in the database.');
				} else {
					$validated['PrimaryTag'] = null;
				}
				if (!isset($existingTags)) {
					$existingTags = $this->model->getTags();
				}
				$validated['TagsJSON'] = DataValidator::validateArray($_POST['tags'] ?? null, 'validateFromList', [$existingTags], 'One or more of the given tags do not exist in the database.', true);

				$existingMetas = $this->model->getMetaJokes();
				$validated['MetasJSON'] = DataValidator::validateArray($_POST['metas'] ?? null, 'validateFromList', [$existingMetas], 'One or more of the given meta tags do not exist in the database.', true);

				$validated['AlternateJokeNames'] = DataValidator::validateArray($_POST['alt_names'] ?? null, 'validateString', [512], 'One or more of the alternate names are not valid.', true);

				if ($id == null) {
					$jokeId = 0;
					$result = $this->submitRequest($validated, 'usp_InsertJoke', '/jokes', 'Joke successfully added!', $jokeId);
				} else {
					$result = $this->submitRequest($validated, 'usp_UpdateJoke', '/jokes', 'Joke successfully updated!');
				}
				break;
		}
		return $result;
	}
}
