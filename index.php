<?php

require_once('Html5Wiki_InstallationWizard.php');
ini_set('display_errors', true);
error_reporting(E_ALL | E_STRICT);
$installscript = "index.php";

/* ---------------------------------------------------------------------- */

/* Run the wizard: */
$wizard = new Html5Wiki_InstallationWizard();
$wizard->run($_POST);

$currentStepIndex = $wizard->getCurrentStepIndex();
$currentStep = $wizard->getCurrentStep();
$messages = $wizard->getMessages();

/* ---------------------------------------------------------------------- */

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Installation Wizard | Demo</title>
	<meta name="description" content="Demo"/>
 	<meta name="author" content="Manuel Alabor"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head> 
<body>
	<div class="container_12">
		<header class="grid_12 header-overall">
			<a href="<?php echo $installscript ?>" class="logo"><span class="hide">Installation Wizard</span></a>
			<nav class="main-menu">
				<ol class="menu-items clearfix">
					<li class="item install active">
						<a href="#" class="tab">Installation Wizard: Step <?php echo $currentStepIndex+1 ?> of <?php echo $wizard->getTotalSteps() ?></a>
					</li>
				</ol>
			</nav>
		</header>
		<div class="clear"></div>
		
		<section class="content editor grid_12">
			<?php if(sizeof($messages) > 0) : ?>
			<?php foreach($messages as $message) : ?>
			<div class="box <?php echo $message['type'] ?>">
				<h3><?php echo $message['title'] ?></h3>
				<p><?php echo $message['text'] ?></p>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
			
			<form action="<?php echo $installscript ?>" method="post">
				<input type="hidden" name="step" value="<?php echo $currentStepIndex ?>" />
				<?php echo $wizard->serializeWizardData(); ?>
				<header class="title">
					<h2>Step <?php echo $currentStepIndex+1; ?>: <?php echo $currentStep['name'] ?></h2>
					<?php echo $currentStep['text'] ?>
				</header>
				
				<?php echo $wizard->renderInputs($currentStep); ?>
			
				<footer class="bottom-button-bar">
					<?php if($wizard->isNextAllowed()) : ?>
					<input type="submit" name="next" value="<?php echo (isset($currentStep['nextCaption']) === true ? $currentStep['nextCaption'] : 'Next >'); ?>" class="large-button caption"/>
					<?php endif; ?>
					<?php if($wizard->isBackAllowed()) : ?>
					<input type="submit" name="back" value="Back" class="large-button caption" />
					<?php endif; ?>
				</footer>
			</form>
		</section>
		<div class="clear"></div>
	</div>
</body>
</html>