<?php

namespace RipDB\Objects;

require_once('DropdownElement.php');

/**
 * Defines a dropdown element to be built in HTML
 */
class MultiSelectDropdownElement extends DropdownElement
{
	/**
	 * Constructs a dropdown where multiple options can be selected.
	 * @param ?string $label The label of the input element.
	 * @param ?array $options An array of values to use as options in the dropdown.
	 * @param ?array $attributes An associative array of html attributes to add to the element.
	 */
	public function __construct(string $label, ?array $options = [], ?array $attributes = [])
	{
		parent::__construct($label, $options, $attributes);
	}

	/**
	 * Builds the dropdown element using the $options property.
	 */
	public function buildElement(): string
	{
		$options = '';
		$attributes = $this->buildAttributes($this->attributes);

		$element = '<span class="multi-select"' . $attributes;
		if (count($this->options) === 0) {
			$element .= 'disabled><span>No options available</span>';
		} else {
			if ($this->disabled) {
				$element .= 'disabled>';
			} else {
				$element .= ">";
			}

			$isList = array_is_list($this->options);

			foreach ($this->options as $option => &$value) {
				if (is_array($value)) {
					$keys = array_keys($value);
					$option = $value[$keys[0]];
					$value = $value[$keys[1]];
					if ($isList) {
						$temp = $value;
						$value = $option;
						$option = $temp;
					}
				} else {
					if ($isList) {
						$temp = $value;
						$value = $option;
						$option = $temp;
					}
				}

				$options .= '<span>' . (new InputElement($option, InputTypes::checkbox, ['id' => $this->attributes['id'] . '-' . $value, 'name' => $this->attributes['id'] . '[]', 'value' => $value]))->buildElement() . '</span>';
			}
		}
		$element .= $this->buildLabel($this->getAttribute('required') ? true : false);
		$element .= '<div class="options">' . $options . '</div>';
		$element .= '</span>';

		return $element;
	}
}
