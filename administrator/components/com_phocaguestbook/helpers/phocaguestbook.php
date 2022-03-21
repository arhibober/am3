<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ 
jimport('joomla.application.component.controller');

class PhocaGuestbookHelper
{
	/**
	 * Method to get Phoca Version
	 * @return string Version of Phoca Gallery
	 */
	function getPhocaVersion()
	{
		$folder = JPATH_ADMINISTRATOR .DS. 'components'.DS.'com_phocaguestbook';
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.'com_phocaguestbook';
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}
	
	function wordDelete($string,$length,$end) {
		if (JString::strlen($string) < $length || JString::strlen($string) == $length) {
			return $string;
		} else {
			return JString::substr($string, 0, $length) . $end;
		}
	}
	
	function getAliasName($name) {
		
		//$paramsC		= &JComponentHelper::getParams( 'com_phocaguestbook' );
		//$alias_iconv	= $paramsC->get( 'alias_iconv', 0 );
		
		/*$iconv = 0;
		if ($alias_iconv == 1) {
			if (function_exists('iconv')) {
				$name = preg_replace('~[^\\pL0-9_.]+~u', '-', $name);
				$name = trim($name, "-");
				$name = iconv("utf-8", "us-ascii//TRANSLIT", $name);
				$name = strtolower($name);
				$name = preg_replace('~[^-a-z0-9_.]+~', '', $name);
				$iconv = 1;
			} else {
				$iconv = 0;
			}
		}
		*/
		//if ($iconv == 0) {
		$name = JFilterOutput::stringURLSafe($name);
		//}
		
		if(trim(str_replace('-','',$name)) == '') {
			$datenow	= &JFactory::getDate();
			$name 		= $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}
		return $name;
	}
}
?>