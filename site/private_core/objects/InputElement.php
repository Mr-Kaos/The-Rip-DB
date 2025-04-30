<?php

namespace RipDB\Objects;

use Exception;

require_once('PageElement.php');

enum InputTypes: string
{
	case button = 'button';
	case checkbox = "checkbox"; // If a value for an unchecked checkbox is needed, specify the key "value-alt" in the attributes parameter with the desired value. A name attribute must also be present.
	case color = "color";
	case date = "date";
	case datetime = "datetime";
	case datetimelocal = "datetime-local";
	case email = "email";
	case file = "file";
	case hidden = "hidden";
	case image = "image";
	case month = "month";
	case number = "number";
	case password = "password";
	case radio = "radio";
	case range = "range";
	case reset = "reset";
	case search = "search";
	case submit = "submit";
	case tel = "tel";
	case text = "text";
	case time = "time";
	case url = "url";
	case week = "week";
	case textarea = "textarea";
	case list = 'list';
	case dropdown = 'dropdown';
}

/**
 * This script defines the class used to build page objects dynamically.
 */
class InputElement extends PageElement
{
	private ?string $label;
	private InputTypes $type;

	/**
	 * If either a name and no id or id and no name attribute is given, it will copy the value of the given attribute to the missing one.
	 */
	public function __construct(?string $label, InputTypes $type, ?array $attributes = [])
	{
		// Ensure that an ID or name is set.
		$id = $attributes['id'] ?? null;
		$name = $attributes['name'] ?? $id;
		if (empty($id) && !empty($name)) {
			$attributes['id'] = $name;
		}
		$attributes['name'] = $name;

		parent::__construct($attributes);
		$this->type = $type;
		$this->label = $label;
	}

	public function __destruct()
	{
		return "PageElement destroyed.";
	}

	/** Builds a HTML label element.
	 * @param bool $required If true, an asterisk will be placed beside the label to indicate the field is required.
	 */
	protected function buildLabel(bool $required = false): string
	{
		$label = '';
		if ($this->type !== InputTypes::hidden) {
			$for = array_key_exists('id', $this->attributes) ? ' for="' . $this->attributes['id'] . '"' : '';
			$label = '<label' . $for . '>' . $this->label;
			if ($required) {
				$label .= ' *';
			}
			$label .= '</label>';
		}
		return $label;
	}

	/**
	 * Builds the container created by the class' functions
	 * @return String the HTML of the constructed element.
	 */
	public function buildElement(): string
	{
		$field = '';
		if (!is_null($this->label)) {
			$field .= $this->buildLabel(isset($this->attributes['required']));
		}
		$error = false;

		$id = $this->attributes['id'] ?? null;
		unset($this->attributes['id']);
		$value = isset($this->attributes['value']) ? $this->attributes['value'] : null;
		$valueAlt = null;
		if (array_key_exists('value-alt', $this->attributes)) {
			$valueAlt = $this->attributes['value-alt'];
			unset($this->attributes['value-alt']);
		}

		$attributes = $this->buildAttributes();

		if (!$error) {
			switch ($this->type) {
				case InputTypes::button:
					$field = '<button id="' . $id . '" type="button"' . "$attributes>$this->label</button>";
					break;
				case InputTypes::textarea:
					$field .= '<textarea id="' . $id . '" ' . $attributes . '>$value</textarea>';
					break;
				case InputTypes::checkbox:
					// If an alt value is specified, create the hidden checkbox.
					if ($valueAlt !== null) {
						if (empty($this->name)) {
							throw (new Exception('Checkboxes with an alternate (hidden) value must have a name attribute specified.'));
						}
						$field .= '<input id="hidden-' . $id . '" type="hidden"' . $attributes . ' value="' . $valueAlt . '">';
					}
					$field .= '<input id="' . $id . '" type="' . $this->type->name . '" ' . $value . "$attributes>";
					break;
				case InputTypes::list:
					$field .= '<input id="hidden-' . $id . '" type="hidden"' . $attributes . ' value="0">' . '<input id="' . $id . '" type="' . $this->type->name . '"' . "$attributes>";
					break;
				case InputTypes::datetime:
					$field .= '<input id="' . $id . '" type="datetime"' . $attributes . ">";
					break;
				case InputTypes::radio:
					$field .= '<input id="' . $id . '" type="radio"' . $attributes . ">";
					break;
				default:
					$field .= '<input id="' . $id . '" type="' . $this->type->value . '"' . "$attributes>";
			}
		}

		return $field;
	}
}
