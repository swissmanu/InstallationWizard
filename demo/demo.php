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


class Demo_Step1 extends InstallationWizard\Step {
	
	public function __construct(InstallationWizard\InstallationWizard $wizard) {
		parent::__construct(
			$wizard
			,'Welcome'
			,'Welcome to the Demo'
		);
	}
	
	protected function initStep() {
		
	}
	
}

class Demo_Step2 extends InstallationWizard\Step {
	
	public function __construct(InstallationWizard\InstallationWizard $wizard) {
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

class Demo_Step3 extends InstallationWizard\Step {
	
	public function __construct(InstallationWizard\InstallationWizard $wizard) {
		parent::__construct(
			$wizard
			,'Welcome'
			,'Welcome to the Demo'
		);
	}
	
	protected function initStep() {
		
	}
	
}


$wizard = new Demo_InstallationWizard();
$wizard->run($_POST);

$currentStepIndex = $wizard->getCurrentStepIndex();
$currentStep = $wizard->getCurrentStep();
$messages = $wizard->getMessages();


include 'demo.tpl.php';

?>