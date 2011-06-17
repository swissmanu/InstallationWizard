<?php
namespace InstallationWizard\Input;

/**
 * An abstract base class which holds information about an input-element for
 * an InstallationWizard_Step. 
 *
 * @author Manuel Alabor
 * @see InstallationWizard
 * @see InstallationWizard\Step
 */
abstract class Input {
	
	private $caption = '';
	
	/**
	 * Default constructior with caption.
	 *
	 * @param $caption
	 */
	public function __construct($caption) {
		$this->caption = $caption;
	}
	
	/**
	 * Returns the caption for this input-element.
	 *
	 * @return caption
	 */
	public function getCaption() {
		return $this->caption;
	}
	
	/**
	 * Sets the caption for this input-element.
	 * 
	 * @param $caption
	 */
	protected function setCaption($caption) {
		$this->caption = $caption;
	}
	
}
?>