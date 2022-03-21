<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionEmailVerificationSender{
	var $formname;
	var $formid;
	var $group = array('id' => 'email_verification', 'title' => 'Email Verification');
	var $details = array('title' => 'Email Verification', 'tooltip' => 'Sends the email and saves the data into DB.');
	function run($form, $actiondata){
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();		
		$params = new JParameter($actiondata->params);
		//save the data to db
		$db_save_details = $actiondata;
		$db_save_details->type = 'db_save';
		$db_save_details->params = 'table_name='.$params->get('table_name');
		$form->data[trim($params->get('verify_field', 'verify'))] = md5(uniqid(rand(), true));
		$form->data[trim($params->get('verification_status_field', 'verified'))] = 0;
		$form->runAction($db_save_details);
		//add the verification link value to the data array
		$CF_PATH = ($mainframe->isSite()) ? JURI::Base() : $uri->root();
		$form->data['verification_link'] = $params->get('verification_link_path', $CF_PATH.'index.php?option=com_chronoforms&amp;chronoform='.$form->form_name);
		$form->data['verification_link'] .= '&amp;action=verify&amp;hash='.$form->data[trim($params->get('verify_field', 'verify'))];
	}
	
	function load($clear){
		if($clear){
			$action_params = array(
				'table_name' => '',
				'verify_field' => '',
				'verification_status_field' => '',
				'verification_link_path' => ''
			);
		}
		return array('action_params' => $action_params);
	}
}
?>