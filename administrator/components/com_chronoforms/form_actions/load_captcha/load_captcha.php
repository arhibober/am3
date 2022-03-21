<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionLoadCaptcha{
	var $formname;
	var $formid;
	var $group = array('id' => 'core_captcha', 'title' => 'Core Captcha');
	var $details = array('title' => 'Load Recaptcha', 'tooltip' => 'Load the Recaptcha image');
	function run($form, $actiondata){
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
		$params = new JParameter($actiondata->params);
		$CF_PATH = ($mainframe->isSite()) ? JURI::Base() : $uri->root();
		$uri =& JFactory::getURI();
		if($uri->isSSL()){
			$CF_PATH = str_replace('http:', 'https:', $CF_PATH);
		}
		$form->form_details->content = str_replace('{chronocaptcha_img}', '  <img src="'.$CF_PATH.'components/com_chronoforms/chrono_verification.php?imtype='.$params->get('fonts').'" alt="" />', $form->form_details->content);
	}
	
	function load($clear){
		if($clear){
			$action_params = array(
								'fonts' => 0
			);
		}
		return array('action_params' => $action_params);
	}
}
?>