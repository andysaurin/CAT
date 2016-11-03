<?php

/**
* NQ_Presenter_rest
*
* @package Framework
* @filesource
*/

/**
* NQ_Presenter_rest
*
* Presenter will automatically take your data and output it in valid XML.
*
* @package Framework
*/
class NQ_Presenter_none extends NQ_Presenter_common
{
	// {{{ __construct(NQ_Module $module)
	/**
	* __construct
	*
	* @access public
	* @param mixed $module Instance of NQ_Module
	* @return void
	*/
	public function __construct(NQ_Module $module)
	{
		parent::__construct($module);
	}
	// }}}
	// {{{ display()
	/**
	* display
	*
	* @return void
	* @link http://pear.php.net/package/XML_Serializer
	*/
	public function display()
	{
	}
	// }}}
	// {{{ __destruct()
	public function __destruct()
	{
		parent::__destruct();
	}
	// }}}
}

?>
