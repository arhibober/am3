<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionMultiLanguage{
	var $formname;
	var $formid;
	var $group = array('id' => 'form_utilities', 'title' => 'Utilities');
	var $details = array('title' => 'Multi Language', 'tooltip' => 'Different language simple string replacer.');
	function run($form, $actiondata){
		$params = new JParameter($actiondata->params);
		
	}
	
	function load($clear){
		if($clear){
			$action_params = array(
				'content1' => '',
				'lang_tag' => ''
			);
		}
		return array('action_params' => $action_params);
	}
}
?>