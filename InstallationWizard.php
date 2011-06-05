<?php
/**
 * The abstract InstallationWizard.<br/>
 * It encapsulates all basic processing logic for running a wizard.
 *
 * @author Manuel Alabor
 */
abstract class InstallationWizard {
	
	protected $steps = array();
	protected $wizardData = array();
	protected $currentStepIndex = 0;
	private $messages = array();
	
	/**
	 * Creates a new InstallationWizard and initializes it with the steps
	 * from $stepSpecifications.
	 *
	 * @param $stepSpecifications array
	 * */
	public function __construct(array $stepSpecifications) {
		$this->steps = $stepSpecifications;
	}
	
	/**
	 * Starts the installation wizard logic.
	 * 
	 * @param $postData simply a $_POST array
	 */
	public function run(array $postData) {
		$this->deserializeWizardData($postData);
		$this->processPostData($postData);
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
	 * Takes possible present input data from the POST-request and fills it into
	 * the proper step-data-array.
	 *
	 * @param $postData simply the $_POST array
	 */
	private function processPostData(array $postData) {
		if(isset($postData['step'])) {
			$this->currentStepIndex = $postData['step'];
			$currentStepIndex = $this->currentStepIndex;

			// Read the input-data into the step specifications
			foreach($postData as $key => $value) {
				if(strstr($key, 'input_') !== false) {
					$key = substr($key, 6);
					$this->wizardData[$key] = $value;
				}
			}

			// Determine if the user clicked next or back:
			$wizardDirection = 'next';
			if(isset($postData['back'])) $wizardDirection = 'back';

			// Check input only if next was clicked:
			$inputOk = true;
			if($wizardDirection === 'next') $inputOk = $this->isInputOk();

			// Execute "after" method if needed and update the
			// currentstep_index regarding the button which was clicked.
			if($inputOk === true) {
				$ok = true;
				if($wizardDirection === 'next') {
					if(isset($this->steps[$currentStepIndex]['callMethodAfter'])) {
						$method = $this->steps[$this->currentStepIndex]['callMethodAfter'];
						$ok = $this->$method($this->wizardData);
					}
					if($ok === true) $this->currentStepIndex++;
				} else if($wizardDirection === 'back') {
					$this->currentStepIndex--;
				}
				
				$currentStepIndex = $this->currentStepIndex;
			}
		}

		// Execute "before" method if present:
		if(isset($this->steps[$this->currentStepIndex]['callMethodBefore'])) {
			$method = $this->steps[$this->currentStepIndex]['callMethodBefore'];
			$this->$method($this->wizardData);
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
		$currentStep = $this->steps[$this->currentStepIndex];
		
		if(isset($currentStep['input'])) {
			foreach($currentStep['input'] as $key => $input) {
				if(isset($input['mandatory']) && $input['mandatory'] === true) {
					$dataValid = true;

					if(isset($this->wizardData[$key])) {
						if(strlen($this->wizardData[$key]) === 0) {
							$dataValid = false;
						}
					} else {
						$dataValid = false;
					}

					if($dataValid === false) $missingFields[] = $input['caption'];
				}
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
	 * Takes the input-specifications of a step and renders the proper input-elements
	 * for them.
	 *
	 * @param $step step-specifications
	 * @return input-elements
	 */
	public function renderInputs($step) {
		$rendered = '';

		if(isset($step['input'])) {
			$inputs = $step['input'];
			$tabindex = 0;
			foreach($inputs as $key => $input) {
				$tabindex++;
				$value = '';
				$placeholder = '';
				if(isset($this->wizardData[$key])) $value = $this->wizardData[$key];
				if(isset($input['placeholder'])) $placeholder = $input['placeholder'];

				$rendered .= '<p>'
						  .  '<label for="input_'. $key. '">'
						  .  $input['caption']
						  .  '</label>';

				switch($input['type']) {
					case 'text' :
						$rendered .= '<input type="text" '
								  .  'name="input_'. $key. '" '
							  	  .  'id="input_'. $key. '" '
								  .  'value="'. $value. '" '
								  .  'placeholder="'. $placeholder. '" '
								  .  'tabindex="'. $tabindex. '" '
								  .  '/>';
						break;
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
	 * If true, the next button is allowed to be displayed.
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
	 * If true, the back button is allowed to be displayed.
	 *
	 * @return true/false
	 */
	public function isBackAllowed() {
		$allowed = true;
		$currentStep = $this->getCurrentStep();
		
		if($this->getCurrentStepIndex() > 0) {
			if(isset($currentStep['allowBack']) && $currentStep['allowBack'] === false) {
				$allowed = false;
			}
		} else {
			$allowed = false;
		}
		
		return $allowed;
	}

}	
?>