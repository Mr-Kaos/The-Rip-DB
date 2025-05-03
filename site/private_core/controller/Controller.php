<?php

namespace RipDB;

abstract class Controller
{
	private array $data = [];
	protected ?Model $model;
	private string $page;

	public function __construct(string $page, ?Model $model = null)
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
}
