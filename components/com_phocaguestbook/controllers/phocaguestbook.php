<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport('joomla.mail.helper');

class PhocaGuestbookControllerPhocaGuestbook extends PhocaGuestbookController
{
	function __construct() {
		parent::__construct();
		$this->registerTask('submit', 'submit');
		$this->registerTask('delete', 'remove');
		$this->registerTask('unpublish', 'unpublish');
	}
	
	function display() {
		parent::display();
	}

	function submit() {
		
		$app	= JFactory::getApplication();
		$user 	= JFactory::getUser();
		$db 	= JFactory::getDBO();
		$uri 	= JFactory::getURI();
		$tmpl	= array();
		
		$redSpam = 'index.php?option=com_phocaguestbook&view=empty';//temp, does not work if sef enabled
		
		$token	= JUtility::getToken();
		if (!JRequest::getInt( $token, 0, 'post' )) {
			$app->redirect(JRoute::_('index.php', false), JText::_('JINVALID_TOKEN'));
			exit;
		}
		
		$paramsC 					= JComponentHelper::getParams('com_phocaguestbook') ;//Add requirement
		$tmpl['session_suffix']		= $paramsC->get('session_suffix');
		
		//Get Session Data (we have saved new session, because we want to check captcha
		$session 					=& JFactory::getSession();
		$phoca_guestbook_session 	= $session->get('pgbsess'.$tmpl['session_suffix']);
		
		// - - - - - - - - - - 
		//Some POST data can be required or not, If yes, set message if there is POST data == ''
		//Get the params, e.g. if we define in params, that e.g. title can be "", we will not check it
		//if params doesn't exist it will be required, if exists and is required (1) it is required
		
		
		$tmpl['display_title_form'] 		= $paramsC->get( 'display_title_form', 2 );
		$tmpl['display_name_form'] 			= $paramsC->get( 'display_name_form', 2 );
		$tmpl['display_email_form']	 		= $paramsC->get( 'display_email_form', 1 );
		$tmpl['display_website_form'] 		= $paramsC->get( 'display_website_form', 0 );
		$tmpl['display_content_form'] 		= $paramsC->get( 'display_content_form', 2 );
		$tmpl['max_char'] 					= $paramsC->get( 'max_char', 2000 );
		$tmpl['send_mail'] 					= $paramsC->get( 'send_mail', 0 );
		$tmpl['registered_users_only'] 		= $paramsC->get( 'registered_users_only', 0 );
		$tmpl['enable_captcha']	 			= $paramsC->get( 'enable_captcha', 1 );
		$tmpl['enable_captcha_users']		= $paramsC->get( 'enable_captcha_users', 0 );
		$tmpl['username_or_name'] 			= $paramsC->get( 'username_or_name', 0 );
		$tmpl['predefined_name'] 			= $paramsC->get( 'predefined_name', '' );
		$tmpl['disable_user_check'] 		= $paramsC->get( 'disable_user_check', 0 );
		$tmpl['enable_html_purifier'] 		= $paramsC->get( 'enable_html_purifier', 1 );
		$tmpl['enable_hidden_field'] 		= $paramsC->get( 'enable_hidden_field', 0 );
		$tmpl['forbidden_word_behaviour'] 	= $paramsC->get( 'forbidden_word_behaviour', 0 );
		
		
		//Get POST Data - - - - - - - - - 
		$post				= JRequest::get('post');
		
		// Hidden Field
		if ($tmpl['enable_hidden_field'] == 1) {
			$session 				=& JFactory::getSession();
			$session_suffix			= $paramsC->get('session_suffix');
			$hiddenSession			= 'pgbsesshf'.$session_suffix;
			$hiddenField 			= JRequest::getVar($session->get($hiddenSession.'name'), '', 'post', 'string');
			
			$session->clear($hiddenSession.'id');
			$session->clear($hiddenSession.'name');
			$session->clear($hiddenSession.'class');
			
			if ($hiddenField != '') {
			
				$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
				exit;
			}
		}
		$post2['content']	= JRequest::getVar( 'pgbcontent', '', 'post', 'string', JREQUEST_ALLOWRAW );
		
		$post2['captcha']	= JRequest::getVar( 'captcha', '', 'post', 'string' );
		$post2['title']		= JRequest::getVar( 'title', '', 'post', 'string' );
		$post2['pgusername']= JRequest::getVar( 'pgusername', '', 'post', 'string' );
		$post2['email']		= JRequest::getVar( 'email', '', 'post', 'string' );
		$post2['website']	= JRequest::getVar( 'website', '', 'post', 'string' );
		$post2['language']	= JRequest::getVar( 'language', '', 'post', 'string' );
		
		$post2['task']		= JRequest::getVar( 'task', '', 'post', 'string' );
		$post2['save']		= JRequest::getVar( 'save', '', 'post', 'string' );
		
		
		
		if (!isset($post2['captcha']) || (isset($post2['captcha']) && $post2['captcha'] == '')) {
			$post2['captcha'] = '';
		}
		

		// HTML Purifier - - - - - - - - - - 
		if ($tmpl['enable_html_purifier'] == 0) {
			$filterTags		= '';//preg_split( '#[,\s]+#', trim( ) ); // black list method is used
			$filterAttrs	= '';//preg_split( '#[,\s]+#', trim( ) ); // black list method is used
		
			$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1, 1 );
			$post2['content']	= $filter->clean( $post2['content'] );
		} else {		

			require_once( JPATH_COMPONENT.DS.'assets'.DS.'library'.DS.'HTMLPurifier.standalone.php' );
			$configP = HTMLPurifier_Config::createDefault();
			$configP->set('Core.Encoding', 'UTF-8');
			$configP->set('HTML.Doctype', 'XHTML 1.0 Transitional');
			$configP->set('HTML.TidyLevel', 'medium');
			$configP->set('HTML.Allowed','strong,em,p[style],span[style],img[src|width|height|alt|title],li,ul,ol,a[href],u,strike,br');
			$purifier = new HTMLPurifier($configP);
			$post2['content'] = $purifier->purify($post2['content']);
		}
		
		$cid				= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post2['catid'] 		= (int) $cid[0];
		
		$post2['published'] 	= (int) 1;
		if ($paramsC->get( 'review_item' ) != '') {
			$post2['published'] = (int)$paramsC->get( 'review_item' );
		}
		$post2['ip']			= $_SERVER["REMOTE_ADDR"];
		
		
		if (!isset($post2['pgusername'])) {
			$post2['username']	= '';
		} else {
			$post2['username']	= $post2['pgusername'];
		}
		
		if (!isset($post2['email'])) {
			$post2['email']	= '';
		}
		if (!isset($post2['website'])) {
			$post2['website']	= '';
		}
		
		
		if ($tmpl['forbidden_word_behaviour'] == 0) {
			$fwfa	= explode( ',', trim( $paramsC->get( 'forbidden_word_filter', '' ) ) );
			$fwwfa	= explode( ',', trim( $paramsC->get( 'forbidden_whole_word_filter', '' ) ) );
			
			$fW = 0;
			foreach ($fwfa as $key2 => $values2) {
				
				if (trim($values2) != '') {
					if (stripos($post2['username'], trim($values2)) !== false) 		{ $fW = 1;break;}
					if (stripos($post2['title'], trim($values2)) !== false) 		{ $fW = 1;break;}
					if (stripos($post2['content'], trim($values2)) !== false) 		{ $fW = 1;break;}
					if (stripos($post2['email'], trim($values2)) !== false) 		{ $fW = 1;break;}
					if (stripos($post2['website'], trim($values2)) !== false) 		{ $fW = 1;break;}
				}
				
			}
		
			//Forbidden Whole Word Filter
			$fWW = 0;
			$matches = '';
			foreach ($fwwfa as $key3 => $values3) {
				if ($values3 !='') {
					//$values3			= "/([\. ])".$values3."([\. ])/";
					$values3			= "/(^|[^a-zA-Z0-9_]){1}(".preg_quote(($values3),"/").")($|[^a-zA-Z0-9_]){1}/i";
					$a[]	= $values3;
					if (preg_match( $values3, $post2['username']) == 1) {$fWW = 1; break;};
					if (preg_match( $values3, $post2['title']) == 1) 	{$fWW = 1; break;};
					if (preg_match( $values3, $post2['content']) == 1) 	{$fWW = 1; break;};
					if (preg_match( $values3, $post2['email']) == 1) 	{$fWW = 1; break;};
					if (preg_match( $values3, $post2['website']) == 1) 	{$fWW = 1; break;};
				}
			}
			
			if ( $fW == 1 || $fWW == 1 ) { 
				$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
				exit;
			}
		
		
		}
		

		// Maximum of character, they will be saved in database
		$post2['content']	= substr($post2['content'], 0, $tmpl['max_char']);

		// Title Check
		if ($tmpl['display_title_form'] == 2) {
			if ( $post2['title'] && trim($post2['title']) !='' ) {
				$title = 1;// there is a value in title ... OK
			} else {
				$title = 0;
				JRequest::setVar( 'title-msg-1', 1, 'get',true );// there is no value in title ... FALSE
			}
		} else if ($tmpl['display_title_form'] == 0) {
			if ( $post2['title'] && trim($post2['title']) !='' ) { 
				$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
				exit;
			}
			$title = 1;
		} else {
			$title = 1;//there is a value or there is no value but it is not required, so it is OK
		}
		
		if ($title != 0 && preg_match("~[<|>]~",$post2['title'])) {
			$title = 0;
			JRequest::setVar( 'title-msg-2', 	1, 'get',true );
		}
		
		// Username or name check
		//$post2 is the same for both (name or username)
		//$tmpl['username'] is the same for both (name or username)
		if ($tmpl['username_or_name'] == 1) {
			if ($tmpl['display_name_form'] == 2) {
				if ( $post2['username'] && trim($post2['username']) !='' ) {
					$username = 1;
				} else {
					$username = 0;
					JRequest::setVar( 'username-msg-1', 	1, 'get',true );
				}
			} else if ($tmpl['display_name_form'] == 0) {
				if ( $post2['username'] && trim($post2['username']) !='' ) { 
					$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
					exit;
				}
				$username = 1;
			} else {
				$username = 1;
			}
			
			if ($username != 0 && preg_match("~[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+]~",$post2['title'])) {
				$username = 0;
				JRequest::setVar( 'username-msg-2', 	1, 'get',true );
			}
			
			if ($tmpl['disable_user_check'] == 0) {
				// Check for existing username
				$query = 'SELECT id'
				. ' FROM #__users '
				. ' WHERE username = ' . $db->Quote($post2['username'])
				. ' OR name = ' . $db->Quote($post2['username'])
				. ' AND id != '. (int) $user->id;
				$db->setQuery( $query );
				$xid = intval( $db->loadResult() );
				if ($xid && $xid != intval( $user->id )) {
					$username = 0;
					JRequest::setVar( 'username-msg-3', 	1, 'get',true );
				}
			}
		} else {
			if ($tmpl['display_name_form'] == 2) {
				if ( $post2['username'] && trim($post2['username']) !='' ) {
					$username = 1;
				} else {
					$username = 0;
					JRequest::setVar( 'username-msg-1', 	1, 'get',true );
				}
			} else if ($tmpl['display_name_form'] == 0) {
				if ( $post2['username'] && trim($post2['username']) !='' ) { 
					$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
					exit;
				}
				$username = 1;
			} else {
				$username = 1;
			}
			
			if ($username != 0 && preg_match("~[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+]~",$post2['title'])) {
				$username = 0;
				JRequest::setVar( 'username-msg-2', 	1, 'get',true );
			}
			
			if ($tmpl['disable_user_check'] == 0) {
				// Check for existing username
				$query = 'SELECT id'
				. ' FROM #__users '
				. ' WHERE username = ' . $db->Quote($post2['username'])
				. ' OR name = ' . $db->Quote($post2['username'])
				. ' AND id != '. (int) $user->id;
				$db->setQuery( $query );
				$xid = intval( $db->loadResult() );
				if ($xid && $xid != intval( $user->id )) {
					$username = 0; JRequest::setVar( 'username-msg-3', 	1, 'get',true );
				}
			}
		}
		
		// Email Check
		if ($tmpl['display_email_form'] == 2) {
			if ($post2['email'] && trim($post2['email']) !='' ) {
				$email = 1;
			} else {
				$email = 0;
				JRequest::setVar( 'email-msg-1', 	1, 'get',true );
			}
			
			if ($email != 0 && ! JMailHelper::isEmailAddress($post2['email']) ) {
				$email = 0;
				JRequest::setVar( 'email-msg-2', 1, 'get',true );
			}	
		} else if ($tmpl['display_email_form'] == 0) {
				if ( $post2['email'] && trim($post2['email']) !='' ) { 
					$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
					exit;
				}
				$email = 1;
		} else {
			$email = 1;
			
			if ($email != 0 && $post2['email'] != '' && ! JMailHelper::isEmailAddress($post2['email']) ) {
				$email = 0;
				JRequest::setVar( 'email-msg-2', 1, 'get',true );
			}
		}

		if ($tmpl['disable_user_check'] == 0) {
			// check for existing email
			$query = 'SELECT id'
				. ' FROM #__users '
				. ' WHERE email = '. $db->Quote($post2['email'])
				. ' AND id != '. (int) $user->id;
			$db->setQuery( $query );
			$xid = intval( $db->loadResult() );
			if ($xid && $xid != intval( $user->id )) {
				$email = 0; JRequest::setVar( 'email-msg-3', 1, 'get',true );
			}
		}
		// Website Check
		if ($tmpl['display_website_form'] == 2) {
			if ($post2['website'] && trim($post2['website']) !='' ) {
				$website = 1;
			} else {
				$website = 0; JRequest::setVar( 'website-msg-1', 	1, 'get',true );
			}
			
			if ($website != 0 && !PhocaguestbookHelperFront::isURLAddress($post2['website']) ) {
				$website = 0;
				JRequest::setVar( 'website-msg-2', 1, 'get',true );
			}
			
		} else if ($tmpl['display_website_form'] == 0) {
				if ( $post2['website'] && trim($post2['website']) !='' ) { 
					$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
					exit;
				}
				$website = 1;
		} else {
			$website = 1;
			if ($website != 0 && $post2['website'] != '' && !PhocaguestbookHelperFront::isURLAddress($post2['website']) ) {
				$website = 0;
				JRequest::setVar( 'website-msg-2', 1, 'get',true );
			}
		}
		
		// Content Check
		if ($tmpl['display_content_form'] == 2) {
			if ($post2['content'] && trim($post2['content']) !='' ) {
				$content = 1;
			} else {
				$content = 0; JRequest::setVar( 'content-msg-1', 	1, 'get',true );
			}
		} else if ($tmpl['display_content_form'] == 0) {
				if ( $post2['content'] && trim($post2['content']) !='' ) { 
					$app->redirect(JRoute::_($redSpam, false), JText::_("COM_PHOCAGUESTBOOK_POSSIBLE_SPAM_DETECTED"));
					exit;
				}
				$content = 1;
		} else {
			$content = 1;
		}
		
		// IP BAN Check
		$ip_ban			= trim( $paramsC->get( 'ip_ban' ) );
		$ip_ban_array	= explode( ',', $ip_ban );
		$tmpl['ipa'] 			= 1;//display
		if (is_array($ip_ban_array)) {
			foreach ($ip_ban_array as $valueIp) {
				//if ($post2['ip'] == trim($value)) {
				if ($valueIp != '') {
					if (strstr($post2['ip'], trim($valueIp)) && strpos($post2['ip'], trim($valueIp))==0) {
						$tmpl['ipa'] = 0;
						JRequest::setVar( 'ip-msg-1', 	1, 'get',true );
						break;
					}
				}
			}
		}
		
		// Not allowed URLs
		$tmpl['deny_url_words'] = $paramsC->get( 'deny_url_words', '' );
		if (!empty($tmpl['deny_url_words'])) {
			$tmpl['deny_url_words'] = explode(',',$paramsC->get( 'deny_url_words', '' ));
		}

		if (!empty($tmpl['deny_url_words']) && $content == 1) {
			$deny_url = 1;
			foreach ($tmpl['deny_url_words'] as $word) {
				if ($word != '') {
					if ((strpos($post2['content'], $word) !== false)
					   || (strpos($post2['title'], $word) !== false)
					   || (strpos($post2['username'], $word) !== false)) {
						$deny_url = 0;
						JRequest::setVar( 'denyurl-msg-1', 	1, 'get',true );
					}
				}
			}
		} else {
			$deny_url = 1;
		}
		
		
		// Registered user Check
		if ($tmpl['registered_users_only'] == 1) {
			if ( $user->id > 0 ) {
				$reguser = 1;
			} else {
				$reguser = 0; JRequest::setVar( 'reguser-msg-1', 	1, 'get',true );
			}
		} else {
			$reguser = 1;
		}
		
		// Captcha not for registered
		if ((int)$tmpl['enable_captcha_users'] == 1) {
			if ((int)$user->id > 0) {
				$tmpl['enable_captcha'] = 0;
			}
		}
		
		// Enable or disable Captcha
		if ($tmpl['enable_captcha'] < 1) {
			$phoca_guestbook_session 	= 1;
			$post2['captcha'] 			= 1;
		}
		
		/*
		if ($content != 0 && eregi( "[\<|\>]", $post2['content'])) {
			$content = 0; JRequest::setVar( 'content-msg-2', 	1, 'get',true );
		}*/
		
		// SAVING DATA - - - - - - - - - - 
		//the captcha picture code is the same as captcha input code, we can save the data
		//and other post data are OK
		
		//Recaptcha
		
		if ($phoca_guestbook_session == '') {
			
			// Maybe it is used a reCAPTCHA - we don't know but, because of security reason
			// no information about which method is used is sent through the form
			// So try to get reCAPTCHA
			require_once( JPATH_COMPONENT.DS.'helpers'.DS.'recaptchalib.php' );
			
			$resp = PhocaGuestbookHelperReCaptcha::recaptcha_check_answer ($paramsC->get('recaptcha_privatekey', ''),
                    $_SERVER["REMOTE_ADDR"],
					JRequest::getVar( 'recaptcha_challenge_field', '', 'post', 'string' ),
                    JRequest::getVar( 'recaptcha_response_field', '', 'post', 'string' ));
					
			if (!$resp->is_valid) {
				$phoca_guestbook_session 	= '';
				$post2['captcha'] 			= '';
			} else {
				$phoca_guestbook_session 	= 1;
				$post2['captcha'] 			= 1;
			}
		}
		
		
		if ($phoca_guestbook_session && $phoca_guestbook_session != '' &&
			isset($post2['captcha']) && $post2['captcha'] != '' && 
			$phoca_guestbook_session == $post2['captcha'] && 
			$title == 1 && 
			$username == 1 && 
			$email==1 && 
			$content == 1 &&
			$website == 1 &&
			$tmpl['ipa'] == 1 &&
			$deny_url == 1 &&
			$reguser == 1 && 
			isset($post2['task']) && 
			$post2['task'] == 'submit' &&
			isset($post2['save']) && 
			isset($post2['published'])) {
			
			$model = $this->getModel( 'guestbook' );
			
			$post2['homesite']	= $post2['website'];

			if ($model->store($post2)) {
				// Send mail to admin or super admin or user
				
				if ((int)$tmpl['send_mail'] > 0) {
					PhocaGuestbookControllerPhocaGuestbook::sendPhocaGuestbookMail((int)$tmpl['send_mail'], $post2, $uri->toString(), $tmpl);
				}
				
				if ($post2['published'] == 0) {
					$msg = JText::_( 'COM_PHOCAGUESTBOOK_SUCCESS_SAVE_ITEM' ). ". " .JText::_( 'COM_PHOCAGUESTBOOK_REVIEW_MESSAGE' );
				} else {
					$msg = JText::_( 'COM_PHOCAGUESTBOOK_SUCCESS_SAVE_ITEM' );
				}
			} else {
				$msg = JText::_( 'COM_PHOCAGUESTBOOK_ERROR_SAVE_ITEM' );
			}
			
			// Set Itemid id for redirect, exists this link in Menu?
		/*	$menu 	= &JSite::getMenu();
			$items	= $menu->getItems('link', 'index.php?option=com_phocaguestbook&view=phocaguestbook&id='.(int) $cid[0]);

			if(isset($items[0])) {
				$itemid = $items[0]->id;
				$alias 	= $items[0]->alias;
			}		*/	
			// No JRoute - there are some problems
			// $this->setRedirect(JRoute::_('index.php?option=com_phocaguestbook&view=phocaguestbook&id='. (int) $cid[0].'&Itemid='.$itemid),$msg );
			$this->setRedirect($uri->toString(),$msg );

		} else {// captcha image code is not the same as captcha input field (don't redirect because we need post data)
			if ($post2['captcha'] == '')							{JRequest::setVar( 'captcha-msg', 1, 'get',true );}
			if (!$post2['captcha'])								{JRequest::setVar( 'captcha-msg', 1, 'get',true );}
			if ($phoca_guestbook_session != $post2['captcha'])	{JRequest::setVar( 'captcha-msg', 1, 'get',true );}
			$this->display();
		}		
	}
	
	function remove() {
		$app	= JFactory::getApplication();
		$user 		= &JFactory::getUser();
		$cid 		= JRequest::getVar( 'mid', null, '', 'int' );
		$id 		= JRequest::getVar( 'id', null, '', 'int' );
		$itemid 	= JRequest::getVar( 'Itemid', null, '', 'int' );
		$limitstart = JRequest::getVar( 'limitstart', null, '', 'int' );
		$model 		= $this->getModel( 'guestbook' );
	
		$canAdmin	= PhocaguestbookHelperFront::canAdmin();
		if ($canAdmin) {

			if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'COM_PHOCAGUESTBOOK_WARNING_SELECT_ITEM_DELETE' ) );
			}
			if(!$model->delete($cid)) {
				echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
				$msg = JText::_( 'COM_PHOCAGUESTBOOK_ERROR_DELETE_ITEM' );
			} else {
				$msg = JText::_( 'COM_PHOCAGUESTBOOK_SUCCESS_DELETE_ITEM' );
			}
		} else {
			$msg = JText::_( 'COM_PHOCAGUESTBOOK_NOT_AUTHORIZED_DO_ACTION' );
		}
		// Limitstart (if we delete the last item from last pagination, this pagination will be lost, we must change limitstart)
		$countItem = $model->countItem($id);
		if ((int)$countItem[0] == $limitstart) {
			$limitstart = 0;
		}

		// Redirect
		$link	= 'index.php?option=com_phocaguestbook&view=guestbook&id='.$id.'&Itemid='.$itemid.'&limitstart='.$limitstart;
		$link	= JRoute::_($link, false);
		$this->setRedirect( $link, $msg );
	}
	
	function unpublish() {
		$app	= JFactory::getApplication();
		$user 		=& JFactory::getUser();
		$cid 		= JRequest::getVar( 'mid', null, '', 'int' );
		$id 		= JRequest::getVar( 'id', null, '', 'int' );
		$itemid 	= JRequest::getVar( 'Itemid', null, '', 'int' );
		$limitstart = JRequest::getVar( 'limitstart', null, '', 'int' );
		$model 		= $this->getModel( 'guestbook' );
		
		$canAdmin	= PhocaguestbookHelperFront::canAdmin();
		if ($canAdmin) {
			
			if (count( $cid ) < 1) {
				JError::raiseError(500, JText::_( 'COM_PHOCAGUESTBOOK_WARNING_SELECT_ITEM_UNPUBLISH' ) );
			}
			if(!$model->publish($cid, 0)) {
				echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
				$msg = JText::_( 'COM_PHOCAGUESTBOOK_ERROR_UNPUBLISH_ITEM' );
			}
			else {
				$msg = JText::_( 'COM_PHOCAGUESTBOOK_SUCCESS_UNPUBLISH_ITEM' );
			}
		} else {
			$msg = JText::_( 'COM_PHOCAGUESTBOOK_NOT_AUTHORIZED_DO_ACTION' );
		}
		
		// Limitstart (if we delete the last item from last pagination, this pagination will be lost, we must change limitstart)
		$countItem = $model->countItem($id);

		if ((int)$countItem[0] == $limitstart) {
			$limitstart = 0;
		}
		
		// Redirect
		$link	= 'index.php?option=com_phocaguestbook&view=guestbook&id='.$id.'&Itemid='.$itemid.'&limitstart='.$limitstart;
		$link	= JRoute::_($link, false);
		$this->setRedirect( $link, $msg );
	}
	
	
	function sendPhocaGuestbookMail ($id, $post2, $url, $tmpl) {
		$app	= JFactory::getApplication();
		$db 		= JFactory::getDBO();
		$sitename 	= $app->getCfg( 'sitename' );
		
		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
		' FROM #__users' .
		' WHERE id = '.(int)$id;
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		
		if (isset($post2['title']) && $post2['title'] != '') {
			$subject = $sitename .' ('.JText::_( 'COM_PHOCAGUESTBOOK_PG_NEW_POST' ). '): '.PhocaguestbookHelperFront::wordDelete($post2['title'], 25,'...');
			$title = $post2['title'];
		} else {
			$subject = $sitename ." (".JText::_( 'COM_PHOCAGUESTBOOK_PG_NEW_POST' ).')';
			$title = $post2['title'];
		}
		
		if (isset($post2['username']) && $post2['username'] != '') {
			$fromname = $post2['username'];
		} else {
			$fromname = $tmpl['predefined_name'];
		}
		
		if (isset($post2['email']) && $post2['email'] != '') {
			$mailfrom = $post2['email'];
		} else {
			$mailfrom = $rows[0]->email;
		}
		
		if (isset($post2['content']) && $post2['content'] != '') {
			$content = $post2['content'];
		} else {
			$content = "...";
		}
		
		$email = $rows[0]->email;
		
		$post2['content'] = str_replace("</p>", "\n", $post2['content'] );
		$post2['content'] = strip_tags($post2['content']);
		
		$message = JText::_( 'COM_PHOCAGUESTBOOK_PG_NEW_POST_ADDED' ) . "\n\n"
							. JText::_( 'COM_PHOCAGUESTBOOK_WEBSITE' ) . ': '. $sitename . "\n"
							. JText::_( 'COM_PHOCAGUESTBOOK_FROM' ) . ': '. $fromname . "\n"
							. JText::_( 'COM_PHOCAGUESTBOOK_DATE' ) . ': '. JHTML::_('date',  gmdate('Y-m-d H:i:s'), JText::_( 'DATE_FORMAT_LC2' )) . "\n\n"
							. JText::_( 'COM_PHOCAGUESTBOOK_SUBJECT' ) . ': '.$title."\n"
							. JText::_( 'COM_PHOCAGUESTBOOK_CONTENT' ) . ': '."\n"
							. "\n\n"
							.PhocaguestbookHelperFront::wordDelete($post2['content'],400,'...')."\n\n"
							. "\n\n"
							. JText::_( 'COM_PHOCAGUESTBOOK_CLICK_LINK' ) ."\n"
							. $url."\n\n"
							. JText::_( 'COM_PHOCAGUESTBOOK_REGARDS' ) .", \n"
							. $sitename ."\n";
					
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$message = html_entity_decode($message, ENT_QUOTES);
		
		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message);	
		return true;
	}
	
}
?>
