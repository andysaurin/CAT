<?php

/**
* NQ_Presenter_rss
*
* @package Framework
* @filesource
*/

/**
* NQ_Presenter_rss
*
* Display module's data in valid RSS.
*
* @package Framework
*/
class NQ_Presenter_rss extends NQ_Presenter_common
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
		$array = $this->module->rssGet();

		header("Content-Type: text/xml");
		echo '<?xml version="1.0" encoding="ISO-8859-1" ?>'.
			"\n\n<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n\n";
		
		if (is_array($array)) {
			echo "\t<channel>\n";
			
			if (defined('NQ_PAGETITLE_OVERRIDE')) 
				$page_title = NQ_PAGETITLE_OVERRIDE;
			else
				$page_title = NQ_PAGETITLE_DEFAULT;
			
			if (defined('MO_SUBTITLE'))
				$page_subtitle = MO_SUBTITLE;
			else
				$page_subtitle = NQ_SUBTITLE_DEFAULT;
			
			echo "\t\t<title>". $page_title ."</title>\n".
				"\t\t<link>". NQ_BASE_URL .'/'."</link>\n".
				"\t\t<description>". $page_subtitle ."</description>\n";
						
			echo "\t\t<image>\n".
				"\t\t\t<title>". $page_title ."</title>\n".
				"\t\t\t<link>". NQ_BASE_URL .'/home'."</link>\n".
				"\t\t\t<url>". NQ_BASE_URL ."/images/newlogo.png</url>\n".
				"\t\t</image>\n";

			
			echo "\t\t<ttl>". NQ_RSS_TTL ."</ttl>";
		
			if (is_array($array['movies'])) {
				foreach($array['movies'] as $item) {
					if (strlen($item->description_fr) > NQ_RSS_DESCRIPTIONSIZE ) {
						$offset = NQ_RSS_DESCRIPTIONSIZE;
						while ( !preg_match('/\s$/', substr($item->description_fr, 0, $offset)) ) {
							$offset++;
						}
						$item->description_fr = substr($item->description_fr, 0, $offset) . " ... &lt;a href='". $item->url ."'&gt;Read More&lt;/a&gt;";
					}
					
					$mp4_key = 'high.mp4';
					$flv_key = "dvd.flv";
					
					if ( $item->metadata->$mp4_key->codec == 'h264' ) {
						$format = 'f4v';
						$width = $item->metadata->$mp4_key->width;
						$height = $item->metadata->$mp4_key->height;
					} else {
						$format = 'flv';
						$width = $item->metadata->$flv_key->width;
						$height = $item->metadata->$flv_key->height;
					}
					$item->url = NQ_BASE_URL."/movies/{$item->id}/{$format}/{$item->title}.{$format}";

					echo "\n\t\t<item>\n";
					
					echo "\t\t\t<title>". $item->titre_fr ."</title>\n".
						"\t\t\t<link>". $item->url ."</link>\n".
						"\t\t\t<guid isPermaLink=\"true\">". $item->url ."</guid>\n".
						"\t\t\t<description>".
						"&lt;table border=0 cellpadding=10 cellspacing=0&gt;&lt;tr&gt;".
						"&lt;td&gt;&lt;a href='". $item->url ."' title='". str_replace("'", "", $item->titre_fr) ."'&gt;&lt;img src='http://thumbs.dvdhot.com/t/{$item->dir_name}/{$item->num_scene_ordered}/160/06.jpg' /&gt;&lt;/a&gt;&lt;/td&gt;".
						"&lt;td&gt;". $item->description_fr ."&lt;/td&gt;".
						"&lt;/tr&gt;&lt;/table&gt;\n&lt;hr&gt;\n".
						"\t\t\t</description>\n";
						if ( date("Y-m-d", $item->date_added) > date("Y-m-d"))
							echo "\t\t\t<pubDate>". date("r", mktime(0, 0, 0, date("n"), date("j"), date("Y"))) ."</pubDate>\n";
						else
							echo "\t\t\t<pubDate>". date("r", $item->date_added) ."</pubDate>\n";
						
						echo "\t\t</item>\n";		
				
				}
			}
			echo "\n\t\t".'<atom:link href="http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URL'] .'" rel="self" type="application/rss+xml" />';
			echo "\n\t</channel>\n";
		}
		echo "</rss>\n";
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
