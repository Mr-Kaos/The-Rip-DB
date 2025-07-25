<?php
trait Paginator
{
	const DEFAULT_ROWCOUNT = 25;
	const PAGINATION_LINKS = 3;

	/**
	 * @return int The number of rows to be displayed
	 */
	protected function getRowCount(): int
	{
		return intval($_GET['c'] ?? self::DEFAULT_ROWCOUNT);
	}

	/**
	 * @return int The page number of the paginator
	 */
	protected function getPageNumber(): int
	{
		$page =  empty($_GET['p'] ?? null) ? 1 : (int)$_GET['p'];
		return $page > 0 ? $page : 1;
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
		$request = $_GET;
		if ($recordStart > $recordCount && $recordCount != 0) {
			$page = ceil($recordCount / $rowCount);
			if (!array_key_exists('p', $request)) {
				$request['p'] = 1;
			} else {
				$request['p'] = $page;
			}

			Flight::redirect("$redirect?" . http_build_query($request));
		}
		return $rowCount * ($page - 1);
	}

	/**
	 * Builds up a set of pagination links for insertion in the footer of a table.
	 * @param int $recordCount The total number of records available to the table across all pages.
	 * @param string $page The URI of the page to use in the links. Should be the the URI of the current page 
	 */
	protected function buildPagination(int $recordCount, string $page)
	{
		$rowCount = $this->getRowCount();
		$pageNum = $this->getPageNumber();
		$pages = ceil($recordCount / $rowCount);
		$pages = ($pages == 0) ? 1 : $pages;
		$request = $_GET;

		$request['p'] = 1;
		$pagination = '<a href="' . $page . '?' . http_build_query($request) . '">«</a>';

		$remainder = 0;
		$min = $pageNum - self::PAGINATION_LINKS;
		if ($min < 1) {
			$remainder = ($min * -1) + 1;
			$min = 1;
		}
		$max = $pageNum + self::PAGINATION_LINKS + $remainder;
		if ($max >= $pages) {
			$min -= ($max - $pages);
			$max = $pages;
			if ($min < 1) {
				$min = 1;
			}
		}

		for ($min; $min <= $max; $min++) {
			$request['p'] = $min;
			if ($pageNum == $min) {
				$pagination .= '<a href="' . $page . '?' . http_build_query($request) . '"><b>' . $min . '</b></a>';
			} else {
				$pagination .= '<a href="' . $page . '?' . http_build_query($request) . '">' . $min . '</a>';
			}
		}
		$request['p'] = $pages;
		$pagination .= '<a href="' . $page . '?' . http_build_query($request) . '">»</a>';

		return $pagination;
	}
}
