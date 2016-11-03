<?php

/**
* NQ_Module
*
* @package Framework
* @filesource
*/

/**
* NQ_Module
*
* The base module class. All applications will extends from this class. This
* means each module will, by default, have an open DB connection and an
* open log file to write to. Also, it's a good place to put functions,
* variables, etc. that all modules need.
*
* @package Framework
*/


abstract class NQ_Module extends NQ_Object_Web
{
	// {{{ properties
	/**
	* $presenter
	*
	* Used in NQ_Presenter::factory() to determine which presentation (view)
	* class should be used for the module.
	*
	* @var string $presenter
	* @see NQ_Presenter, NQ_Presenter_common, NQ_Presenter_smarty
	*/
	public $presenter;

	/**
	* $data
	*
	* Data set by the module that will eventually be passed to the view.
	*
	* @var mixed $data Module data
	* @see NQ_Module::set(), NQ_Module::getData()
	*/
	protected $data = array();
	/**
	* $name
	*
	* @var string $name Name of module class
	*/
	public $name;

	/**
	* $tplFile
	*
	* @var string $tplFile Name of template file
	* @see NQ_Presenter_smarty
	*/
	public $tplFile;

	/**
	* $moduleName
	*
	* @var string $moduleName Name of requested module
	* @see NQ_Presenter_smarty
	*/
	public $moduleName = null;

	/**
	* $pageTemplateFile
	*
	* @var string $pageTemplateFile Name of outer page template
	*/
	public $pageTemplateFile = null;
	// }}}
	// {{{ __construct()
	/**
	* __construct
	*
	*/
	public function __construct()
	{
		parent::__construct();

		if (PRESENTER == 'debug') {
			$this->presenter = SYSTEM_PRESENTER;
			$this->log->log('## DEBUG MODE START (' . _ID . ')##');
		} else {
			$this->presenter = PRESENTER; // smarty/rest
		}
		if ( ( (isset($_GET['ajax']) && $_GET['ajax']==1) || (isset($_GET['body']) && $_GET['body']==1) ) && !defined('MO_BODYONLY')) //ajax(y) call - show only body contents
			define('MO_BODYONLY', 1);

		if ( isset($_GET['nosidebar']) && $_GET['nosidebar'] == 1 ) {
			define('MO_NOSIDEBAR', 1);
		}
		$this->name = $this->me->getName();
		$this->tplFile = $this->name.'.tpl';
	}
	// }}}
	// {{{ __default()
	/**
	* __default
	*
	* This function is ran by the controller if an event is not specified
	* in the user's request.
	*
	*/
	abstract public function __default();
	// }}}
	// {{{ set($var,$val)
	/**
	* set
	*
	* Set data for your module. This will eventually be passed to the
	* presenter class via NQ_Module::getData().
	*
	* @param string $var Name of variable
	* @param mixed $val Value of variable
	* @return void
	* @see NQ_Module::getData()
	*/
	protected function set($var,$val) {
		$this->data[$var] = $val;
	}
	// }}}
	// {{{ getData()
	/**
	* getData
	*
	* Returns module's data.
	*
	* @return mixed
	* @see NQ_Presenter_common
	*/
	public function getData()
	{
		return $this->data;
	}
	// }}}

	protected function rssSet($var,$val)
	{
		if ($var == 'add_item')
			$this->rss['items'][] = $val;
		else
			$this->rss[$var] = $val;
	}
	public function rssGet()
	{
		return $this->rss;
	}
	// {{{ redirect()
	/**
	* redirect
	*
	* Header-independent Javascript-dependent browser redirect
	*
	* @return Void (Browser redirect)
	*/
	function redirect($location) {
		echo"<script language=\"JavaScript\"><!--
			var time = null
			window.location = '$location'
			//-->
			</script>";
		exit(0);
	}
	// }}}
	// {{{ cleanup()
	/**
	* cleanup
	*
	* cleans up a string, taking into account MS WORD special characters
	*
	* @return string cleaned string
	*/
	public function cleanup($str)
	{
		if (is_array($str)) {
			foreach ($str as $key=>$val) {
				$str[$key] = $this->cleanup($val);
			}
		} else {
			$str = html_entity_decode($str, ENT_QUOTES);
			//$str = stripslashes($str);
			$str = strtr($str, get_html_translation_table(HTML_ENTITIES));
			$str = str_replace( array("\x82", "\x84", "\x85", "\x91", "\x92", "\x93", "\x94", "\x95", "\x96",  "\x97"), array("&#8218;", "&#8222;", "&#8230;", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8226;", "&#8211;", "&#8212;"),$str);
		}
		return $str;
	}
	// }}}
	// {{{ nq_encrypt()
	/**
	* nq_encrypt
	*
	* encrypts a string with XXTEA
	*
	*
	* @return string encrypted string
	*/
	public function nq_encrypt($key, $string)
	{
		$this->key = $key;
		$tea = new NQ_Tea();
		$tea->setKey($key);
		$encrypted = $tea->encrypt($string);
		return urlencode(base64_encode($encrypted));

/*
		// from http://www.zayinkrige.com/blowfish-encryption-between-php-java/

		$cipher = mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
    	// The block-size of the Blowfish algorithm is 64-bits, therefore our IV
    	// is always 8 bytes:
    	$iv =  '12345678';
    	// The strengh of the encryption is determined by the length of the key
    	// passed to mcrypt_generic_init
    	if (mcrypt_generic_init($cipher, $key, $iv) != -1)
    	{
        	$cipherText = mcrypt_generic($cipher, $text);
        	mcrypt_generic_deinit($cipher);
        	return bin2hex($cipherText);
    	} else {
        	mcrypt_generic_deinit($cipher);
        	return "";
    	}
*/
	}
	// }}}
	// {{{ nq_decrypt()
	/**
	* nq_decrypt
	*
	* decrypts a string with XXTEA
	*
	*
	* @return string decrypted string
	*/
	public function nq_decrypt($key, $enc_string)
	{
		$tea = new NQ_Tea();
		$tea->setKey($key);
		return $tea->decrypt(base64_decode($enc_string));

/*
    	$iv =  '12345678';
    	$text = trim(NQ_Module::hex2bin($text));
    	return mcrypt_cbc(MCRYPT_BLOWFISH, $key, $text, MCRYPT_DECRYPT, $iv);
*/
	}
	// }}}
	public function hex2bin($h)
	{
    	//this function is the opposite of php's bin2hex
   		if (!is_string($h)) return null;
    	$r='';
    	for ($a=0; $a<strlen($h); $a+=2) { $r.=chr(hexdec($h{$a}.$h{($a+1)})); }
    	return $r;
	}
	// }}}



	/**
	 * Encryption using blowfish algorithm
	 *
	 * @param   string  original data
	 * @param   string  the secret
	 *
	 * @return  string  the encrypted result
	 *
	 * @access  public
	 *
	 * @author  lem9
	 */
	public function blowfish_encrypt($data, $secret) {
	    $pma_cipher = new NQ_Blowfish;
	    $encrypt = '';
	    for ($i=0; $i<strlen($data); $i+=8) {
	        $block = substr($data, $i, 8);
	        if (strlen($block) < 8) {
	            $block = $this->full_str_pad($block,8,"\0", 1);
	        }
	        $encrypt .= $pma_cipher->encryptBlock($block, $secret);
	    }
	    return base64_encode($encrypt);
	}

	/**
	 * Decryption using blowfish algorithm
	 *
	 * @param   string  encrypted data
	 * @param   string  the secret
	 *
	 * @return  string  original data
	 *
	 * @access  public
	 *
	 * @author  lem9
	 */
	public function blowfish_decrypt($encdata, $secret) {
	    $pma_cipher = new NQ_Blowfish;
	    $decrypt = '';
	    $data = base64_decode($encdata);
	    for ($i=0; $i<strlen($data); $i+=8) {
	        $decrypt .= $pma_cipher->decryptBlock(substr($data, $i, 8), $secret);
	    }
	    return trim($decrypt);
	}

	/**
	 * String padding
	 *
	 * @param   string  input string
	 * @param   integer length of the result
	 * @param   string  the filling string
	 * @param   integer padding mode
	 *
	 * @return  string  the padded string
	 *
	 * @access  public
	 */
	public function full_str_pad($input, $pad_length, $pad_string = '', $pad_type = 0) {
	    $str = '';
	    $length = $pad_length - strlen($input);
	    if ($length > 0) { // str_repeat doesn't like negatives
	        if ($pad_type == STR_PAD_RIGHT) { // STR_PAD_RIGHT == 1
	            $str = $input.str_repeat($pad_string, $length);
	        } elseif ($pad_type == STR_PAD_BOTH) { // STR_PAD_BOTH == 2
	            $str = str_repeat($pad_string, floor($length/2));
	            $str .= $input;
	            $str .= str_repeat($pad_string, ceil($length/2));
	        } else { // defaults to STR_PAD_LEFT == 0
	            $str = str_repeat($pad_string, $length).$input;
        }
	    } else { // if $length is negative or zero we don't need to do anything
	        $str = $input;
	    }
	    return $str;
	}

	// {{{ isValid($module)
	/**
	* isValid
	*
	* Determines if $module is a valid framework module. This is used by
	* the controller to determine if the module fits into our framework's
	* mold. If it extends from both NQ_Module and NQ_Auth then it should be
	* good to run.
	*
	* @static
	* @param mixed $module
	* @return bool
	*/
	public static function isValid($module)
	{
		return (is_object($module) &&
				$module instanceof NQ_Module &&
				$module instanceof NQ_Auth);
	}
	// }}}
	// {{{ errorDoc($type, $log)
	/**
	* errorDoc
	*
	* Throws an apache error document
	*
	* @static
	* @param string $type Error Document type
	* @param mixed $log Text to log
	* @return exit(0)
	*/
	public function errorDoc($type, $log)
	{
		switch ($type) {
			case '400': $txt = '400 Bad Request'; break;
			case '403': $txt = '403 Forbidden'; break;
			case '404': $txt = '404 Not Found'; break;
			case '503': $txt = '503 Temporarily Unavailable'; break;

		}
		header('HTTP/1.1 '.$txt);
		if (file_exists(NQ_ERRORDOCS_PATH.'/'.$type.'.php'))
			@include_once(NQ_ERRORDOCS_PATH.'/'.$type.'.php');

		$this->log->log($log, PEAR_LOG_ERROR);
		exit(0);

	}
	// }}}
	// {{{
	/**
	* array_find
	*
	* Searches for a string in every value of a one-dimensional array.
	*
	* @param string $needle.
	* @param mixed $haystack Array to search in.
	* @return mixed Index of the _first_ match, or FALSE if no match is found.
	*/
	public function array_find($pattern, $haystack)
	{
		for($i = 0; $i < count($haystack); $i++) {
			if (strpos($haystack[$i], $pattern))
				return $i;
		}
		return false;
	}
	// }}}
	// {{{ logQuery
	/**
	* logQuery
	*
	* Logs the last ezSQL mysql query, function call, and numrows
	*
	* @return void
	*/
	public function logQuery($conn) {
		$toLog = "<span><b>$conn MySQL Query</b> [".$this->$conn->num_queries."]<b>:</b> ".($this->$conn->last_query?preg_replace('|\n|', '', $this->$conn->last_query):"NULL")."</span><br>";
		if ($this->$conn->from_disk_cache == true)
			$toLog .= '<span><b><font color="orange">Using disk cache...</font></b></span><br />';
		else
			$toLog .= '<span><b>'.$conn.' Function Call:</b> ' . ($this->$conn->func_call?preg_replace('|\n|', '', $this->$conn->func_call):"None") . '</span><br>';
		if (count($this->$conn->last_result) < 1)
			$toLog .= '<span class="l'.PEAR_LOG_WARNING.'">';
		else
			$toLog .= '<span class="l'.PEAR_LOG_NOTICE.'">';
		$toLog .= '<b>Rows Returned:</b> '. count($this->$conn->last_result) .' in '. $this->$conn->query_time .' secs</span>';
		if ($this->$conn->last_error) {
			$toLog .= '<br><span class="l'.PEAR_LOG_EMERG.'">'.$this->$conn->last_error.'</span>';
			unset($this->$conn->last_error);
		}
		$this->log->log($toLog, PEAR_LOG_DEBUG);
	}
	// }}}
	// {{{ log2win
	/**
	* log2win
	*
	* Creates a log window popup if PRESENTER==debug && NQ_DEBUG_OUTPUT=display
	*
	* @param string $type Error Document type
	* @param mixed $log Text to log
	* @return void
	*/
	public function log2win()
	{
		if ($this->tplFile == 'index.tpl')
			$tplFile = $this->moduleName . '.tpl';
		else
			$tplFile = $this->moduleName .  ucfirst($this-tplFile);
		$log = file(SYSTEM_LOG_FILE);

		$start = $this->array_find('DEBUG MODE START ('. _ID .')', $log);
		$end = $this->array_find('DEBUG MODE END ('. _ID .')', $log);
		preg_match('/^([a-zA-Z]{3} [0-9]{2} [0-9:]{8})/', $log[$start], $start_time);
		preg_match('/^([a-zA-Z]{3} [0-9]{2} [0-9:]{8})/', $log[$end], $end_time);
		if ($start && $end) {
			$log = array_slice($log, $start+1, ($end-1)-$start);
		}
		$styles = array(
                        PEAR_LOG_EMERG   => 'background-color: red; color:yellow;',
                        PEAR_LOG_ALERT   => 'background-color: orange;',
                        PEAR_LOG_CRIT    => 'background-color: yellow; color:red;',
                        PEAR_LOG_ERR     => 'background-color: violet; color:yellow;',
                        PEAR_LOG_WARNING => 'background-color: #ff9900; color:blue;',
                        PEAR_LOG_NOTICE  => 'background-color: blue; color:yellow;',
                        PEAR_LOG_INFO    => 'background-color: #CCCCCC;',
                        PEAR_LOG_DEBUG   => 'background-color: green; color:yellow;'
                    );
		$win = 'debugLog';

		$logOut = array();
		$logOut[] = '<tr class="l'.PEAR_LOG_INFO.'"><td nowrap>'.$end_time[1].'</td><td align=center>info</td><td>Debug Log Ended</td></tr>';
		krsort($log);
		foreach ($log as $entry) {
			preg_match('/^([a-zA-Z]{3} [0-9]{2} [0-9:]{8}).[^\[]+\[([a-zA-Z]+)\] (.*)/', $entry, $matches);
			$ident = strtoupper($matches[2]);
			switch ($ident) {
				case 'ERROR':
					$ident = 'ERR';
					break;
				case 'EMERGENCY':
					$ident = 'EMERG';
					break;
				case 'CRITICAL':
					$ident = 'CRIT';
					break;
			}
			$ident = "PEAR_LOG_{$ident}";
			$logOut[] = '<tr class="l'.constant($ident).'"><td nowrap>'.$matches[1].'</td><td align=center>'.$matches[2].'</td><td>'.addslashes($matches[3]).'</td></tr>';
		}
		$logOut[] = '<tr class="l'.PEAR_LOG_INFO.'"><td nowrap>'.$start_time[1].'</td><td align=center>info</td><td>Debug Log Started</td></tr>';
		foreach ($logOut as $html) {
			$_html .= $win.".document.writeln('".$html."');\n";
		}
		echo <<< EOT
		<script language="JavaScript">
		$win = window.open('', '{$win}', 'toolbar=no,scrollbars,width=700,height=600');
		$win.document.writeln('<html>');
		$win.document.writeln('<head>');
		$win.document.writeln('<title>Runtime Log for {$this->presenter} {$tplFile}</title>');
		$win.document.writeln('<style type="text/css">');
		$win.document.writeln('body { font-family: monospace; font-size: 8pt; }');
		$win.document.writeln('td,th { font-size: 8pt; }');
		$win.document.writeln('td,th { border-bottom: #999999 solid 1px; }');
		$win.document.writeln('td,th { border-right: #999999 solid 1px; }');
		$win.document.writeln('tr { text-align: left; vertical-align: top; }');
		$win.document.writeln('.l0 { $styles[0] }');
		$win.document.writeln('.l1 { $styles[1] }');
		$win.document.writeln('.l2 { $styles[2] }');
		$win.document.writeln('.l3 { $styles[3] }');
		$win.document.writeln('.l4 { $styles[4] }');
		$win.document.writeln('.l5 { $styles[5] }');
		$win.document.writeln('.l6 { $styles[6] }');
		$win.document.writeln('.l7 { $styles[7] }');
		$win.document.writeln('</style>');
		$win.document.writeln('</head>');
		$win.document.writeln('<body>');
		$win.document.writeln('<table border="0" cellpadding="2" cellspacing="0">');
		$win.document.writeln('<tr><th>Time</th>');
		$win.document.writeln('<th>Priority</th><th width="100%">Message</th></tr>');
		$_html
		$win.document.writeln('</table>');
		$win.document.writeln('</body></html>');
		$win.document.close();
		</script>
EOT;
	}
	// }}}

	// {{{ sec2time()
	/*
	* sec2time
	*
	* returns a formatted HH:mm:ss value for seconds
	* @param string $seconds Number of seconds to convert
	* @return formatted time
	*/
	public function sec2time($seconds, $long=false) {
    	$hours = floor($seconds / 3600);
    	$minutes = floor($seconds % 3600 / 60);
    	$seconds = $seconds % 60;

		if ($long ===true) {
			return sprintf("%d hrs %02d mins %02d secs", $hours, $minutes, $seconds);
		} else {
	    	return sprintf("%d:%02d:%02d", $hours, $minutes, $seconds);
		}
	}
	// }}}

	// {{{ bytesToSize()
	/**
	* bytesToSize
	*
 	* Convert bytes to human readable format
 	*
 	* @param integer bytes Size in bytes to convert
 	* @return string
	 */
	public function bytesToSize($bytes, $precision = 2)
	{
	    $kilobyte = 1024;
    	$megabyte = $kilobyte * 1024;
    	$gigabyte = $megabyte * 1024;
    	$terabyte = $gigabyte * 1024;

    	if (($bytes >= 0) && ($bytes < $kilobyte)) {
        	return $bytes . ' B';

    	} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        	return round($bytes / $kilobyte, $precision) . ' KB';

    	} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        	return round($bytes / $megabyte, $precision) . ' MB';

    	} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        	return round($bytes / $gigabyte, $precision) . ' GB';

    	} elseif ($bytes >= $terabyte) {
        	return round($bytes / $gigabyte, $precision) . ' TB';
    	} else {
        	return $bytes . ' B';
    	}
	}

	public function regex_escape($str)
	{
		$str = str_ireplace('(', '\(', $str);
		$str = str_ireplace(')', '\)', $str);
		$str = str_ireplace(']', '\]', $str);
		$str = str_ireplace('[', '\[', $str);
		$str = str_ireplace('-', '\-', $str);
		$str = str_ireplace('.', '\.', $str);
		$str = str_ireplace('?', '\?', $str);
		$str = str_ireplace('"', '\"', $str);

		return $str;

	}

	/* convert Windows CRLF/CR to LF */
	public function normalise_linefeeds($s) {
		// Normalize line endings using Global
		// Convert all line-endings to UNIX format
		$s = str_replace(CRLF, LF, $s);
		$s = str_replace(CR, LF, $s);
		// Don't allow out-of-control blank lines
		$s = preg_replace("/\n{2,}/", LF . LF, $s);
		return $s;
	}

	// {{{ __destruct()
	public function __destruct()
	{
		parent::__destruct();
	}
	// }}}
}

?>
