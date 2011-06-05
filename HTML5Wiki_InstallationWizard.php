<?php
/**
 * An implementation of InstallationWizard.<br/>
 * It extends the default wizard with the step specifications and all necessary
 * methods for the installation process.
 *
 * @author Manuel Alabor
 */
class Html5Wiki_InstallationWizard extends InstallationWizard {
	
	private $basePath = '';
	
	const PROPERTY_WIKINAME = 'wikiname';
	const PROPERTY_DATABASE_HOST = 'database_host';
	const PROPERTY_DATABASE_NAME = 'database_name';
	const PROPERTY_DATABASE_USER = 'database_user';
	const PROPERTY_DATABASE_PASSWORD = 'database_password';
	const PROPERTY_INSTALLATION_TYPE = 'installationtype';
	const INSTALLATION_TYPE_WEB = 'useWeb';
	const INSTALLATION_TYPE_ROOT = 'useRoot';
	const FILE_CONFIG = '../config/config.php';
	const FILE_DATABASE_SCHEMA = '../data/sql/html5wiki_schema.sql';
	const FOLDER_WEB = '../web/';
	const FOLDER_ROOT = '../';
	
	public function __construct() {
		$steps = array(
			array(
				'name' => 'Welcome'
				,'text' => '<p>Welcome to the HTML5Wiki installation wizard.</p><p>The wizard will guide you through a few steps to setup all necessary stuff like database and basic configuration.<br/>Please click <em>Next</em> when you\'re ready to start.</p>'
				,'callMethodBefore' => 'testWritePermissions'
			)
			,array(
				'name' => 'Database Setup'
				,'text' => '<p>HTML5Wiki needs a MySQL database system to store its data.</p><p>Please specify the servers hostname (mostly <em>localhost</em>), a database, a valid user and its password.</p>'
				,'input' => array(
					self::PROPERTY_DATABASE_HOST => array(
						'type' => 'text'
						,'caption' => 'Database host'
						,'placeholder' => 'localhost'
						,'mandatory' => true
					)
					,self::PROPERTY_DATABASE_NAME => array(
						'type' => 'text'
						,'caption' => 'Database name'
						,'mandatory' => true
					)
					,self::PROPERTY_DATABASE_USER => array(
						'type' => 'text'
						,'caption' => 'Database user'
						,'mandatory' => true
					)
					,self::PROPERTY_DATABASE_PASSWORD => array(
						'type' => 'text'
						,'caption' => 'Database password'
						,'placeholder' => 'optional'
					)
				)
				,'callMethodAfter' => 'testDatabaseConnection'
			)
			,array(
				'name' => 'Branding'
				,'text' => '<p>Please enter a name for your HTML5Wiki installation.</p>'
				,'input' => array(
					self::PROPERTY_WIKINAME => array(
						'type' => 'text'
						,'caption' => 'Name for your wiki'
						,'mandatory' => true
					)
				)
			)
			,array(
				'name' => 'Installation type'
				,'text' => '<p>How is your webserver set up?</p><p>HTML5Wikis bootstrap is located inside the <em>web</em> directory. If you\'re able to point your webserver directly to this location, please select the first option below.</p><p>Many people are not allowed to control their hosted webservers on this level.<br/>If you\'re one of them, select the second option. All files from  <em>web</em> get moved one directory up to allow flawless interaction with HTML5Wiki.</p>'
				,'input' => array(
					self::PROPERTY_INSTALLATION_TYPE => array(
						'type' => 'radio'
						,'caption' => 'Installation type'
						,'mandatory' => true
						,'items' => array(
							self::INSTALLATION_TYPE_WEB => 'Use <em>web/</em>'
							,self::INSTALLATION_TYPE_ROOT => 'Don\'t use <em>web/</em>'
						)
					)
				)
			)
			,array(
				'name' => 'Ready to install'
				,'text' => '<p>The installation wizard has now all necessary information available.</p><p>Please click <em>Install</em> to finally set up your HTML5Wiki.<br/>Feel free to use the <em>Back</em> button to review your input before finishing the installation.</p>'
				,'nextCaption' => 'Install >'
			)
			,array(
				'name' => 'Installation done'
				,'text' => '<p>The installation steps were executed. If there was any problem, please read the displayed messages above precisely.<br/>They will include information how you can fullify the installation by yourself.</p><p>When everything is done, click <a href="'. $this->basePath. 'wiki/">here</a> to open your fresh installed HTML5Wiki.</p>'
				,'nextCaption' => 'Finish'
				,'callMethodBefore' => 'install'
				,'allowBack' => false
			)
		);
		
		parent::__construct($steps);
	}
	
	public function getBasePath() {
		return $this->basePath;
	}
	
	/**
	 * Tests if the installation wizard has writepermissions for several paths.
	 *
	 * @param $stepData
	 * @return true/false
	 */
	protected function testWritePermissions($stepData) {
		$writePermissions = is_writable('../web/') && is_writable('../');
		
		if($writePermissions) {
			$this->addMessage('info', 'No write permissions', 'The installation wizard has recognized that he has no or partially no write permissions.</p><p>You can try to fix this by changing the permissions on your server (<em>chmod 777</em>) and restart the wizard.</p><p>If not, you\'ll have to do some configuration steps by yourself. If you choose this variant, the installation wizard will tell you exactly the steps you have to do.');
		}
		
		return ($writePermissions);
	}


	/**
	 * This method tests the databaseconnection.<br/>
	 * If everythings fine, it returns true, otherwise it adds messages to the
	 * wizard and returns false.
	 *
	 * @param $stepData
	 * @return true/false
	 */
	protected function testDatabaseConnection($stepData) {
		$host = $this->getDataValue($stepData,'database_host');
		$dbname = $this->getDataValue($stepData,'database_name');
		$user = $this->getDataValue($stepData,'database_user');
		$password = $this->getDataValue($stepData,'database_password');

		$ok = (($connection = @mysql_connect($host, $user, $password)) !== false);
		if($ok === true) {
			$ok = (@mysql_select_db($dbname, $connection) !== false);

			if($ok === false) {
				$this->addMessage('error', 'Invalid database name', 'Could not access the database "'. $dbname. '". Please make sure this database exists.');
			} else {
				@mysql_close($connection);
				$this->addMessage('info', 'Database connection verified', 'The database connection has successfully been tested.');
			}
		} else {
			$this->addMessage('error','Connection error', 'Could not connect to the host "'. $host. '". Please check host, username and password.');
		}

		return $ok;
	}

	/**
	 * Does the following steps:<br/>
	 *  - Create configuration<br/>
	 *  - Setup the database with the schema and some default articles<br/>
	 *  - Move the files from web/ one level up if necessary<br/>
	 * <br/>
	 * If  any of these steps could be executed, the user gets a report with
	 * detailed instructions what he has to do manually.
	 *
	 * @param $stepData
	 * @return true if everythings fine, false if something went wrong
	 */ 
	protected function install($stepData) {
		$configOk = $this->setupConfig(self::FILE_CONFIG);
		$databaseOk = $this->setupDatabase(self::FILE_DATABASE_SCHEMA);
		$installationTypeOk = $this->setupInstallationtype();
		$htaccessOk = $this->setupHtaccessFile();
		
		if($configOk === true && $databaseOk === true && $installationTypeOk === true && $htaccessOk === true) {
			$this->addMessage('info','Installation successfull','Congratulations! All installation steps are successfully completed.');
		}
	}
	
	/**
	 * Builds a configuration array with the given parameters and writes it to
	 * its target file in the config folder.
	 *
	 * @param $targetFile
	 * @return true/false regarding success
	 */
	private function setupConfig($targetFile) {
		$configOk = true;
		$wikiname = $this->wizardData[self::PROPERTY_WIKINAME];
		$database_host = $this->wizardData[self::PROPERTY_DATABASE_HOST];
		$database_name = $this->wizardData[self::PROPERTY_DATABASE_NAME];
		$database_user = $this->wizardData[self::PROPERTY_DATABASE_USER];
		$database_password = $this->wizardData[self::PROPERTY_DATABASE_PASSWORD];
		
		/* Create config string: */
		$config = '<?php $config = array('. "\n"
				. '\'wikiName\' => \''. $wikiname. '\','. "\n"
				. '\'databaseAdapter\' => \'PDO_MYSQL\','. "\n"
				. '\'database\' => array('. "\n"
				. '	\'host\'     => \''. $database_host. '\','. "\n"
				. '	\'dbname\'   => \''. $database_name. '\','. "\n"
				. '	\'username\' => \''. $database_user. '\','. "\n"
				. '	\'password\' => \''. $database_password. '\''. "\n"
				. '),'. "\n"
				. '\'routing\' => array('. "\n"
				. '	\'defaultController\' => \'wiki\','. "\n"
				. '	\'defaultAction\'     => \'welcome\''. "\n"
				. ')'. "\n"
				. ',\'languages\' => array(\'en\', \'de\')'. "\n"
				. ',\'defaultLanguage\' => \'en\''. "\n"
				. ',\'defaultTimezone\' => \'Europe/Zurich\''. "\n"
				. ',\'development\' => false'. "\n"
			. '); ?>'. "\n";
		
		/* Try writing the file: */
		$configOk = $this->writeFile(self::FILE_CONFIG, $config);
		
		if($configOk === false) {
			$this->addMessage('error','Could not create configuration file', 'Please create the file <em>config/config.php</em> by yourself and copy paste the following configuration data into it:</p><p class="white-paper">'. nl2br(htmlentities($config)));
		}
		
		return $configOk;
	}
	
	/**
	 * Tries to setup the database with the give schema.
	 *
	 * @param $schemaFile
	 * @return true/false regarding success
	 */
	private function setupDatabase($schemaFile) {
		$databaseOk = true;
		$sql_schema = '';
		$database_host = $this->wizardData[self::PROPERTY_DATABASE_HOST];
		$database_name = $this->wizardData[self::PROPERTY_DATABASE_NAME];
		$database_user = $this->wizardData[self::PROPERTY_DATABASE_USER];
		$database_password = $this->wizardData[self::PROPERTY_DATABASE_PASSWORD];
		
		/* Try to read schema: */
		if(is_readable($schemaFile)) {
			if(($sql_schema = file_get_contents($schemaFile)) === false) {
				$databaseOk = false;
			}
		} else {
			$databaseOk = false;
		}
		
		/* Run the sql schema on the database: */
		if(strlen($sql_schema) > 0) {
			$connection = mysql_connect($database_host, $database_user, $database_password);
			if($connection !== false) {
				if(mysql_select_db($database_name, $connection) !== false) {
					// The schema needs to be split up to single statements since
					// mysql_query can only run one statement at once.
					$statements = explode(';', $sql_schema);
					foreach($statements as $statement) {
						$statement = trim($statement);
						if(strlen($statement) > 0) {
							if(mysql_query($statement, $connection) === false) {
								$databaseOk = false;
								break;
							}
						}
					}
					
					mysql_close($connection);
				} else {
					mysql_close($connection);
					$databaseOk = false;
				}
			} else {
				$databaseOk = false;
			}
		}
		
		if($databaseOk === false) {
			$this->addMessage('error','Database not set up', 'The database was not set up correctly.<br/>Please use the schema file <em>data/sql/html5wiki_schema.sql</em> and try setting up the database by yourself.');
		}
		
		return $databaseOk;
	}
	
	/**
	 * If the user wanted to, this setup method moves all files from /web/ one
	 * directory up (except of the install.php of course ;) )
	 *
	 * @return true/false regarding success
	 */
	private function setupInstallationtype() {
		$installationtypeOk = true;
		
		if($this->wizardData[self::PROPERTY_INSTALLATION_TYPE] === self::INSTALLATION_TYPE_ROOT) {
			$installationtypeOk = $this->copyDirectory(
				self::FOLDER_WEB
				,self::FOLDER_WEB
				,self::FOLDER_ROOT
				,array('../web/install.php')
				,true
				,array('../web/', '../web/install.php')
				);
		}
		
		if($installationtypeOk === false) {
			$this->addMessage('error','Files not moved', 'You choosed the installation type which runs the HTML5Wiki boostrap outside of the <em>web/</em> directory.<p><p>The wizard was not able to move all files located in <em>web/</em>. Please move the contained files by yourself one directory up and delete <em>web/</em> afterwards.');
		} else {
			$this->basePath = '../';
			
			$currentStep = $this->getCurrentStep();
			$currentStep['text'] = str_replace('wiki/', $this->basePath.'wiki/', $currentStep['text']);
			$this->steps[$this->getCurrentStepIndex()] = $currentStep;
		}
		
		return $installationtypeOk;
	}
	
	/**
	 * Copies a complete directory to the in $destinationBasePath specified
	 * target directory.<br/>
	 * Uses recursion to resolve all subdirectories.
	 *
	 * @param $directoryToCopy To current directory which should be copied
	 * @param $sourceBasePath The source base directory
	 * @param $destinationBasePath The target base directory
	 * @param $excludeFromCopy Exclude these files/directory from copy
	 * @param $move Delete source files/directory after successful copy
	 * @param $excludeFromDelete Exclude these files/directories from delete
	 * @return true/false regarding success
	 */
	private function copyDirectory($directoryToCopy, $sourceBasePath, $destinationBasePath, $excludeFromCopy = array(), $move = false, array $excludeFromDelete = array()) {
		$success = true;
		$files = scandir($directoryToCopy);
		$destinationPath = str_replace($sourceBasePath, $destinationBasePath, $directoryToCopy);
		if(!file_exists($destinationPath)) {
			if(mkdir($destinationPath) === false) $success = false;
		}
		
		if($success === true) {
			foreach ($files as $file) {
				if (in_array($file, array(".",".."))) continue;

				if(is_dir($directoryToCopy.$file) && !in_array($directoryToCopy, $excludeFromCopy)) {
					// Copy subdirectory:
					$success = $this->copyDirectory($directoryToCopy.$file.'/', $sourceBasePath, $destinationBasePath, $excludeFromCopy, $move, $excludeFromDelete);
				} else {
					// Copy file:
					if(!in_array($directoryToCopy.$file, $excludeFromCopy)) {
						if (!copy($directoryToCopy.$file, $destinationPath.$file)) {
							$success = false;
						} else {
							if($move === true) {
								if(!in_array($directoryToCopy.$file, $excludeFromDelete)) {
									unlink($directoryToCopy.$file);
								}
							}
						}
					}
				}
			}
		}
		
		if($success === true && $move === true) {
			if(!in_array($directoryToCopy, $excludeFromDelete)) {
				if(rmdir($directoryToCopy) === false) $success = false;
			}
		}
		
		return $success;
	}
	
	/**
	 * This creates the htaccess-file in the correct position.
	 *
	 * @return true/false regarding success
	 */
	private function setupHtaccessFile() {
		$htaccessOk = true;
		$targetFile = '';
		
		/* Where to write? */
		$installationtype = $this->wizardData[self::PROPERTY_INSTALLATION_TYPE];
		if($installationtype === self::INSTALLATION_TYPE_WEB) $targetFile = 'web/.htaccess';
		else if($installationtype === self::INSTALLATION_TYPE_ROOT) $targetFile = '.htaccess';
		
		/* Create contents: */
		$htaccess = '# This file was generated by the HTML5Wiki Installation Wizard. Do not modify.'."\n\n"
				  . 'RewriteEngine On'."\n"
				  . 'RewriteCond %{REQUEST_FILENAME} !-f'."\n"
				  . 'RewriteCond %{REQUEST_FILENAME} !-d'."\n"
				  . 'RewriteCond %{REQUEST_FILENAME} !-l'."\n"
				  . 'RewriteRule !^(css/.*|images/.*|js/.*) index.php [L]'."\n";

		
		/* Try to write the file: */
		$htaccessOk = $this->writeFile($targetFile, $htaccess);
		
		
		if($htaccessOk === false) {
			$this->addMessage('error','Could not create .htaccess file', 'Please create the file <em>'. $targetFile. '</em> by yourself and copy paste the following content into it:</p><p class="white-paper">'. nl2br($htaccess));
		}
		
		return $htaccessOk;
	}
	
	/**
	 * Tries to write $content into the file $targetFile.
	 *
	 * @param $targetFile
	 * @param $content
	 * @return true/false regarding success
	 */
	private function writeFile($targetFile, $content) {
		$ok = true;
		
		$fh = fopen($targetFile, 'w');
		if($fh) {
			if(fwrite($fh, $content)) fclose($fh);
			else $ok = false;
		} else {
			$ok = false;
		}
		
		return $ok;
	}
	
}
?>