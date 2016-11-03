<?php

/**
* config.php
*
* @package Framework
* @filesource
*/


define('SYSTEM_PAGE_TITLE', 'CAT - A ChIP Association Tester');
define('DOMAIN_NAME', 'cat.biotools.fr');

/**
* MAIL_FROM
*
* Mail From address to use when sending mail
*
* @global constant MAIL_FROM Mail From address
*/
define('MAIL_FROM', '');

/**
* SMTP_USERNAME
*
* SMTP username to use when sending mail
*
* @global constant SMTP_USERNAME SMTP username
*/
define('SMTP_USERNAME', '');

/**
* SMTP_PASSWORD
*
* SMTP password to use when sending mail
*
* @global constant SMTP_PASSWORD SMTP password
*/
define('SMTP_PASSWORD', '');

/**
* SMTP_HOST
*
* SMTP host to use when sending mail
*
* @global constant SMTP_HOST SMTP password
*/
define('SMTP_HOST', '');


/**
* MAX_FILE_SIZE
*
* The maximum size (in bytes) permitted for uploaded files.
* Set to 0 for no limit
*
* @global constant MAX_FILE_SIZE The maximum uploadable filesize in bytes
*/
define('MAX_FILE_SIZE', 100000000); //100MB

/**
* SYSTEM_CORES
*
* The number of cores on the server.
*
* @global constant SYSTEM_CORES The number of cores on the server
*/
define('SYSTEM_CORES', 4);

/**
* LOAD_ALERT
*
* The load at which an alert is shown on the *CAT pages.
*
* @global constant LOAD_ALERT The load at which an alert is shown on the *CAT pages
*/
define('LOAD_ALERT',  (SYSTEM_CORES * 0.66));

/**
* GENE_BEDFILE_MAX_REGIONS
*
* The maximum number of unique gene regions in a gene BED file.
* The higher the number, the longer it takes for GAT to run against modENCODE chip peaks
* Roughly 1000 lines (genes) in a gene BED file takes 15 seconds per modENCODE ChIP dataset
*
* @global constant GENE_BEDFILE_MAX_REGIONS The maximum number of gene regions in a gene BED file.
*/
define('GENE_BEDFILE_MAX_REGIONS', 300);

/**
* BEDTOOLS
*
* The path to the bedtools binary executable
* If the binary cannot be executed by the web server, the script will fail
*
* @global contant BEDTOOLS_PATH The bedtools binary path
*/
define("BEDTOOLS", "/usr/local/bin/bedtools");

/**
* GAT_RUN
*
* The path to the gat-run.py python executable
* If the python script cannot be executed by the web server, the script will fail
*
* @global contant GAT_RUN The gat-run.py path
*/
define("GAT_RUN", "/usr/bin/gat-run.py");

/**
* GAT_RUN_THREADS
*
* gat-run.py can be run in a multi-threaded environment
* set the number of threads to use here - use 1 to disactivate multi-threading
*
* @global contant GAT_RUN_THREADS The number of threads to use when running gat-run.py
*/
define("GAT_RUN_THREADS", 5);

/**
* CRON_MAX_CONCURRENT
*
* How many concurrent cron jobs can be running?
* Set CRON_MAX_CONCURRENT to be :
	number of cores/processors divided by the value set above for GAT-RUN-THREADS
*
* @global contant CRON_MAX_CONCURRENT The max number of concurrent cron jobs that can be run
*/
define("CRON_MAX_CONCURRENT", 3);

/**
* STORE_DATA_TIME
*
* How long, in seconds to store data for
* After this time, the data is automatically deleted by cron
*
* @global constant STORE_DATA_TIME The time in seconds to store data
*/
define("STORE_DATA_TIME", 60*60*24*2); // 2 Days



/*****************************************************
*   NOTHING SHOULD REQUIRE EDITING BELOW THIS LINE   *
******************************************************/

/**
* DS
* PS
*
* defines machine-specific seperators in shorthand
*
* @global constant DS The server directory separator
* @global constant PS The server path separator
*/
define('DS', DIRECTORY_SEPARATOR );
define('PS', PATH_SEPARATOR);

/**
* SYSTEM_DIRECTORY_ROOT
*
* Dynamically figure out where in the filesystem the directory root is located.
*
* @global constant SYSTEM_DIRECTORY_ROOT Absolute path to our framework
*/
define('SYSTEM_DIRECTORY_ROOT', str_replace('/config', '', dirname(__FILE__) ) );

/**
* SYSTEM_DOCUMENT_ROOT
*
* Dynamically figure out where in the filesystem the http document root is located.
* Note, we cannot reply on _SERVER['DOCUMENT_ROOT'] as this is not set when running from the CLI
*
* @global constant SYSTEM_DOCUMENT_ROOT Absolute path to the http document root
*/
if ( !defined('SYSTEM_DOCUMENT_ROOT') )
	define('SYSTEM_DOCUMENT_ROOT', SYSTEM_DIRECTORY_ROOT . "/htdocs" );

/**
* SYSTEM_TMPDIR
*
* The tmp directory where to store uploaded files and to execute scripts in.
* It must be writable by the webserver and not web-accessible
*
* @global constant SYSTEM_TMPDIR Tmp directory
*/
define('SYSTEM_TMPDIR', SYSTEM_DIRECTORY_ROOT . "/tmp" );

/**
* SYSTEM_ENGINE_ROOT
*
* Dynamically figure out where in the filesystem the engine is located.
*
* @global constant SYSTEM_ENGINE_ROOT Absolute path to our framework
*/
define('SYSTEM_ENGINE_ROOT', SYSTEM_DIRECTORY_ROOT );

/**
* SYSTEM_DATA_ROOT
*
* Path to the data directory eg where modENCODE BED files are stored
*
* @global constant SYSTEM_DATA_ROOT
**/
define ('SYSTEM_DATA_ROOT', SYSTEM_DIRECTORY_ROOT.'/data');


/**
* SYSTEM_ERRORDOCS_PATH
*
* Path to error docs - error docs named eg 404.php
*
* @global constant SYSTEM_ERRORDOCS_PATH
**/
define ('SYSTEM_ERRORDOCS_PATH', SYSTEM_DOCUMENT_ROOT.'/errordocs');


/**
* SYSTEM_LOG_FILE
*
* Path to centralized log file that can be accessed directly from our
* application classes.
*
* @global constant SYSTEM_LOG_FILE Path to log file
* @link http://pear.php.net/package/Log
*/
define('SYSTEM_LOG_FILE', SYSTEM_DATA_ROOT.'/logs/debug/log.txt');

/**
* SMARTY_DIR
*
* @global constant SMARTY_DIR Path to Smarty install
* @link http://smarty.php.net
*/
define('SMARTY_DIR', SYSTEM_DIRECTORY_ROOT.'/includes/Smarty/');

/**
* CONF_DIR
*
* @global constant CONF_DIR Path to Config directory
*/
define('SYSTEM_CONFIG_DIR', dirname( __FILE__ ) );

/**
* SENDMAIL_PATH
*
* Path to the sendmail binary
*
* @global constant SENDMAIL_PATH path to sendmail
*/
define('SENDMAIL_PATH', '/usr/sbin/sendmail');

/**
* DISPLAY_XPM4_ERRORS
*
* Display XPM4 Mailer errors?
*
* @global constant DISPLAY_XPM4_ERRORS Display mailer errors boolean
* @link http://xpertmailer.sourceforge.net/documentation/
*/
define('DISPLAY_XPM4_ERRORS', true);

/**
* SYSTEM_PRESENTER
*
* This defines the default view (presenter)
*
* This can be overridden with $_GET['presenter']
*
* @global constant SYSTEM_PRESENTER Default View presenter (smarty, rest, debug)
*/
define('SYSTEM_PRESENTER','smarty');

define('SYSTEM_TEMPLATE','default');

define('SYSTEM_DEBUG_OUTPUT', 'log');
/** include database config file **/

require_once('db.php');

?>
