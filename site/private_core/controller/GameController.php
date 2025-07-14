<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/GameModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\GameModel $model
 */
class GameController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	const RIPS_PER_PAGE = 25;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\GameModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'games/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of games
				$offset = $this->getOffset($recordCount, '/games');
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
				$this->setData('pagination', $this->buildPagination($recordCount, '/games'));
				break;
			case 'games/edit':
				$game = $this->model->getGame($data['GameID']);
				if (empty($game)) {
					\RipDB\addNotification('That game does not exist.', \RipDB\NotificationPriority::Warning);
					\Flight::redirect('/games');
					die();
				}
				$this->setData('game', $game);
				break;
		}
	}

	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'games/new':
				$validated['NewGame'] = $this->validateFromList($_POST['name'], $this->model->getAllGameNames(), 'This game already exists.', true);
				$validated['NewDescription'] = $this->validateString($_POST['description']);
				$validated['FakeGame'] = $this->validateBool($_POST['isFake'] ?? false);

				$gameId = 0;
				$result = $this->submitRequest($validated, 'usp_InsertGame', '/games', 'Game successfully added!', $gameId);
				break;
			case 'games/edit':
				$validated['GameID'] = $this->validateNumber($extraData['id']);
				$validated['NewGame'] = $this->validateFromList($_POST['name'], $this->model->getAllGameNames((int)$extraData['id']), 'This game already exists.', true);
				$validated['NewDescription'] = $this->validateString($_POST['description']);
				$validated['FakeGame'] = $this->validateBool($_POST['isFake'] ?? false);

				$result = $this->submitRequest($validated, 'usp_UpdateGame', '/games', 'Game successfully updated!');
				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}

		return $result;
	}
}
