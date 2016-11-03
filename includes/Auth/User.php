<?php

/**
* NQ_Auth_User
*
* @package Framework
* @filesource
*/

/**
* NQ_Auth_User
*
* If the module class requires that a user be logged in in order to access
* it then extend it from this Auth class. 
*
* @package Framework
*/
abstract class NQ_Auth_User extends NQ_Auth
{
	function __construct()
	{
		parent::__construct();
	}

	function authenticate()
	{
		return ($this->session->id > 0);
	}

	function __destruct()
	{
		parent::__destruct();
	}
}

?>
