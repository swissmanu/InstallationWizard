<?php
namespace InstallationWizard\Input;

/**
 * A dropdown box to select different options.
 *
 * @author Manuel Alabor
 */
class Dropdown extends \InstallationWizard\Input\Input {
	
	private $choices = array();
	
	/**
	 * Default constructior with caption and choices.<br/>
	 * For $choices, pass an associative key/value-array.
	 *
	 * @param $caption
	 * @param $choices
	 */
	public function __construct($caption, array $choices) {
		parent::__construct($caption);
		$this->choices = $choices;
	}
	
	/**
	 * Renders the dropdown with all its options. Ensures, that the selected option
	 * gets selected again.
	 *
	 * @param $key
	 * @param $value value to fill in by default
	 * @return HTML code
	 */
	protected function renderInput($key, $value) {
		$rendered = '<select '
							.  'name="'. $key. '" '
							.  'id="'. $key. '" '
							.  '>';
							
		foreach($this->choices as $choiceKey => $choice) {
			$checked = '';
			if($choiceKey === $value) $checked = ' selected="selected"';
			$rendered .= '<option value="'. $choiceKey. '"'. $checked. '>'. $choice. '</option>';
		}
		
		$rendered .= '</select>';
							
		return $rendered;
	}
	
	/**
	 * Checks if a value is valid.
	 *
	 * @param $value
	 * @return true if $value is present in $this->choices
	 */
	public function isValueValid($value) {
		return in_array($value, $this->choices, true);
	}
	
}
?>