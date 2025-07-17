<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/JokeModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
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
			case 'jokes/new':
				$this->setData('tags', $this->model->getTags());
				break;
			case 'jokes/edit':
				$joke = $this->model->getJoke($data['id']);
				if (empty($joke)) {
					\RipDB\addNotification('That joke does not exist.', \RipDB\NotificationPriority::Warning);
					\Flight::redirect('/jokes');
					die();
				}
				$this->setData('joke', $joke);
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
				$validated['InJokeID'] = $this->validateNumber($extraData['id']);
			case 'jokes/new':
				$validated['InJokeName'] = $this->validateString($_POST['name'], 'The given joke name is invalid.', 128);
				$validated['InJokeDescription'] = $this->validateString($_POST['description'], 'The given description is invalid.', null, 1);

				$existingTags = $this->model->getTags();
				$validated['PrimaryTag'] = $this->validateFromList(intval($_POST['primary']), $existingTags, 'The selected primary tag does not exist in the database.');
				$validated['TagsJSON'] = $this->validateArray($_POST['tags'] ?? null, 'validateFromList', [$existingTags], 'One or more of the given tags do not exist in the database.');

				$existingMetas = $this->model->getMetaJokes();
				$validated['MetasJSON'] = $this->validateArray($_POST['metas'] ?? null, 'validateFromList', [$existingMetas], 'One or more of the given meta tags do not exist in the database.');

				switch ($this->getPage()) {
					case 'jokes/new':
						$jokeId = 0;
						$result = $this->submitRequest($validated, 'usp_InsertJoke', '/jokes', 'Joke successfully added!', $jokeId);
						break;
					case 'jokes/edit':
						$result = $this->submitRequest($validated, 'usp_UpdateJoke', '/jokes', 'Joke successfully updated!');
						break;
				}
				break;
		}
		return $result;
	}
}
