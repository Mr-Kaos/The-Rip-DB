<?php

namespace RipDB\Controller;

use RipDB\Model as m;

require_once('Controller.php');
require_once('private_core/model/APIModel.php');
require_once('private_core/objects/IAsyncHandler.php');

/**
 * @property \RipDB\Model\APIModel $model
 */
class APIController extends Controller implements \RipDB\Objects\IAsyncHandler
{
	public function __construct(string $page)
	{
		parent::__construct($page, new m\APIModel());
	}

	/**
	 * Performs the GET or POST request by the user.
	 * Generally a search request.
	 */
	public function performRequest(array $data = []): void {}

	public function get(string $method, ?string $methodGroup = null): mixed
	{
		$result = null;

		switch ($methodGroup) {
			case 'search':
				$result = $this->dropdownSearch($method);
				break;
			default:
				break;
		}

		return $result;
	}
	public function post(string $method, ?string $methodGroup = null): mixed
	{
		return null;
	}
	public function put(string $method, ?string $methodGroup = null): mixed
	{
		return null;
	}
	public function delete(string $method, ?string $methodGroup = null): mixed
	{
		return null;
	}

	/**
	 * 
	 */
	private function dropdownSearch($method): array
	{
		$rand = false;
		$search = '';
		if (array_key_exists('q', $_GET)) {
			$search = $_GET['q'];
		} else {
			$rand = true;
		}
		$result = [];
		switch ($method) {
			case 'tags':
				$result = $this->model->getRecords('Tags', 'TagID', 'TagName', $search, $rand);
				break;
			case 'metas':
				$result = $this->model->getRecords('Metas', 'MetaID', 'MetaName', $search, $rand);
				break;
			case 'jokes':
				$result = $this->model->getRecords('Jokes', 'JokeID', 'JokeName', $search, $rand);
				break;
			case 'meta-jokes':
				$result = $this->model->getRecords('MetaJokes', 'MetaJokeID', 'MetaJokeName', $search, $rand);
				break;
			case 'games':
				$result = $this->model->getRecords('Games', 'GameID', 'GameName', $search, $rand);
				break;
			case 'rippers':
				$result = $this->model->getRecords('Rippers', 'RipperID', 'RipperName', $search, $rand);
				break;
			case 'genres':
				$result = $this->model->getRecords('Genres', 'GenreID', 'GenreName', $search, $rand);
				break;
			case 'channels':
				$result = $this->model->getRecords('Channels', 'ChannelID', 'ChannelName', $search, $rand);
				break;
		}
		return $result;
	}
}
