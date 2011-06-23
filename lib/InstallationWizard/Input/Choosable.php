<?php
namespace InstallationWizard\Input;

/**
 * An abstract version of Input which allows to create Input-implementations to
 * select one specific value out of a collection of values.
 *
 * @author Manuel Alabor
 */
abstract class Choosable extends \InstallationWizard\Input\Input {
	
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
	 * Checks if a value is valid.
	 *
	 * @param $value
	 * @return true if $value is present in $this->choices
	 */
	public function isValueValid($value) {
		return in_array($value, $this->choices, true);
	}
	
	/**
	 * Returns the available choices.
	 *
	 * @return array
	 */
	protected function getChoices() {
		return $this->choices;
	}
	
}
?>