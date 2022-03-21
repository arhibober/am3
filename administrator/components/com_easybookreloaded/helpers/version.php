<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.helper' );

class EasybookReloadedHelperVersion extends JObject 
{
	var $_current;
	
	function __construct()
	{
		$this->_loadVersion();
	}
	
	function _loadVersion()
	{
		require_once( JPATH_COMPONENT.DS.'libraries'.DS.'httpclient.class.php' );
		$client = new HttpClient('www.easy-joomla.org');
		
			if (!$client->get('/components/com_versions/directinfo.php?catid=3')) {
				$this->setError($client->getError());
				return false;
			}
		
		$this->_current = $client->getContent();
		return true;
	}
	
	function checkVersion($version)
	{
		if ($version == $this->_current) {
			return 1;
		} elseif($this->getErrors()) {
			return -2;
		} else {
			return -1;
		}
	}
	
	function getVersion()
	{
		return $this->_current;
	}
	
}
?>
