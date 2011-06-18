<?php
namespace InstallationWizard;

/**
 * The abstract InstallationWizard.<br/>
 * All basic logic to handle the flow between different wizard steps is ecapsulated
 * into this class.<br/>
 * To implement your own wizard, derive from InstallationWizard and implement
 * the #initSteps() method.
 *
 * @author Manuel Alabor
 * @see InstallationWizard\Step
 */
abstract class InstallationWizard {
	
	private $steps = array();
	private $wizardData = array();
	private $currentStepIndex = 0;
	private $messages = array();
	
	/**
	 * Initializes the InstallationWizard and calls the #initSteps()-method which
	 * will be implemented by a concrete class.
	 *
	 * @see #initSteps()
	 */
	public function __construct() {
		$this->steps = $this->initSteps();
	}
	
	/**
	 * Creates instances for all necessary steps and returns an array containing
	 * them.<br/>
	 * Implement this method in your concrete InstallationWizard.
	 *
	 * @return array with step information
	 * @see #__construct()
	 */
	protected abstract function initSteps();
	
	/**
	 * Starts the installation wizard logic.
	 * 
	 * @param $postData array, usually something like <code>$_POST</code>
	 */
	public function run(array $postData) {
		$this->deserializeWizardData($postData);
		$this->processPostData($postData);
	}
	
	
	/**
	 * Takes possible present input data from the POST-request and fills it into
	 * the proper step-data-array.
	 *
	 * @param $postData simply the $_POST array
	 */
	private function processPostData(array $postData) {
		$action = 'next';
		
		if(isset($postData['step'])) {
			$this->currentStepIndex = $postData['step'];
			$currentStepIndex = $this->currentStepIndex;
			
			// Read the input-data into the step specifications
			foreach($postData as $key => $value) {
				if(strstr($key, 'input_') !== false) {
					$key = substr($key, 6);
					$this->setWizardDataForKey($key, $value);
				}
			}
			
			// Determine if the user clicked next or back:
			if(isset($postData['back'])) $action = 'back';

			// Check input only if next was clicked:
			$inputOk = true;
			if($action === 'next') $inputOk = $this->isInputOk();

			// Execute "after" method if needed and update the
			// currentstep_index regarding the button which was clicked.
			if($inputOk === true) {
				$ok = true;
				if($action === 'next') {
					$ok = $this->steps[$currentStepIndex]->after($action);
					if($ok === true) $this->currentStepIndex++;
				} else if($action === 'back') {
					$this->currentStepIndex--;
				}
				
				$currentStepIndex = $this->currentStepIndex;
			}
		}

		// Execute "before" method if present:
		$this->getCurrentStep()->before($action);
		
	}

	/**
	 * Serializes the data of each step in the steps-array into a hidden
	 * input-element.
	 *
	 * @return input-elements with the serialized data
	 * @see deserializeSWizardData
	 */
	public function serializeWizardData() {
		$serialized = '';
		
		if(sizeof($this->wizardData) > 0) {
			$serialized .= '<input type="hidden" '
			            .  'name="wizardData" '
						.  'value="'. urlencode(serialize($this->wizardData)). '" '
						.  ' />'."\n";
		}

		return $serialized."\n";
	}

	/**
	 * After a POST-Request, this method deserializes the steps-data back into
	 * the wizardData-array
	 *
	 * @param $postSource simply the $_POST-variable
	 * @see serializeWizardData
	 */
	private function deserializeWizardData(array $postSource) {
		if(isset($postSource['wizardData'])) {
			$this->wizardData = unserialize(urldecode($postSource['wizardData']));
		}
	}
	
	/**
	 * This function checks if all mandatory inputs for the step $currentstep_index
	 * are filled in.<br/>
	 * If not, a message gets added with #addMessage and false is returned.
	 * Otherwise true gets returned.
	 * 
	 * @return true/false
	 */
	private function isInputOk() {
		$missingFields = array();
		$currentStep = $this->getCurrentStep();
		
		foreach($currentStep->getInputs() as $key => $input) {
			if($input->isMandatory() === true) {
				$dataValid = true;

				if(isset($this->wizardData[$key])) {
					$dataValid = $input->isValueValid($this->wizardData[$key]);
				} else {
					$dataValid = false;
				}

				if($dataValid === false) $missingFields[] = $input->getCaption();
			}
		}

		// Add message if needed:
		if(sizeof($missingFields) > 0) {
			$message = 'Please fill in the following field(s) you missed:<br/>';
			foreach($missingFields as $field) {
				$message .= '&nbsp;-&nbsp;'. $field. '<br/>';
			}
			$this->addMessage('error', 'Missing input', $message);
		}

		return (sizeof($missingFields) === 0);
	}

	/**
	 * Takes the input-specifications of a step and renders the proper input-elements
	 * for them.
	 *
	 * @param InstallationWizard_Step $step
	 * @return Rendered input elements
	 */
	public function renderInputs(Step $step) {
		$rendered = '';

		if(sizeof($step->getInputs()) > 0) {
			$tabindex = 0;
			
			foreach($step->getInputs() as $key => $input) {
				$tabindex++;
				$value = '';
				$placeholder = '';
				if(isset($this->wizardData[$key])) $value = $this->wizardData[$key];
				
				$rendered .= '<p>'
						  .  $input->render($key, $value)
						  .  '</p>'."\n";
				
				/*
					case 'radio' :
						$items = $input['items'];
						foreach($items as $itemvalue => $item) {
							if($itemvalue === $value) $selected = ' checked="checked"';
							else $selected = '';
							$rendered .= '<span class="radiocontainer">'
							          .  '<input type="radio" '
									  .  'name="input_'. $key. '" '
							  	  	  .  'id="input_'. $key. '" '
									  .  'value="'. $itemvalue. '" '
									  .  'tabindex="'. $tabindex. '" '
									  .  $selected
									  .  '/> '
									  .  $item
									  .  '</span>';
							$tabindex++;
						}
				}
	
				$rendered .= '</p>'."\n";
				*/
			}
		}
		
		return $rendered;

	}

	/**
	 * Adds a message to the wizard.
	 *
	 * @param $type [error|info]
	 * @param $title
	 * @param $message
	 */
	public function addMessage($type, $title, $message) {
		$this->messages[] = array(
			'type' => $type
			,'title' => $title
			,'text' => $message
		);
	}
		
	/**
	 * Returns the current step index.
	 *
	 * @return Current steps index
	 */		
	public function getCurrentStepIndex() {
		return $this->currentStepIndex;
	}
	
	/**
	 * Returns the current step.
	 *
	 * @return Current step specification
	 */
	public function getCurrentStep() {
		return $this->steps[$this->getCurrentStepIndex()];
	}
	
	/**
	 * Returns the total count of steps.
	 *
	 * @return count of steps
	 */
	public function getTotalSteps() {
		return sizeof($this->steps);
	}
	
	/**
	 * Returns the complete data storage of this wizard.
	 *
	 * @return array
	 */
	public function getWizardData() {
		return $this->wizardData;
	}
	
	/**
	 * Returns a specific value of the wizard data storage.<br/>
	 * If the key was not found, <code>null</code> gets returned.
	 *
	 * @param $key
	 * @return value or null
	 */
	public function getWizardDataForKey($key) {
		return $this->getValue($this->wizardData, $key, null);
	}
	
	/**
	 * Sets the value $value for the key $key in the wizard data storage.
	 *
	 * @param $key
	 * @param $value
	 */
	public function setWizardDataForKey($key, $value) {
		$this->wizardData[$key] = $value;
	}
	
	/**
	 * Returns the available messages.
	 *
	 * @return an array with messages
	 */
	public function getMessages() {
		return $this->messages;
	}

	/**
	 * Returns a value out of the array $data. If the specific key is not
	 * present in $data, $default gets returned.
	 * 
	 * @param $data
	 * @param $key
	 * @param $default (optional, default='')
	 * @return entrie from $data or $default
	 */
	function getDataValue($data, $key, $default='') {
		$result = $default;
		if(isset($data[$key])) $result = $data[$key];
		return $result;
	}
	
	/**
	 * Returns if the <em>Next</em>-button should be displayed or not.
	 *
	 * @return true/false
	 */
	public function isNextAllowed() {
		$allowed = false;

		if($this->getCurrentStepIndex() < $this->getTotalSteps()-1) {
			$allowed = true;
		}
		
		return $allowed;
	}
	
	/**
	 * Returns if the <em>Back</em>-button should be displayed or not.<br/>
	 * To recognize this, the current steps index has to be larger than 0 AND, if
	 * available, the current steps property <code>allowBack</code> has to be true.
	 *
	 * @return true/false
	 */
	public function isBackAllowed() {
		$allowed = true;
		$currentStep = $this->getCurrentStep();
		
		if($this->getCurrentStepIndex() > 0) {
			$allowed = $currentStep->isBackAllowed(true);
		} else {
			$allowed = false;
		}
		
		return $allowed;
	}

}	
?>