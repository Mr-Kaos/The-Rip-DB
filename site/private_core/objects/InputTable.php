<?php

namespace RipDB\Objects;

require_once('InputElement.php');

use Exception;

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
		if (!array_key_exists('id', $attributes)) {
			$attributes['id'] = uniqid('InputTable_');
		}
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

		$html = '<table' . $attributes . '><caption>' . $this->label . '</caption><thead id="temp_' . $this->attributes['id'] . '" style="display:none"><tr>';

		$row = '';
		foreach ($this->columnTemplates as $col) {
			$row .= '<td>';
			// Check to ensure the column entry is an InputElement and not an InputTable
			if ($col instanceof InputTable) {
				throw (new Exception('You cannot nest an InputTable in another InputTable!'));
			}
			elseif ($col instanceof InputElement) {
				$col->setAttribute('form', '');
				$row .= $col->buildElement();
			} else {
				throw (new Exception('All columns in an InputTable must be an instance of an InputType element!'));
			}
			$row .= '</td>';
		}
		$row .= '<td>' . (new InputElement('Remove', InputTypes::button, ['disabled' => true, 'btnRemove' => '']))->buildElement() . '</td>';

		$html .= $row . '</tr></thead><tbody id="body_' . $this->attributes['id'] . '"></tbody><tfoot><tr><td colspan="' . count($this->columnTemplates) + 1 . '">';
		// Commented code below is for if support for nested InputTables is added. The issue with that at the moment is that the ID of the nested tables gets cloned when a new
		// row is added as well as any attached event listeners being removed from the clone, making it difficult to implement easily. Left this in for now in case I decide to work on this again.
		// $html .= (new InputElement('+', InputTypes::button, ['id' => 'add_' . $this->attributes['id'], 'onclick' => 'InputTable.addRow(\'' . $this->attributes['id'] . '\')']))->buildElement();
		$html .= (new InputElement('+', InputTypes::button, ['id' => 'add_' . $this->attributes['id']]))->buildElement();
		$html .= '</td></tr></tfoot></table>';

		return $html;
	}
}
