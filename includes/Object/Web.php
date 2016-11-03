<?php

 /**
* NQ_Object_Web
*
* @package Framework
* @filesource
*/

/**
* NQ_Object_Web
*
* This is the base class for web applications extended from NQ_Module, which
* includes all module classes. Sets up a current user and session.
*
* @package Framework
*/
abstract class NQ_Object_Web extends NQ_Object_DB
{
	// {{{ properties
	/**
	* $user
	*
	* This is the current user. If the user is not logged in then the
	* information defaults to the special anonymous user (userID = 0).
	*
	* @var mixed $user Instnace of NQ_User of current user
	*/
	protected $user;

	/**
	* $session
	*
	* A simple wrapper class around PHP's $_SESSION variable.
	*
	* @var mixed $session Instance of NQ_Session
	*/
	protected $session;
	// }}} 
	public function __construct()
	{
		parent::__construct();
		$this->user = new NQ_User();
		$this->session = NQ_Session::singleton();

	}

	public function __destruct()
	{
		parent::__destruct();
	}
}

?>
