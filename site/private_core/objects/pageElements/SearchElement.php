<?php

namespace RipDB\Objects;

require_once('InputElement.php');
/**
 * Defines a search element to be built in HTML.
 * Search elements let the user search for text in the database using the given search path (API request)
 */
class SearchElement extends InputElement
{
	private ?string $url;
	private bool $multiSelect;
	private string|array|null $values;

	/**
	 * Sets up a searchable input element object.
	 * @param ?string $label The label of the input element.
	 * @param string $url The URL to query with the users input.
	 * @param bool $allowMultiSelect If true, allows multiple searches to be performed and have them as "selected" options, similar to the MultiSelectDropdownElement. Default is false - i.e. single value.
	 * @param ?array $value An associative array containing the IDs and text-value of the input. If $allowMultiSelect is true, then the value should be an array of key-pair values for each input.
	 * @param ?array $attributes An associative array of html attributes to add to the element.
	 * @param ?array $labelAttributes An associative array of html attributes to add to the element's label.
	 */
	public function __construct(string $label, string $url, bool $allowMultiSelect, ?array $value, ?array $attributes = [], ?array $labelAttributes = [])
	{
		$this->url = $url;
		$this->multiSelect = $allowMultiSelect;

		// If a multiselect, ensure the name attribute, if set ends with square braces
		if ($allowMultiSelect && array_key_exists('name', $attributes)) {
			if (!str_ends_with($attributes['name'], '[]')) {
				$attributes['name'] = $attributes['name'] . '[]';
			}
		}
		$this->values = $value;
		parent::__construct($label, InputTypes::custom, $attributes, $labelAttributes);
	}

	/**
	 * Sets the prefill value(s) for the input.
	 * @param ?array $value The value to prefill the element with.
	 */
	public function setValue(?array $value): void
	{
		$this->values = $value;
	}

	/**
	 * Builds the dropdown element using the $options property.
	 */
	public function buildElement(): string
	{
		$element = '<span id="search_' . $this->attributes['id'] . '" name="' . $this->attributes['name'] . '" class="search-element" type="' . ($this->multiSelect ? 'multi' : 'single') . '">';
		$element .= $this->buildLabel($this->getAttribute('required') ? true : false);

		$attributes = $this->attributes;
		$attributes['name'] = null;
		$attributes['search-url'] = $this->url;
		$element .= (new InputElement(null, InputTypes::search, $attributes))->buildElement();
		$element .= '<div class="options"></div>';
		if ($this->multiSelect) {
			$element .= '<div class="selected">';
			if (!empty($this->values)) {
				foreach ($this->values as $id => $val) {
					$element .= '<span class="pill">' . $val . '<input hidden="" name="' . $this->attributes['name'] . '" value="' . $id . '"><button type="button">×</button></span>';
				}
			}
			$element .= '</div>';
		} elseif (is_array($this->values)) {
			// if a value is given, make sure it is exactly one element in length.
			if (count($this->values) == 1) {
				$element .= '<span class="pill">' . $this->values[array_keys($this->values)[0]] . '<input hidden="" name="' . $this->attributes['name'] . '" value="' . array_keys($this->values)[0] . '" ><button type="button">×</button></span>';
			} else {
				throw (new \Exception('The value for a single-option SearchElement must be associative array with exactly one key-pair value!'));
			}
		}
		$element .= '</span>';

		return $element;
	}
}
