<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionJoomlaRegistration{
	var $formname;
	var $formid;
	var $group = array('id' => 'joomla_user', 'title' => 'Joomla User Management');
	var $events = array('success' => 0, 'fail' => 0);
	var $details = array('title' => 'Joomla Registration', 'tooltip' => 'Register some user to Joomla.');
	function run($form, $actiondata){
		$params = new JParameter($actiondata->params);
		$mainframe = JFactory::getApplication();
		// Get required system objects
		$user 		= clone(JFactory::getUser());
		$pathway 	=& $mainframe->getPathway();
		$config		=& JFactory::getConfig();
		$authorize	=& JFactory::getACL();
		$document   =& JFactory::getDocument();
		$language =& JFactory::getLanguage();
        $language->load('com_users');

		// If user registration is not allowed, show 403 not authorized.
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		if($usersConfig->get('allowUserRegistration') == '0' && !$params->get('override_allow_user_registration', 0)){
			JError::raiseError( 403, JText::_( 'Access Forbidden' ));
			return;
		}

		// Initialize new usertype setting
		$userConfig = JComponentHelper::getParams('com_users');
		// Default to Registered.
		$defaultUserGroup = $params->get('new_usertype', '');
		if(empty($defaultUserGroup)){
			$defaultUserGroup = $userConfig->get('new_usertype', array(2));
		}else{
			$_groups = explode(",", trim($defaultUserGroup));
			$defaultUserGroup = array();
			foreach($_groups as $_group){
				$defaultUserGroup[] = (int)$_group;
			}
		}
		
		//set the post fields values
		$form->data['name'] = $form->data[$params->get('name', '')];
		$form->data['username'] = $form->data[$params->get('username', '')];
		$form->data['email'] = $form->data[$params->get('email', '')];
		$form->data['password'] = $form->data[$params->get('password', '')];
		$form->data['password2'] = $form->data[$params->get('password2', '')];
		
		//generate the random pass if enabled
		if((int)$params->get('random_password', 0) == 1){
			jimport('joomla.user.helper');
			$random_pass = JUserHelper::genRandomPassword();
			JRequest::setVar('password', $random_pass);
			JRequest::setVar('password2', $random_pass);
		}
		//check empty fields
		$checks = array('name', 'username', 'email', 'password');
		foreach($checks as $check){
			if(!trim($form->data[$check])){
				$this->events['fail'] = 1;
				$form->validation_errors[$params->get($check)] = 'You must provide your '.$check.'.';
				//return false;
			}
		}
		if($this->events['fail'] == 1){
			return false;
		}
		//check the 2 passwords
		if($form->data['password'] != $form->data['password2']){
			$this->events['fail'] = 1;
			$form->validation_errors[$params->get('password2')] = 'Passwords do NOT match.';
			$form->debug[] = "Couldn't create new user, Passwords do NOT match.";
			return false;
		}
		// Bind the post array to the user object
		if(!$user->bind(JRequest::get('post'), 'usertype')){
			//JError::raiseError( 500, $user->getError());
			$this->events['fail'] = 1;
			$form->validation_errors[] = $user->getError();
			$form->debug[] = "Couldn't bind new user, Joomla returned this error : ".$user->getError();
			return false;
		}

		// Set some initial user values
		$user->set('id', 0);
		$user->set('usertype', 'deprecated');
		$user->set('groups', $defaultUserGroup);

		$date =& JFactory::getDate();
		$user->set('registerDate', $date->toMySQL());

		// If user activation is turned on, we need to set the activation information
		$useractivation = $params->get('useractivation', $usersConfig->get('useractivation'));
		if((int)$useractivation == 1){
			jimport('joomla.user.helper');
			$user->set('activation', JUtility::getHash(JUserHelper::genRandomPassword()));
			$user->set('block', '1');
		}

		// If there was an error with registration, set the message and display form
		if(!$user->save()){
			/*JError::raiseWarning('', JText::_( $user->getError()));
			$this->register();*/
			$this->events['fail'] = 1;
			$form->validation_errors[] = $user->getError();
			$form->debug[] = "Couldn't save new user, Joomla returned this error : ".$user->getError();
			return false;
		}else{
			$this->events['success'] = 1;
		}
		//store user data
		$user_data = (array)$user;
		$removes = array('params', '_params', 'guest', '_errorMsg', '_errors');
		foreach($removes as $remove){
			unset($user_data[$remove]);
		}
		$form->data['_PLUGINS_']['joomla_registration'] = $user_data;
		// Send registration confirmation mail
		$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
		if((int)$params->get('send_joo_activation', 0) == 1){
			$this->_sendMail($user, $password);
		}
		// Everything went fine, set relevant message depending upon user activation state and display message
		if((int)$useractivation == 1){
			$message  = JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE');
		}else{
			$message = JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS');
		}
		
		if($params->get('display_reg_complete', 0) == 1){
			echo $message;
		}
		
		if((int)$params->get('auto_login', 0) == 1){
			$credentials = array();
			$credentials['username'] = $form->data['username'];
			$credentials['password'] = $form->data['password'];
			$mainframe->login($credentials);
		}
	}
	
	function _sendMail(&$user, $password)
	{
		$mainframe = JFactory::getApplication();

		$db		=& JFactory::getDBO();

		$name 		= $user->get('name');
		$email 		= $user->get('email');
		$username 	= $user->get('username');

		$usersConfig 	= &JComponentHelper::getParams( 'com_users' );
		$sitename 		= $mainframe->getCfg( 'sitename' );
		$useractivation = $usersConfig->get( 'useractivation' );
		$mailfrom 		= $mainframe->getCfg( 'mailfrom' );
		$fromname 		= $mainframe->getCfg( 'fromname' );
		$siteURL		= JURI::base();

		$subject 	= sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject 	= html_entity_decode($subject, ENT_QUOTES);

		if ( $useractivation == 1 ){
			$message = sprintf ( JText::_( 'SEND_MSG_ACTIVATE' ), $name, $sitename, $siteURL."index.php?option=com_user&task=activate&activation=".$user->get('activation'), $siteURL, $username, $password);
		} else {
			$message = sprintf ( JText::_( 'SEND_MSG' ), $name, $sitename, $siteURL);
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		// Send email to user
		if ( ! $mailfrom  || ! $fromname ) {
			$fromname = $rows[0]->name;
			$mailfrom = $rows[0]->email;
		}

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message);

		// Send notification to all administrators
		$subject2 = sprintf ( JText::_( 'Account details for' ), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ( $rows as $row )
		{
			if ($row->sendEmail)
			{
				$message2 = sprintf ( JText::_( 'SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
			}
		}
	}
	
	function load($clear){
		if($clear){
			$action_params = array(
				'content1' => '',
				'name' => '',
				'username' => '',
				'email' => '',
				'password' => '',
				'password2' => '',
				'override_allow_user_registration' => 1,
				'new_usertype' => 'Registered',
				'useractivation' => 1,
				'random_password' => 0,
				'auto_login' => 0,
				'send_joo_activation' => 0,
				'display_reg_complete' => 0
			);
		}
		return array('action_params' => $action_params);
	}
}
?>