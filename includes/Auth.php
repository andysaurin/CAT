<?php

/**
* NQ_Auth
*
* @package Framework
* @filesource
*/

/**
* NQ_Auth
*
* Base class of authentication classes. The controller will check to make
* sure your module is an instance of NQ_Auth.
*
* @package Framework
* @see NQ_Module
*/
abstract class NQ_Auth extends NQ_Module
{
	// {{{ __construct()
	function __construct()
	{
		parent::__construct();
	}
	// }}}
	// {{{ authenticate()
	abstract function authenticate();
	// }}}
	// {{{ __destruct()
	function __destruct()
	{
		parent::__destruct();
	}
	// }}}
}

?>
