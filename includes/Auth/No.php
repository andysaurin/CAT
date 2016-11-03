<?php

/**
* NQ_Auth_No
*
* @package Framework
* @filesource
*/

/**
* NQ_Auth_No
*
* If the module class does not require any authentication then it should
* extend from this authentication module.
*
* @package Framework
*/
abstract class NQ_Auth_No extends NQ_Auth
{
	function __construct()
	{
		parent::__construct();
	}

	function authenticate()
	{
		return true;
	}

	function __destruct()
	{
		parent::__destruct();
	}
}

?>
