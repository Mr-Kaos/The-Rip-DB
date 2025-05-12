<?php

namespace RipDB\Objects;

use Exception;

require_once('PageObject.php');

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
	case tel = "tel";
	case text = "text";
	case time = "time";
	case url = "url";
	case week = "week";
	case textarea = "textarea";
	case list = 'list';
	case dropdown = 'dropdown';
	case custom = 'custom';
}

/**
 * This script defines the class used to build page objects dynamically.
 */
class InputElement extends PageObject
{
	protected ?string $label;
	private array $labelAttributes;
	private InputTypes $type;

	/**
	 * If either a name and no id or id and no name attribute is given, it will copy the value of the given attribute to the missing one.
	 * @param ?string $label The label of the input element.
	 * @param InputTypes $type The InputType of the element.
	 * @param ?array $attributes An associative array of html attributes to add to the element.
	 * @param ?array $labelAttributes An associative array of html attributes to add to the element's label.
	 */
	public function __construct(?string $label, InputTypes $type, ?array $attributes = [], ?array $labelAttributes = [])
	{
		$ignoreId = false;
		$ignoreName = false;

		// Ensure that an ID or name is set. However, if the value is explicity null, do not set it.
		if (array_key_exists('name', $attributes) && ($attributes['name'] ?? null) === null) {
			unset($attributes['name']);
			$ignoreName = true;
		}
		if (array_key_exists('id', $attributes) && ($attributes['id'] ?? null) == null) {
			unset($attributes['id']);
			$ignoreId = true;
		}

		$id = $attributes['id'] ?? null;
		$name = $attributes['name'] ?? $id;
		if (empty($id) && !empty($name) && !$ignoreId) {
			$attributes['id'] = $name;
		}
		if (empty($attributes['name']) && !empty($id) && !$ignoreName) {
			$attributes['name'] = $name;
		}

		parent::__construct($attributes);
		$this->type = $type;
		$this->label = $label;
		$this->labelAttributes = $labelAttributes;
	}

	public function __destruct()
	{
		return "PageObject destroyed.";
	}

	/**
	 * Gets the input element type.
	 */
	public function getType(): InputTypes
	{
		return $this->type;
	}

	/** Builds a HTML label element.
	 * @param bool $required If true, an asterisk will be placed beside the label to indicate the field is required.
	 */
	protected function buildLabel(bool $required = false): string
	{
		$label = '';
		if ($this->type !== InputTypes::hidden) {
			$for = array_key_exists('id', $this->attributes) ? ' for="' . $this->attributes['id'] . '" ' : ' ';
			$attributes = $this->buildAttributes($this->labelAttributes);
			$label = '<label' . $for . $attributes . '>';
			if ($required) {
				$label .= '*';
			}
			$label .= $this->label . '</label>';
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
		$label = '';
		if (!is_null($this->label)) {
			$label = $this->buildLabel(isset($this->attributes['required']));
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

		$attributes = $this->buildAttributes($this->attributes);

		if (!$error) {
			switch ($this->type) {
				case InputTypes::button:
					$type = '';
					if (!array_key_exists('type', $this->attributes)) {
						$type = 'type="submit"';
					}
					$field = '<button id="' . $id . '" ' . $type . "$attributes>$this->label</button>";
					break;
				case InputTypes::textarea:
					$field = '<textarea id="' . $id . '" ' . $attributes . '>' . $value . '</textarea>';
					break;
				case InputTypes::checkbox:
					// If an alt value is specified, create the hidden checkbox.
					if ($valueAlt !== null) {
						if (empty($this->name)) {
							throw (new Exception('Checkboxes with an alternate (hidden) value must have a name attribute specified.'));
						}
						$field = '<input id="hidden-' . $id . '" type="hidden"' . $attributes . ' value="' . $valueAlt . '">';
					}
					$field = '<input id="' . $id . '" type="' . $this->type->name . '" ' . $value . "$attributes>";
					break;
				case InputTypes::list:
					$field = '<input id="hidden-' . $id . '" type="hidden"' . $attributes . ' value="0">' . '<input id="' . $id . '" type="' . $this->type->name . '"' . "$attributes>";
					break;
				case InputTypes::datetime:
					$field = '<input id="' . $id . '" type="datetime"' . $attributes . ">";
					break;
				case InputTypes::radio:
					$field = '<input id="' . $id . '" type="radio"' . $attributes . ">";
					break;
				default:
					$field = '<input id="' . $id . '" type="' . $this->type->value . '"' . "$attributes>";
			}
		}

		// Some elements require the label to be placed after the input
		switch ($this->type) {
			case InputTypes::checkbox:
			case InputTypes::radio:
				$field = $field . $label;
				break;
			case InputTypes::button:
				break;
			default:
				$field = $label . $field;
				break;
		}

		return $field;
	}
}
