<?php
namespace InstallationWizard\Input;

/**
 * A basic textfield input.
 *
 * @author Manuel Alabor
 */
class Textfield extends Input {
	
	/**
	 * Default constructior with caption.
	 *
	 * @param $caption
	 */
	public function __construct($caption) {
		parent::__construct($caption);
	}
	
}
?>