<?php
require_once('../lib/InstallationWizard/InstallationWizard.php');
use InstallationWizard\InstallationWizard;
use InstallationWizard\Step;


class Demo_InstallationWizard extends InstallationWizard {

	protected function initSteps() {
		return array(
			new Demo_Step1($this)
			,new Demo_Step2($this)
			,new Demo_Step3($this)
		);
	}
	
}


class Demo_Step1 extends Step {
	
	public function __construct(InstallationWizard $wizard) {
		parent::__construct(
			$wizard
			,'Welcome'
			,'Welcome to the Demo'
		);
	}
	
	protected function initStep() {
		
	}
	
}

class Demo_Step2 extends Step {
	
	public function __construct(InstallationWizard $wizard) {
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

class Demo_Step3 extends Step {
	
	public function __construct(InstallationWizard $wizard) {
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

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Installation Wizard </title>
		<meta name="description" content="InstallationWizard Demo"/>
		<meta name="author" content="InstallationWizard"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>
	<body>
		
		<section class="">
			<header>
				<h2>Step <?php echo $currentStepIndex+1; ?>: <?php echo $currentStep->getTitle() ?></h2>
				
				<?php if(sizeof($messages) > 0) : ?>
				<?php foreach($messages as $message) : ?>
				<div class="box <?php echo $message['type'] ?>">
					<h3><?php echo $message['title'] ?></h3>
					<p><?php echo $message['text'] ?></p>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
				
				<?php echo $currentStep->getText() ?>
			</header>
			
			<form action="index.php" method="post">
				<input type="hidden" name="step" value="<?php echo $currentStepIndex ?>" />
				<?php echo $wizard->serializeWizardData(); ?>

				<?php echo $wizard->renderInputs($currentStep); ?>
				
				<footer class="bottom-button-bar">
					<?php if($wizard->isNextAllowed()) : ?>
					<input type="submit" name="next" value="Next >" />
					<?php endif; ?>
					<?php if($wizard->isBackAllowed()) : ?>
					<input type="submit" name="back" value="Back" />
					<?php endif; ?>
				</footer>
			</form>
		</section>
	</body>
</html>