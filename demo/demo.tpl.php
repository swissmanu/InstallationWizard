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