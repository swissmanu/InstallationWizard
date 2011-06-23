<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Installation Wizard </title>
		<meta name="description" content="InstallationWizard Demo"/>
		<meta name="author" content="InstallationWizard"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<link href="http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz&v1" rel="stylesheet" type="text/css">
		<style type="text/css">
			body {
				font-family: "Lucida Sans", "Lucida Grande", Lucida, sans-serif;
				font-size: 100%;
				width: 960px;
				margin: 0 auto;
				background-color: #DDD;
			}
			
			h1, h2, h3, h4, h5, h6 {
				font-size: 100%;
				font-family: "Yanone Kaffeesatz", "Lucida Sans", "Lucida Grande", Lucida, sans-serif;
				margin: 0;
			}
			h1 {
				font-size: 300%;
				margin-bottom: 20px;
			}
			h2 {
				font-size: 200%;
			}
			
			.wizard {
				background-color: white;
				margin-top: 20px;
				padding: 10px 20px;
				background-color: #EEE;
				border-radius: 10px;
				border: 1px solid #CCC;
			}
			
			.step-text {
				background-color: #E1E1E1;
				padding: 5px;
				font-size: 90%;
			}
			
			label, input, select { font-size: 90%; }
			label {
				display: inline-block;
				width: 150px;
			}
			
			.buttons {
				margin-top: 10px;
			}
		
		
		</style>
	</head>
	<body>
		<section class="wizard">
			<header>
				<h1>Step <?php echo $currentStepIndex+1; ?>: <?php echo $currentStep->getTitle() ?></h1>
			
				<?php if(sizeof($messages) > 0) : ?>
				<?php foreach($messages as $message) : ?>
				<div class="box <?php echo $message['type'] ?>">
					<h2><?php echo $message['title'] ?></h2>
					<p><?php echo $message['text'] ?></p>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
			
				<div class="step-text">
				<?php echo $currentStep->getText() ?>
				</div>
			</header>
		
			<form action="demo.php" method="post">
				<input type="hidden" name="step" value="<?php echo $currentStepIndex ?>" />
				<?php echo $wizard->serializeWizardData(); ?>

				<?php echo $wizard->renderInputs($currentStep); ?>
			
				<footer class="buttons">
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