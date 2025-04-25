<?php

namespace RipDB\Objects;

/**
 * This script defines the class used to build page objects dynamically.
 */

abstract class PageElement
{
	protected array $attributes;

	public function __construct(array $attributes = [])
	{
		$this->attributes = $attributes;
	}

	public function __destruct()
	{
		return "PageElement destroyed.";
	}

	/**
	 * Retrieves the value of the specified attribute if it exists.
	 * @param string $attributeName The name of the element's attribute to retrieve its value for.
	 * @return mixed The value of the attribute. If not found, returns null.
	 */
	public function getAttribute(string $attributeName): mixed
	{
		return isset($this->attributes[$attributeName]) ? $this->attributes[$attributeName] : null;
	}

	/**
	 * Checks if the specified attribute exists.
	 * @param string $attributeName The name of the element's attribute to retrieve its value for.
	 * @return bool True if the attribute exists.
	 */
	public function checkAttribute(string $attributeName): mixed
	{
		return isset($this->attributes[$attributeName]);
	}

	/**
	 * Adds the specified attribute to the element.
	 * @param string $name The name of the attribute to add to the input.
	 * @param string|int $value The value to be assigned to the attribute.
	 */
	public function addAttribute(string $name, string | int $value): void
	{
		$this->attributes[$name] = $value;
	}

	/**
	 * Build a string of all attributes for use in the element.
	 * @return string
	 */
	protected function buildAttributes(): string
	{
		$attrs = '';
		foreach ($this->attributes as $key => $val) {
			$attrs .= ' $key="' . $val . '"';
		}
		return $attrs;
	}

	/**
	 * Builds the container created by the class' functions.
	 * @return string - The generated HTML of the element.
	 */
	abstract public function buildElement(): string;
}
