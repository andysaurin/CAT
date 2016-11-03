<?php
/**
* index.php
*
* @package Framework
*/

//if for the agony we want to be strict perfect, uncomment these lines to see the errors
/*
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
*/
//set_error_handler("exception_error_handler");

if ( !defined('SYSTEM_DOCUMENT_ROOT') )
	define('SYSTEM_DOCUMENT_ROOT', dirname(__FILE__) );

require_once( SYSTEM_DOCUMENT_ROOT . '/../config/config.php');

set_include_path( SYSTEM_DIRECTORY_ROOT . '/includes' . ':' .SYSTEM_DIRECTORY_ROOT . '/modules'.':'. get_include_path() );

function autoload($class)
{
	if (preg_match('/^NQ_/', $class)) {
		$file = str_replace('_','/',substr($class,3)).'.php';
		require_once(SYSTEM_DIRECTORY_ROOT.'/includes/'.$file);
	}elseif (preg_match('/^Smarty_/', $class)) {
		if(!defined(SMARTY_SPL_AUTOLOAD))
			define('SMARTY_SPL_AUTOLOAD', 1);
		set_include_path(get_include_path() . PATH_SEPARATOR . SMARTY_SYSPLUGINS_DIR);
		require_once(strtolower($class).".php");
	}else{
		$file = str_replace('_','/',$class).'.php';
		require_once($file);
	}
}
spl_autoload_register('autoload');

//kick off the whole shebang
NQ_Server::instantiate();


?>
