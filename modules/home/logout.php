<?php

/**
* logout
*
* 
* 
* @license http://www.opensource.org/licenses/gpl-license.php
* @package Modules
* @filesource
*/

/**
* logout
*
* 
* @package Modules
*/
class logout extends NQ_Auth_No
{
	function __construct()
	{
		parent::__construct();
	}

	function __default()
	{
		$this->session->destroy();
		
		unset($_SERVER['PHP_AUTH_USER']);
		
		if (is_array($_COOKIE)) {
			foreach ($_COOKIE as $key => $val) {

               NQ_Cookie::delete($key);

/*
				// need regexp to find the ProxyPass cookies that begin with pcar and psso
 				$key = str_replace("_","%5f",$key);
 				$key = str_replace("=","%3d",$key);
				setcookie($key, false, time() - 3600);
               
                // Unset key
                unset($_COOKIE[$key]);
                NQ_Cookie::delete($key);
*/
			}
		}
		setcookie("user", '', time()-31536000, '/', $_SERVER['HTTP_HOST']);
		
		$session = NQ_Session::singleton(); 
		$message['text'] = 'You have been logged out.';
		$message['type'] = 'warning';
		session_start();
		$_SESSION['message'] = $message;

		if (isset($_GET['ret'])) {
			$go = $this->fr_decrypt($_GET['ret']);
		} else {
			$go = '/';
		}
		
		//$this->redirect('/'); //if cookie was unset, we need a javascript resirect
		
		header("Location: $go");
		exit();
	}

	function __destruct()
	{
		parent::__destruct();
	}
}

?>
