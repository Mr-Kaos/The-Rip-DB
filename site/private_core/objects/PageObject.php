<?php

namespace RipDB\Objects;

/**
 * This script defines the class used to build page objects dynamically.
 */

abstract class PageObject
{
	protected array $attributes;

	/**
	 * Base constructor for a page object.
	 * @param ?array $attributes An associative array of html attributes to add to the element.
	 */
	public function __construct(array $attributes = [])
	{
		$this->attributes = $attributes;
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
	public function setAttribute(string $name, string | int $value): void
	{
		$this->attributes[$name] = $value;
	}

	/**
	 * Build a string of all attributes for use in the element.
	 * @param array $attributes An override for attributes to use. If null, uses $this->attributes.
	 * @return string
	 */
	protected function buildAttributes(array $attributes): string
	{
		$attrs = '';

		foreach ($attributes as $key => $val) {
			if (is_bool($val) && $val == true) {
				$attrs .= ' ' . $key . '="' . $val . '"';
			} elseif (!is_bool($val)) {
				$attrs .= ' ' . $key . '="' . $val . '"';
			}
		}
		return $attrs;
	}

	/**
	 * Builds the container created by the class' functions.
	 * @return string - The generated HTML of the element.
	 */
	abstract public function buildElement(): string;
}
