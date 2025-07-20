<?php

namespace RipDB\Controller;

use RipDB\Model as m;

abstract class Controller
{
	private array $data = [];
	protected ?m\Model $model;
	private string $page;
	private ?string $pageTitleOverride = null;

	const SUBMIT_RESPONSE_ID = '_NewID';
	const SUBMIT_RESPONSE_MSG = '_Message';
	const SUBMIT_RESPONSE_ERR = '_Error';

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
	public function validateRequest(?array $extraData = null): array|string
	{
		throw (new \Exception("This controller's validateRequest function has not been initialised!"));
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

	/**
	 * Submits the request to the controller's model. The data should be validated before sending it through this function.
	 * @param array $validatedData An associative array that contains the validated submission data from the controller's {@see validateRequest} function.
	 * @param string $storedProcedure The name of the SQL stored procedure that will send the data to the database.
	 * @param string $redirectLink The URI in the site to redirect to upon successful submission to the database.
	 * @param string $successMessage A message to display to the user when the submission to the database is successful.
	 * @param mixed &$outputParam An output parameter to use in the stored procedure if a value needs to be returned.  
	 * The Output parameter will always be set the last parameter if it is not null, so the stored procedure being executed must have an OUT parameter as the last parameter.
	 * 
	 * @return false|array|string If submission fails, False is returned. If successful, depending on whether an HTTP_ACCEPT header is sent or not will determine if an array or string is returned.  
	 * - If HTTP_ACCEPT is sent, an array of the validated data submitted is returned, along with an ID of the record (if applicable) and a success message.
	 * - If no HTTP_ACCEPT is sent, a URI is returned to indicate where the user should be redirected to.
	 */
	protected function submitRequest(array $validatedData, string $storedProcedure, string $redirectLink, string $successMessage = "Request successfully submitted!", mixed &$outputParam = null): false|array|string
	{
		$result = [];

		$submission = $this->model->submitFormData($validatedData, $storedProcedure, $outputParam);
		if ($submission === true) {
			// If the sender sent an ACCEPT header, ensure that the submitted data is returned.
			if (($_SERVER['HTTP_ACCEPT'] ?? null) == 'application/json') {
				$result = $validatedData;
				$result[self::SUBMIT_RESPONSE_ID] = $outputParam;
				$result[self::SUBMIT_RESPONSE_MSG] = $successMessage;
			} else {
				\RipDB\addNotification($successMessage, \RipDB\NotificationPriority::Success);
				$result = $redirectLink;
			}
		} else {
			// If an accept header is sent, send the error messages as a single string. Else, create Notifications for each error.
			if (($_SERVER['HTTP_ACCEPT'] ?? null) == 'application/json') {
				$msg = "Failed to submit request";
				foreach ($submission as $error) {
					$msg .= "\n" . $error->getMessage();
				}
				$result = [self::SUBMIT_RESPONSE_ERR => $msg];
			} else {
				foreach ($submission as $error) {
					\RipDB\addNotification($error->getMessage(), $error->getPriority());
				}
				$result = false;
			}
		}

		return $result;
	}
}
