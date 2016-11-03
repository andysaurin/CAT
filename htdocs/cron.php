<?php
/**
* cron.php
*
* @package Framework
*/

if ( php_sapi_name() != 'cli' )
	exit(0);

//ob_start();

//ini_set('error_reporting', 6135);
//ini_set("display_errors", true);

//if for the agony we want to be strict perfect, uncomment these lines to see the errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
//set_error_handler("exception_error_handler");

if ( !defined('SYSTEM_DOCUMENT_ROOT') )
	define('SYSTEM_DOCUMENT_ROOT', dirname(__FILE__) );

require_once( SYSTEM_DOCUMENT_ROOT . '/../config/config.php');

set_include_path( SYSTEM_DIRECTORY_ROOT . '/includes' . ':' . get_include_path() );

function autoload($class)
{
	if (preg_match('/^NQ_/', $class)) {
		$file = str_replace('_','/',substr($class,3)).'.php';
		require_once(SYSTEM_DIRECTORY_ROOT.'/includes/'.$file);
	}elseif (preg_match('/^Smarty_/', $class)) {
		if(!defined(SMARTY_SPL_AUTOLOAD))
			define('SMARTY_SPL_AUTOLOAD', 1);
		set_include_path(get_include_path() . PATH_SEPARATOR . SMARTY_SYSPLUGINS_DIR);
//		die(SMARTY_SPL_AUTOLOAD);
		require_once(strtolower($class).".php");
	}else{
		$file = str_replace('_','/',$class).'.php';
		require_once($file);
	}
}
spl_autoload_register('autoload');

function getArgs() {

	global $argv;

	$ret = array(
        'exec'      => '',
        'options'   => array(),
        'flags'     => array(),
        'arguments' => array(),
    );

    $ret['exec'] = array_shift( $argv );

    while (($arg = array_shift($argv)) != NULL) {
        // Is it a option? (prefixed with --)
        if ( substr($arg, 0, 2) === '--' ) {
            $option = substr($arg, 2);

            // is it the syntax '--option=argument'?
            if (strpos($option,'=') !== FALSE) {
             	$a = explode('=', $option, 2);
             	$opt_name = $a[0];
             	$opt_val = $a[1];
             	$ret['options'][$opt_name] = $opt_val;
             	//   array_push( $ret['options'], explode('=', $option, 2) );
            }else
                $ret['options'][$option] = null;
                //array_push( $ret['options'], $option );

            continue;
        }

        // Is it a flag or a serial of flags? (prefixed with -)
        if ( substr( $arg, 0, 1 ) === '-' ) {
        	if (strpos($arg,'=') !== FALSE) {
	        	list( $flag, $var ) = explode("=", $arg);
	        	$flag = ltrim($flag, '-');
	            $ret['flags'][$flag] = $var;

	            continue;
	        } else {
		        $ret['flags'][$flag] = null;
	        }
        }

        // finally, it is not option, nor flag
        $ret['arguments'][] = $arg;
        continue;
    }
    return $ret;

}
$args = getArgs();
$_GET['module'] = ( $args['flags']['module'] ? $args['flags']['module'] : 'home' );
$_GET['class'] = ( $args['flags']['class'] ? $args['flags']['class'] : 'index' );
$_GET['action'] = ( $args['flags']['action'] ? $args['flags']['action'] : false );

//var_dump($argv);

define("PRESENTER", "none");

//print_r($this->args(;

//kick off the whole shebang
NQ_Server::instantiate();


?>
