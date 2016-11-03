<?php

/**
* NQ_Presenter
*
* @package Framework
* @filesource
*/

/**
* NQ_Presenter
*
* Presenter factory class. This is used by the controller file, in 
* conjunction with the NQ_Module::$presenter variable to produced the
* desired presenter class.
*
* @package Framework
* @see NQ_Module::$presenter, NQ_Presenter_common
*/
class NQ_Presenter
{
	// {{{ factory($type,NQ_Module $module)
	/**
	* factory
	*
	* @access public
	* @param string $type Presentation type (our view)
	* @param mixed $module Our module, which the presenter will display
	* @return mixed PEAR_Error on failure or a valid presenter
	* @static
	*/
	static public function factory($type,NQ_Module $module)
	{
		$file = SYSTEM_DIRECTORY_ROOT.'/includes/Presenter/'.$type.'.php';
		if (include($file)) {
			$class = 'NQ_Presenter_'.$type;
			if (class_exists($class)) {
				$presenter = new $class($module);
				if ($presenter instanceof NQ_Presenter_common) {
					return $presenter;
				}

				return PEAR::raiseError('Invalid presentation class: '.$type);
			}

			return PEAR::raiseError('Presentation class not found: '.$type);
		}

		return PEAR::raiseError('Presenter file not found: '.$type);
	}
	// }}}
}

?>
