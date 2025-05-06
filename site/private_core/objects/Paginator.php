<?php
trait Paginator
{
	const DEFAULT_ROWCOUNT = 25;

	protected function getRowCount()
	{
		return intval($_GET['c'] ?? self::DEFAULT_ROWCOUNT);
	}

	protected function getPageNumber()
	{
		return empty($_GET['p'] ?? null) ? 1 : (int)$_GET['p'];
	}

	/**
	 * @param int $recordCount The total number of records that exist in the table to be paginated
	 * @param string $redirect The page to redirect to if the given pagination is invalid, e.g. the requested page exceeds the number of records in the database.
	 */
	protected function getOffset(int $recordCount, string $redirect): int
	{
		$rowCount = $this->getRowCount();
		$page = $this->getPageNumber();
		$recordStart = (($page - 1) * $rowCount) + 1;

		// If the page number exceeds the number of rips, go to the highest page
		if ($recordStart > $recordCount) {
			$page = ceil($recordCount / $rowCount);
			$request = $_GET;
			if (!array_key_exists('p', $request)) {
				$request['p'] = 1;
			} else {
				$request['p'] = $page;
			}

			header("location:/$redirect?" . http_build_query($request));
			die();
		}
		return $rowCount * ($page - 1);
	}
}
