<?php
namespace InstallationWizard\Input;

/**
 * A checkbox.
 *
 * @author Manuel Alabor
 */
class Checkbox extends \InstallationWizard\Input\Input {
	
	private $trueIsValid = true;
	private $checkboxText = '';
	
	/**
	 * Default constructior with caption.
	 *
	 * @param $caption
	 */
	public function __construct($caption) {
		parent::__construct($caption);
	}
	
	/**
	 * Renders a checkbox.
	 *
	 * @param $key
	 * @param $value value to fill in by default
	 * @return HTML code
	 */
	protected function renderInput($key, $value) {
		$checked = '';
		if($this->convertValueToBoolean($value) === true) $checked = ' checked="checked"';
		
		$rendered = '<span class="checkboxcontainer">'
		          .  '<input type="checkbox" '
				 	 		.  'name="'. $key. '" '
		  	  	  .  'id="'. $key. '" '
				  		.  'value="1" '
				  		.  $checked
				  		.  '/> '
				  		.  $this->checkboxText
				  		.  '</span>';
							
		return $rendered;
	}
	
	/**
	 * By default, #isValueValid returns <code>true</code> if $value is <code>true</code>
	 * (or an equivalent like '1').<br/>
	 * If #trueIsValid was set to <code>false</code>, this functions determines
	 * that <code>$value</code> is only valid, if its value is <code>false</code>.
	 *
	 * @param $value
	 * @return true if strlen($value) > 0
	 */
	public function isValueValid($value) {
		$boolean = $this->convertValueToBoolean($value);
		return ($boolean === $this->trueIsValid);
	}
	
	/**
	 * Converts a string value to boolean.
	 *
	 * @param $value
	 * @return true/false
	 */
	private function convertValueToBoolean($value) {
		$boolean = false;
		if($value === '1' || $value === 'true' || $value === true) {
			$boolean = true;
		}
		
		return $boolean;
	}
	
	/**
	 * #setMandatory was used to set this checkbox to mondatory, this method defines
	 * if this checkbox needs to be checked or unchecked to be valid.
	 *
	 * @param $trueIsValid
	 **/
	public function setTrueIsValid($trueIsValid) {
		$this->trueIsValid = $trueIsValid;
	}
	
	/**
	 * Sets the text, which will be displayed to the right of the checkbox.
	 *
	 * @param $text
	 */
	public function setCheckboxText($text) {
		$this->checkboxText = $text;
	}
	
}
?>