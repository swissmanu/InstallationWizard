<?php
namespace InstallationWizard\Input;

/**
 * One or more radio buttons to select one specific option.
 *
 * @author Manuel Alabor
 */
class RadiobuttonGroup extends \InstallationWizard\Input\Choosable {
	
	/**
	 * Default constructior with caption and choices.<br/>
	 * For $choices, pass an associative key/value-array.
	 *
	 * @param $caption
	 * @param $choices
	 */
	public function __construct($caption, array $choices) {
		parent::__construct($caption, $choices);
	}
	
	/**
	 * Renders the $choices as radiobuttons.
	 *
	 * @param $key
	 * @param $value value to fill in by default
	 * @return HTML code
	 */
	protected function renderInput($key, $value) {
		$rendered = '';
		foreach($this->getChoices() as $choiceKey => $choice) {
			if($choice === $value) $selected = ' checked="checked"';
			else $selected = '';
			
			$rendered .= '<span class="radiocontainer">'
			          .  '<input type="radio" '
					  .  'name="input_'. $key. '" '
			  	  	  .  'id="input_'. $key. '" '
					  .  'value="'. $choiceKey. '" '
					  .  $selected
					  .  '/> '
					  .  $choice
					  .  '</span>';
		}
							
		return $rendered;
	}
	
}
?>