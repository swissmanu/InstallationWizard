<?php
namespace InstallationWizard;

require_once('InstallationWizard.php');

/**
 * Holds all information about a particular InstallationWizard step.<br/>
 *
 * @author Manuel Alabor
 * @see InstallationWizard
 */
abstract class Step {
	
	private $wizard = null;
	private $title = '';
	private $text = '';
	private $inputs = array();
	private $nextButtonCaption = null;
	private $backButtonCaption = null;
	private $nextAllowed = null;
	private $backAllowed = null;
	
	/**
	 * Creates a new step for the InstallationWizard $wizard with the title $title
	 * and the text $text.
	 *
	 * @param InstallationWizard $wizard
	 * @param $title
	 * @param $text
	 */
	public function __construct(InstallationWizard $wizard, $title, $text) {
		$this->wizard = $wizard;
		$this->title = $title;
		$this->text = $text;
		$this->initStep();
	}
	
	/**
	 * Initializes the rest of the step.<br/>
	 * Do your customization work here.
	 *
	 * @see #__construct()
	 */
	protected abstract function initStep();
	
	/**
	 * This method is called <em>before</em> a step is displayed:<br/>
	 *  Next/Back --> Wizard Logic --> before()<br/>
	 * <br/>
	 * By default, nothing happens. Overwrite in your concrete step-class to
	 * implement functionality.
	 *
	 * @param $action Did the user click <em>next</em> or <em>back</em>?
	 * @see #after($action)
	 */
	public function before($action) { }
	
	/**
	 * This method is called <em>after</em> a step:
	 *  Next/Back --> after() --> Wizard Logic<br/>
	 * <br/>
	 * <br/>
	 * By default, nothing happens. Overwrite in you concrete step-class to
	 * implement functionality.
	 *
	 * @param $action Did the user click <em>next</em> or <em>back</em>?
	 * @see #before($action)
	 */
	public function after($action) {
		return true;
	}
	
	
	/**
	 * Returns a reference on the wizard which this step belongs to.
	 *
	 * @return InstallationWizard
	 * @see InstallationWizard
	 */
	protected function getWizard() {
		return $this->wizard;
	}
	
	/**
	 * Returns this steps title.
	 *
	 * @return title
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Returns this steps text.
	 *
	 * @return text
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * Returns an array with InstallationWizard_Input instances. Each describes
	 * a field (or similar) which gets rendered by the Wizard and provides possibility
	 * to input data.
	 *
	 * @return array with InstallationWizard_Inpu
	 */
	public function getInputs() {
		return $this->inputs;
	}
	
	/**
	 * Sets the array with the InstallationWizard_Input instances.
	 *
	 * @param $inputs array wiht InstallationWizard_Inputs
	 */
	protected function setInputs(array $inputs) {
		$this->inputs = $inputs;
	}
	
	/**
	 * Returns the caption for the wizards <em>next</em>-button.<br/>
	 * If the step has no specific caption for the button, $default gets returned.
	 *
	 * @param $default gets returned, if no caption specified
	 * @return next button caption or $default
	 */
	public function getNextButtonCaption($default) {
		$caption = $default;
		if($this->nextButtonCaption !== null) $caption = $this->nextButtonCaption;
		return $caption;
	}
	
	/**
	 * Sets the caption for the <em>next</em>-button.
	 *
	 * @param $caption
	 */
	protected function setNextButtonCaption($caption) {
		$this->nextButtonCaption = $caption;
	}
	
	/**
	 * Returns the caption for the wizards <em>back</em>-button.<br/>
	 * If the step has no specific caption for the button, $default gets returned.
	 *
	 * @param $default gets returned, if no caption specified
	 * @return back button caption or $default
	 */
	public function getBackButtonCaption($default) {
		$caption = $default;
		if($this->backButtonCaption !== null) $caption = $this->backButtonCaption;
		return $caption;
	}
	
	/**
	 * Sets the caption for the <em>back</em>-button.
	 *
	 * @param $caption
	 */
	protected function setBackButtonCaption($caption) {
		$this->backButtonCaption = $caption;
	}
	
	/**
	 * Checks if the <em>next</em>-button is available in this step.<br/>
	 * If the step does not specify this, $default gets returned.
	 *
	 * @param $default gets returned, if nothing specified inside step implementation
	 * @return true/false or $default, if nothing specified
	 */
	public function isNextAllowed($default) {
		$allowed = $default;
		if($this->nextAllowed !== null) $allowed = $this->nextAllowed;
		return $allowed;
	}
	
	/**
	 * Enables or disabled the <em>next</em>-button.
	 *
	 * @param $allowed true/false
	 */
	protected function setNextAllowed($allowed) {
		$this->nextAllowed = $allowed;
	}
	
	/**
	 * Checks if the <em>back</em>-button is available in this step.<br/>
	 * If the step does not specify this, $default gets returned.
	 *
	 * @param $default gets returned, if nothing specified inside step implementation
	 * @return true/false or $default, if nothing specified
	 */
	public function isBackAllowed($default) {
		$allowed = $default;
		if($this->backAllowed !== null) $allowed = $this->backAllowed;
		return $allowed;
	}

	/**
	 * Enables or disabled the <em>back</em>-button.
	 *
	 * @param $allowed true/false
	 */	
	protected function setBackAllowed($allowed) {
		$this->backAllowed = $allowed;
	}
	
}
?>