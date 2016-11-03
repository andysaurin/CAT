<?php

abstract class NQ_Server {

	protected $moduleName;
	
	function __construct()
	{	
	
	}
	
	public static function instantiate()
	{
		$mtime = microtime(); 
		$mtime = explode(' ', $mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$starttime = $mtime; 

		if (!isset($_GET['module'])) {
			$_GET['module'] = 'home';
		}
		
		$module = $_GET['module'];
		define('REQUESTED_MODULE', $module);
		
		if (isset($_GET['event'])) {
			$event = $_GET['event'];
		} else {
			$event = '__default';
		}
		
		if (isset($_GET['class'])) {
			$class = $_GET['class'];
		} else {
			$class = 'index'; //$class = $module;
		}
		
		if ( !defined('PRESENTER') ) { //cron jobs already define the presenter as 'none'
			if ( isset($_GET['presenter']) && $_GET['presenter'] == 'debug')
				define('PRESENTER', 'debug');
			elseif (isset($_GET['presenter']) && file_exists(SYSTEM_DIRECTORY_ROOT.'/includes/Presenter/'.$_GET['presenter'].'.php')) 
				define('PRESENTER', $_GET['presenter']);
			elseif ( isset($_GET['module']) && $_GET['module'] == 'api' )
				define('PRESENTER', 'none');
			else
				define('PRESENTER', SYSTEM_PRESENTER);
		}
		
		// here we separate custom modules from the core
		if ( file_exists( SYSTEM_DIRECTORY_ROOT . "/modules/{$module}/{$class}.php" ) ) {
			define( 'REQUESTED_MODULE_DIR', SYSTEM_DIRECTORY_ROOT.'/modules');
		}
		
		$classFile = false;
		if ( defined('REQUESTED_MODULE_DIR') ) {
			$classFile = REQUESTED_MODULE_DIR.'/'.$module.'/'.$class.'.php';
			$classConfig = REQUESTED_MODULE_DIR.'/'.$module.'/config.php';
			$functionsFile = REQUESTED_MODULE_DIR.'/'.$module.'/functions.php';
		}
		if (file_exists($classFile)) {
			if (file_exists($classConfig))
				include_once($classConfig);
			if (file_exists($functionsFile))
				include_once($functionsFile);			
			require_once($classFile);
			if (class_exists($class)) {
				try {
					$instance = new $class();
					if (!NQ_Module::isValid($instance)) {
						header("HTTP/1.1 404 Not Found");
						if (PRESENTER == 'debug' && SYSTEM_DEBUG_OUTPUT == 'display' )
							echo "<h1>The requested module <code>$module</code> is not a valid framework module</h1>";
						@include_once(SYSTEM_DOCUMENT_ROOT.'/errordocs/404.php');
						exit(0);
					}
					$instance->startTime = $starttime;
					$instance->moduleName = $module;
					if ($instance->authenticate()) {
						try {
							if (method_exists($instance, $event)) {
								$result = $instance->$event();
								if (!PEAR::isError($result)) {
									$presenter = NQ_Presenter::factory($instance->presenter,$instance);
									if (!PEAR::isError($presenter)) {
										$presenter->display();
									} else {
										die($presenter->getMessage());
									}
								}
							} else {
								header("HTTP/1.1 404 Not Found");
								if (PRESENTER == 'debug' && SYSTEM_DEBUG_OUTPUT == 'display' )
									echo '<h1>Method '.$instance->moduleName.'::'.$event.' does not exists</h1>';
								@include_once(SYSTEM_DOCUMENT_ROOT.'/errordocs/404.php');
								exit(0);
							}
						} catch (Exception $error) {
							die($error->getMessage());
						}
					} else {
						if (isset($_SERVER['REQUEST_URI'])) {
							$from = $instance->nq_encrypt(SYSTEM_ENCRYPTION_KEY, $_SERVER['REQUEST_URI']);//store what they were trying to see, so we can send them back there after login
							header("Location: /login/?ret={$from}");
						}else{
							header("Location: /login/");
						}
						exit(0);
					}
				} catch (Exception $error) {
					die($error->getMessage());   
				}
			} else {
				header("HTTP/1.1 404 Not Found");
				if (PRESENTER == 'debug' && SYSTEM_DEBUG_OUTPUT == 'display' )
					echo "<h1>A valid module for your request was not found</h1>";
				@include_once(SYSTEM_DOCUMENT_ROOT.'/errordocs/404.php');
				exit(0);
			}
		} else {
			header("HTTP/1.1 404 Not Found");
			if (PRESENTER == 'debug' && SYSTEM_DEBUG_OUTPUT == 'display' )
				echo "<h1>Could not find class File: $classFile</h1>";
				
			@include_once(SYSTEM_DOCUMENT_ROOT.'/errordocs/404.php');
			exit(0);
		}
		
	
		$all_constants = get_defined_constants(true);
		$runtime_constants = $all_constants['user'];
		ksort($runtime_constants);
		unset($all_constants);
/*
print_r($runtime_constants);
exit;
*/
	
	
	}
}
?>