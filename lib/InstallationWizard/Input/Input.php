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
	private $mandatory = false;
	private $showCaption = true;
	
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
	
	/**
	 * Returns true, if this field should be filled in.
	 *
	 * @return true/false
	 */
	public function isMandatory() {
		return $this->mandatory;
	}
	
	/**
	 * Defines if filling this input is mandatory or not.
	 *
	 * @param $mandatory true/false
	 */
	public function setMandatory($mandatory) {
		$this->mandatory = $mandatory;
	}
	
	/**
	 * Validates a $value.<br/>
	 * Has to be implemented by the concrete class.
	 *
	 * @param $value
	 * @return true/false
	 */
	public abstract function isValueValid($value);
	
	/**
	 * Returns the HTML source containing a caption and the rendered input element.
	 *
	 * @param $key
	 * @param $value;
	 */
	public function render($key, $value) {
		$inputKey = 'input_'. $key;
		$rendered = '';
		
		if($this->showCaption === true) $rendered .= $this->renderCaption($inputKey, $this->getCaption());
		$rendered .= $this->renderInput($inputKey, $value);
		
		return $rendered;
	}
	
	/**
	 * Renders a caption for this input.
	 *
	 * @param $key
	 * @param $caption
	 * @return HTML code
	 */
	private function renderCaption($key, $caption) {
		$rendered = '<label for="'. $key. '">'
						  .  $caption
						  .  '</label>';
		return $rendered;
	}
	
	/**
	 * Should this Input render a caption for itself?
	 *
	 * @param $show true/false
	 */
	protected function setShowCaption($show) {
		$this->showCaption = $show;
	}
	
	/**
	 * Renders this input element.<br/>
	 * Has to be implemented by the concrete class.
	 *
	 * @param $key
	 * @param $value
	 * @return HTML code
	 */
	protected abstract function renderInput($key, $value);
	
}
?>