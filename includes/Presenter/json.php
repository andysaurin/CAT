<?php

/**
* NQ_Presenter_json
*
* @package Framework
* @filesource
*/

/**
* NQ_Presenter_json
*
* Presenter will automatically take your data and output it in valid JSON.
*
* @package Framework
*/
class NQ_Presenter_json extends NQ_Presenter_common
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
	* Output our data array using PHPs json_encode(). 
	*
	* @return void
	*/
	public function display()
	{
		//prevent caching
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		//send correct app headers
//		header('Content-type: application/json');
		
		$Item = $this->module->getData();
		echo json_encode($Item['json']);
		
	}
	// }}}
	// {{{ indent()
	/**
	* indent
	*
	* Indent our json code properly
	*
	* @return string indented json array
	*/
	function indent($json) {
 
		$result		= '';
		$pos		   = 0;
		$strLen		= strlen($json);
		$indentStr = '  ';
		$newLine   = "\n";
 
		for($i = 0; $i <= $strLen; $i++) {
				
				// Grab the next character in the string
				$char = substr($json, $i, 1);
				
				// If this character is the end of an element, 
				// output a new line and indent the next line
				if($char == '}' || $char == ']') {
						$result .= $newLine;
						$pos --;
						for ($j=0; $j<$pos; $j++) {
								$result .= $indentStr;
						}
				}
				
				// Add the character to the result string
				$result .= $char;
 
				// If the last character was the beginning of an element, 
				// output a new line and indent the next line
				if ($char == ',' || $char == '{' || $char == '[') {
						$result .= $newLine;
						if ($char == '{' || $char == '[') {
								$pos ++;
						}
						for ($j = 0; $j < $pos; $j++) {
								$result .= $indentStr;
						}
				}
		}
 
		return $result;
	}

	// {{{ __destruct()
	public function __destruct()
	{
		parent::__destruct();
	}
	// }}}
}

?>
