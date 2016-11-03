<?php

/**
* NQ_Presenter_common
*
* @package Framework
* @filesource
*/

/**
* NQ_Presenter_common
*
* A common base class for our presenters (views). All of our presenters must
* extend from this class. If they do not then Presenter::factory() will 
* return an error.
*
* @package Framework
*/
abstract class NQ_Presenter_common extends NQ_Object_Web
{
	protected $module;

	public function __construct(NQ_Module $module)
	{
		parent::__construct();
		$this->module = $module;
		if (SYSTEM_PRESENTER != PRESENTER)
			$this->log->log('using presenter '.SYSTEM_PRESENTER.' ('.PRESENTER.' Mode)', PEAR_LOG_DEBUG);
		else
			$this->log->log('using presenter '.SYSTEM_PRESENTER, PEAR_LOG_DEBUG);
	}

	abstract public function display();

	public function __destruct()
	{
		if ($this->log instanceof Log) {
			$this->log->log('Page was created in ' .$this->totalTime. ' seconds.', PEAR_LOG_NOTICE);
			$this->log->log('## DEBUG MODE END ('. _ID .')##');
			if (NQ_DEBUG_OUTPUT == 'display' && PRESENTER == 'debug') //cannot debug popu in xml mode
				$this->module->log2win();
		}
		parent::__destruct();
	}
}

?>
