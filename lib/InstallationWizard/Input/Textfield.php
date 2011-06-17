<?php
namespace InstallationWizard\Input;

/**
 * A basic textfield input.
 *
 * @author Manuel Alabor
 */
class Textfield extends \InstallationWizard\Input\Input {
	
	/**
	 * Default constructior with caption.
	 *
	 * @param $caption
	 */
	public function __construct($caption) {
		parent::__construct($caption);
	}
	
	/**
	 * Renders a textfield.
	 *
	 * @param $key
	 * @param $value value to fill in by default
	 * @return HTML code
	 */
	protected function renderInput($key, $value) {
		$rendered = '<input type="text" '
							.  'name="'. $key. '" '
							.  'id="'. $key. '" '
							.  'value="'. $value. '" '
							.  '/>';
		/*.  'placeholder="'. $placeholder. '" '
		.  'tabindex="'. $tabindex. '" '
		*/
							
		return $rendered;
	}
	
	/**
	 * Returns true, if the string length of $value is larger than zero
	 *
	 * @param $value
	 * @return true if strlen($value) > 0
	 */
	public function isValueValid($value) {
		return (strlen($value) > 0);
	}
	
}
?>