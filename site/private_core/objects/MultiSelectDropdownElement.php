<?php

namespace RipDB\Objects;

require_once('DropdownElement.php');

/**
 * Defines a dropdown element to be built in HTML
 */
class MultiSelectDropdownElement extends DropdownElement
{
	private array $selected = [];
	/**
	 * Sets up a dropdown element object.
	 * Checks if any options were given. If none were given, options can be added later via {@see DropdownElement/addOption()}, else the dropdown is automatically disabled.
	 * Also checks if a selected value was set for the dropdown. If a HTML attribute of 'value' or 'selected' is given, it sets the dropdown's value to the specified value, if it exists in its options. The 'selected' attribute takes precedence over 'value' if both are given.
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
		$attributes = $this->buildAttributes();

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
