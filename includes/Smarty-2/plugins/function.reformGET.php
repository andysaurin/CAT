<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {reformGET} custom function plugin
 *
 * Type:     function<br>
 * Name:     reformGET<br>
 * Purpose:  reform a URL without a specified GET var 

 * @author   Andy <andy@adulteuros.com
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_reformGET($params, &$smarty)
{
	
	//do not return these GET vars
	$without = array(
		"module",
		"class",
		"event",
		"category",
	);
	if ( strpos($params['without'], ',') ) {
		$without_params = explode(",", $params['without']);
		foreach ($without_params as $k=>$v) {
			$without[] = trim($v);
		}
	} else {
		$without[] = $params['without'];
	}
	
	if ( !is_array($_GET) ) {
        return;
    }
    
	if ( $_GET ) {
		$_GET = $_GET; //don't manipulate the $_GET array directly as this will screw things up downstream that rely on it
		foreach ($_GET as $key => $val) {
			
			if ( !in_array($key, $without) ) {
				if ( isset ($get_string) ) 
					$get_string .= "&{$key}={$val}";
				else
					$get_string = "{$key}={$val}";	
			}
		}
		
		return $get_string;
	
	} else {
		return ;
	}


}
?>