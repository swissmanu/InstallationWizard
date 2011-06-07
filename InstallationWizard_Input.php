<?php

/**
 * 
 *
 * @author Manuel Alabor
 * @see InstallationWizard
 * @see InstallationWizard_Step
 */
class InstallationWizard_Input {
	
	private $caption = '';
	private $type = '';
	
	/**
	 * 
	 *
	 * @param $caption
	 * @param $type
	 */
	public function __construct($caption, $type) {
		$this->caption = $caption;
		$this->type = $type;
	}
	
}
?>