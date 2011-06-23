<?php
require_once('../lib/InstallationWizard/ClassLoader.php');
$classLoader = new \InstallationWizard\ClassLoader('InstallationWizard');
$classLoader->setIncludePath('../lib');
$classLoader->register();

class Demo_InstallationWizard extends InstallationWizard\InstallationWizard {

	protected function initSteps() {
		return array(
			new Demo_Step1($this)
			,new Demo_Step2($this)
			,new Demo_Step3($this)
		);
	}
	
}


class Demo_Step1 extends \InstallationWizard\Step {
	
	public function __construct(\InstallationWizard\InstallationWizard $wizard) {
		parent::__construct(
			$wizard
			,'Welcome'
			,'This is a demo for the InstallationWizard PHP framework. Click &quot;Next &gt;&quot; to continue.'
		);
	}
	
	protected function initStep() {
	}
	
}

class Demo_Step2 extends \InstallationWizard\Step {
	
	public function __construct(\InstallationWizard\InstallationWizard $wizard) {
		parent::__construct(
			$wizard
			,'Different Inputs'
			,'The framework provides different methods to gather information from your user.'
		);
	}
	
	protected function initStep() {
		$databasename = new \InstallationWizard\Input\Textfield('Database host');
		$databasename->setMandatory(false);
		$ok = new \InstallationWizard\Input\Checkbox('OK?');
		$ok->setMandatory(true);
		$dropdown = new \InstallationWizard\Input\Dropdown('Gender',array('f'=>'Female','m'=>'Male'));
		$age = new \InstallationWizard\Input\RadiobuttonGroup('Age',array('old'=>'>= 25','young'=>'< 25'));
		
		
		$this->addInput('database_name', $databasename);
		$this->addInput('ok', $ok);
		$this->addInput('gender', $dropdown);
		$this->addInput('age', $age);
	}
	
}

class Demo_Step3 extends InstallationWizard\Step {
	
	public function __construct(\InstallationWizard\InstallationWizard $wizard) {
		parent::__construct(
			$wizard
			,'Welcome'
			,'Welcome to the Demo'
		);
	}
	
	protected function initStep() {
		$this->setBackAllowed(false);
	}
	
}


$wizard = new Demo_InstallationWizard();
$wizard->run($_POST);

$currentStepIndex = $wizard->getCurrentStepIndex();
$currentStep = $wizard->getCurrentStep();
$messages = $wizard->getMessages();


include 'demo.tpl.php';

?>