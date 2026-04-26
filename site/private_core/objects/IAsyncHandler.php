<?php

namespace RipDB\Objects;

/**
 * Async Request Handler
 * 
 * Provides functions to be implemented in controllers that require asynchronous request support.
 */
interface IAsyncHandler
{
	/**
	 * Used for GET requests
	 */
	function get(string $method, ?array $data = null): mixed;
	/**
	 * Used for POST requests
	 */
	function post(string $method, ?array $data = null): mixed;
	/**
	 * Used for PUT requests
	 */
	function put(string $method, ?array $data = null): mixed;
	/**
	 * Used for DELETE requests
	 */
	function delete(string $method, ?array $data = null): mixed;
}
