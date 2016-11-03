<?php

/**
* NQ_Object
*
* @package Framework
* @filesource
*/

require_once( dirname(__FILE__).'/Log.php');
$logconf_file = array('mode' => 0775, 'timeFormat' => '%X %x');
$logconf_display = array(
	'error_prepend' => '<font color="#ff0000"><tt>',
	'error_append'  => '</tt></font>'
	);

/**
* NQ_Object
*
* The base object class for most of the classes that we use in our framework.
* Provides basic logging and set/get functionality.
*
* @package Framework
*/
abstract class NQ_Object
{
	/**
	* $log
	*
	* @var mixed $log Instance of PEAR Log
	*/
	protected $log;

	/**
	* $me
	*
	* @var mixed $me Instance of ReflectionClass
	*/
	protected $me;

	/**
	* $ident
	*
	* @var mixed $ident Timestamp of script runtime for logs
	*/
	protected $ident;

	/**
	* __construct
	*
	* @access public
	*/
	public function __construct()
	{
		if (!defined("_ID"))
			define ('_ID', time().'_'.rand(0,1000) );
		if (PRESENTER == 'debug') {
			switch (NQ_DEBUG_OUTPUT) {
				case 'file':
				case 'display': //display is handled by log parser - see NQ_Module::log2win()
					if (!file_exists(SYSTEM_LOG_FILE))
						touch(SYSTEM_LOG_FILE);
					$this->log = Log::factory('file', SYSTEM_LOG_FILE, _ID, $logconf_display);
					break;
			}
		}else{
			$this->log = Log::factory('null');
		}
		$this->me = new ReflectionClass($this);
	}

	/**
	* setFrom
	*
	* @access public
	* @param mixed $data Array of variables to assign to instance
	* @return void
	*/
	public function setFrom($data)
	{
		if (is_array($data) && count($data)) {
			$valid = get_class_vars(get_class($this));
			foreach ($valid as $var => $val) {
				if (isset($data[$var])) {
					$this->$var = $data[$var];
				}
			}
		} else if( is_object($data) ) {
			$valid = get_class_vars(get_class($this));
			foreach ($valid as $var => $val) {
				if (isset($data->$var)) {
					$this->$var = $data->$var;
				}
			}
		}
	}

	/**
	* toArray
	*
	* @access public
	* @return mixed Array of member variables keyed by variable name
	*/
	public function toArray()
	{
		$defaults = $this->me->getDefaultProperties();
		$return = array();
		foreach ($defaults as $var => $val) {
			if ($this->$var instanceof NQ_Object) {
				$return[$var] = $this->$var->toArray();
			} else {
				$return[$var] = $this->$var;
			}
		}

		return $return;
	}

	/**
	* __destruct
	*
	* @access public
	* @return void
	*/
	public function __destruct()
	{
		if ($this->log instanceof Log) {
			$this->log->close();
		}
	}
}

?>
