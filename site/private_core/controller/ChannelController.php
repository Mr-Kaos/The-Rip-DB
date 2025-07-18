<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/ChannelModel.php');
require_once('private_core/objects/pageElements/Paginator.php');
require_once('private_core/objects/DataValidators.php');

/**
 * @property \RipDB\Model\ChannelModel $model
 */
class ChannelController extends Controller
{
	use \RipDB\DataValidator;
	use \Paginator;

	public function __construct(string $page)
	{
		parent::__construct($page, new m\ChannelModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void
	{
		switch ($this->getPage()) {
			case 'channels/search':
				$recordCount = $this->model->getCount();
				$rowCount = $this->getRowCount();
				$page = $this->getPageNumber();

				// Get records of channels
				$offset = $this->getOffset($recordCount, '/channels');
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
				$this->setData('pagination', $this->buildPagination($recordCount, '/channels'));
				break;
			case 'channels/edit':
				$channel = $this->model->getChannel($data['id']);
				if (empty($channel)) {
					\RipDB\addNotification('That channel does not exist.', \RipDB\NotificationPriority::Warning);
					\Flight::redirect('/channels');
					die();
				}
				$this->setData('channel', $channel);
				break;
		}
	}

	public function validateRequest(?array $extraData = null): array|string
	{
		$result = [];
		switch ($this->getPage()) {
			case 'channels/new':
				$validated['NewChannel'] = $this->validateFromList($_POST['name'], $this->model->getAllChannelNames(), 'This channel already exists.', true);
				$validated['NewDescription'] = $this->validateString($_POST['description']);
				$validated['NewURL'] = $this->validateString($_POST['url'] ?? false, 'The URL must be a valid YouTube URL!', 512, 16, '/^https:\/{2}[w]{0,3}[youtube.com][a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}.*$/');

				$channelId = 0;
				$result = $this->submitRequest($validated, 'usp_InsertChannel', '/channels', 'Channel successfully added!', $channelId);
				break;
			case 'channels/edit':
				$validated['InChannelID'] = $this->validateNumber($extraData['id']);
				$validated['InChannel'] = $this->validateFromList($_POST['name'], $this->model->getAllChannelNames((int)$extraData['id']), 'This channel already exists.', true);
				$validated['InDescription'] = $this->validateString($_POST['description']);
				$validated['InURL'] = $this->validateString($_POST['url'] ?? false);

				$result = $this->submitRequest($validated, 'usp_UpdateChannel', '/channels', 'Channel successfully updated!');
				break;
			default:
				$result = [new \RipDB\Error('Invalid form submission.')];
				break;
		}

		return $result;
	}
}
