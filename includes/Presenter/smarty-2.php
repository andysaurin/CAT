<?php
/**
* NQ_Presenter_smarty
*
* @package Framework
* @filesource
*/

require_once(SMARTY_DIR.'Smarty.class.php');


/**
* NQ_Presenter_smarty
*
* By default we use Smarty as our websites presentation layer (view).
*
* @package Framework
* @link http://smarty.php.net
*/
class NQ_Presenter_smarty extends NQ_Presenter_common
{
	private $template = null;
	private $path = null;

	public function __construct(NQ_Module $module)
	{
		parent::__construct($module);
		$this->path = NQ_DOC_ROOT.'/templates/'.NQ_TEMPLATE;
		$this->template = new Smarty();
		$this->template->plugins_dir = array('plugins',NQ_DIR_ROOT.'/modules/smarty_common/plugins');
		$this->template->template_dir = $this->path.'/'.'templates';
		$this->template->compile_dir = $this->path.'/'.'compile';
		$this->template->cache_dir = $this->path.'/'.'cache';
		//$this->template->config_dir = $this->path.'/'.'config';

		/**
		* NQ_Presenter_smarty
		*
     	* Enables template caching.
     	* <ul>
     	*  <li>0 = no caching</li>
     	*  <li>1 = use class cache_lifetime value</li>
     	*  <li>2 = use cache_lifetime in cache file</li>
     	* </ul>
     	* @var integer
     	*/
		$this->template->caching = 0;

	    /**
     	* NQ_Presenter_smarty
		*
		* This forces templates to compile every time. Useful for development
     	* or debugging.
     	*
     	* @var boolean
    	*/
		$this->template->force_compile = false;
	}

	public function display()
	{
		$this->tplFile = $this->module->moduleName . '/' . $this->module->tplFile;

		$this->template->assign('templatePath',$this->template->template_dir);
		$this->template->assign('tplFile',$this->tplFile);
		$this->template->assign('user',$this->user);
		$this->template->assign('session',$this->session);

		if (defined('MO_SECTION_HEADING'))
			$this->template->assign('SectionHeading',MO_SECTION_HEADING);

		if (defined('NQ_PAGETITLE_OVERRIDE')) {
			if (defined('MO_SUBTITLE'))
				$this->template->assign('PageTitle', NQ_PAGETITLE_OVERRIDE ." - ". MO_SUBTITLE);
			else
				$this->template->assign('PageTitle', NQ_PAGETITLE_OVERRIDE ." - ". NQ_SUBTITLE_DEFAULT);
		} elseif (defined('MO_SUBTITLE'))
			$this->template->assign('PageTitle', NQ_PAGETITLE_DEFAULT ." - ". MO_SUBTITLE);
		else
			$this->template->assign('PageTitle', NQ_PAGETITLE_DEFAULT);
		die(MO_NOSIDEBAR);
		if (defined('MO_NOSIDEBAR')) {
			$this->template->assign('hide_sidebar', 1); 
		}
		
		foreach ($this->module->getData() as $var => $val) {
			if (!in_array($var,array('path','tplFile'))) {
				$this->template->assign($var,$val);
			}
		}

		if ($this->module->pageTemplateFile == null) {
			$pageTemplateFile = 'common/page.tpl';
		} else {
			$pageTemplateFile = $this->module->pageTemplateFile;
		}

		if (!defined('MO_BODYONLY')) {

			/**
			* NQ_Presenter_smarty
			*
			* include any further common template variables
			* Included smarty_common modules inherit this class and variables set within
			* will only show in the smarty templates (and importantly not in any other presenters, eg rest)
			*
			* useful for common content (ie menus, headers, footers etc)
			* @package Framework
			* @global string smarty_common Common files to be included in all Smarty calls where MO_BODYONLY is not defined
			*/
			$smarty_common = array(
						'global',
						);

			foreach ($smarty_common as $file) {
				@include_once(NQ_DIR_ROOT.'/modules/smarty_common/'.$file.'.config.php');
				require_once(NQ_DIR_ROOT.'/modules/smarty_common/'.$file.'.php');
			}


		} else {
			$this->template->assign('ShowBodyOnly', 1);
		}

		//headers
		header("X-Powered-By: BioTools.fr/".NQ_VERSION);

		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$this->totalTime = ($endtime - $this->module->startTime);
		$this->template->assign('renderTime', $this->totalTime);

		if (PRESENTER == 'debug' && NQ_DEBUG_OUTPUT == 'display' ) {
			$this->template->debugging = true;
			$this->template->assign ('debugMode', NQ_DEBUG_OUTPUT);
		}
		$this->template->display($pageTemplateFile);
		$this->log->log('# SMARTY: Display Templates #');
	}

	public function __destruct()
	{
		parent::__destruct();
	}
}

?>
