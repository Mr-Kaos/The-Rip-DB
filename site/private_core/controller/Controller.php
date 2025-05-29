<?php

namespace RipDB\Controller;

use RipDB\Model as m;

abstract class Controller
{
	private array $data = [];
	protected ?m\Model $model;
	private string $page;
	private ?string $pageTitleOverride = null;

	public function __construct(string $page, ?m\Model $model = null)
	{
		$this->model = $model;
		$this->page = $page;
	}

	protected function getPage(): string
	{
		return $this->page;
	}

	/**
	 * Sets some data to a key that can be retrieved in a view.
	 */
	protected function setData(string $key, mixed $value): void
	{
		$this->data[$key] = $value;
	}

	/**
	 * Retrieves a specific piece of data from the controller's data array.
	 * @param string $key The key in the Controller's data that the data is stored in.
	 */
	public function getData($key): mixed
	{
		return $this->data[$key] ?? null;
	}

	public function getPreparedData(): array
	{
		return $this->data;
	}

	public abstract function performRequest(array $data = []): void;

	/**
	 * Optional function used for submitting forms through.
	 * @return RipDB\Error[]|string Returns a URL to redirect to upon a successful submission. Else, if an error occurred, an array of each error encountered will be returned.
	 * 	This array is retrieved from the model.
	 */
	public function submitRequest(?array $extraData = null): array|string
	{
		throw (new \Exception("This controller's submitRequest function has not been initialised!"));
		return '';
	}

	/**
	 * Sets an override for the page's title in the head tag.
	 * @param string $title The text to use in the title.
	 */
	protected function setPageTitle(string $title)
	{
		$this->pageTitleOverride = $title;
	}

	/**
	 * Retrieves the override for the page's title in the head tag.
	 */
	public function getPageTitle(): ?string
	{
		return $this->pageTitleOverride;
	}
}
