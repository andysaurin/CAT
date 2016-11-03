<?php

/**
* NQ_Presenter_rest
*
* @package Framework
* @filesource
*/

require_once('XML/Serializer.php');

/**
* NQ_Presenter_rest
*
* Presenter will automatically take your data and output it in valid XML.
*
* @package Framework
*/
class NQ_Presenter_rest extends NQ_Presenter_common
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
	* Output our data array using the PEAR package XML_Serializer. This may
	* not be the optimal output for your REST API, but it should
	* display valid XML that can be easily consumed by anyone.
	*
	* @return void
	* @link http://pear.php.net/package/XML_Serializer
	*/
	public function display()
	{
		$defaultTag = 'Item';
		
		header("Content-Type: text/xml");
		$serializer_options = array ( 
			'addDecl' => TRUE, 
			'encoding' => 'ISO-8859-1', 
			'indent' => '  ', 
			'defaultTagName' => $defaultTag,
			'rootName' => 'root',
			'mode' => 'simplexml',
		);
		$xml = new XML_Serializer($serializer_options);
		$Item = $this->module->getData();
		//unset any private data to not show in XML
		if (is_array($Item['private']) && count($Item['private']))
			unset($Item['private']);
		$xml->serialize($Item);

		echo $xml->getSerializedData();
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
