<?php

namespace RipDB\Objects;

require_once('InputElement.php');

use Exception;
use WarRoom\PageBuilder as pb;

/**
 * InputTable class
 * 
 * An InputTable is a table that can have more rows added to it.
 * Each column must be defined when constructing it, and can be an array of HTML strings or InputElements.
 * 
 */
class InputTable extends InputElement
{
	protected array $columnTemplates;

	/**
	 * @param ?string $title The title (label) of the InputTable object.
	 * @param array $columnTemplates An array of InputElements to add for each column in a row.
	 * @param ?array $attributes An associative array of html attributes to add to the element.
	 */
	function __construct(?string $title, array $columnTemplates, ?array $attributes = [])
	{
		$attributes['InputTable'] = '';
		$this->columnTemplates = $columnTemplates;
		parent::__construct($title, InputTypes::custom, $attributes);
	}

	/**
	 * Builds the multi element and the input element.
	 * 
	 * JavaScript will apply event listeners to the various buttons on the table.
	 */
	public function buildElement(): string
	{
		$attributes = $this->buildAttributes($this->attributes);

		$html = '<table' . $attributes . '><thead style="display:none"><tr>';

		$row = '';
		foreach ($this->columnTemplates as $col) {
			$row .= '<td>';
			// Check to ensure the column entry is an InputElement
			if ($col instanceof InputElement) {
				$col->setAttribute('form', '');
				$row .= $col->buildElement();
			} else {
				throw (new Exception('All columns in an InputTable must be an instance of an InputType element!'));
			}
			$row .= '</td>';
		}
		$row .= '<td>' . (new InputElement('Remove', InputTypes::button, ['disabled' => true, 'btnRemove' => '']))->buildElement() . '</td>';

		$html .= $row . '</tr></thead><tbody></tbody><tfoot><tr><td colspan="' . count($this->columnTemplates) + 1 . '">';
		$html .= (new InputElement('+', InputTypes::button))->buildElement();
		$html .= '</td></tr></tfoot></table>';

		return $html;
	}
}
