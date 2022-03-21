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
jimport( 'joomla.application.component.view');

class PhocaGuestbookViewGuestbook extends JView
{
	protected $tmpl;
	
	function display($tpl = null) {
		$app	= JFactory::getApplication();
		$pathway 	= $app->getPathway();
		$document	= JFactory::getDocument();
		$uri 		= JFactory::getURI();
		$user 		= JFactory::getUser();
		$params		= $app->getParams();
		$this->tmpl = array();
		
		
		JHTML::stylesheet( 'components/com_phocaguestbook/assets/phocaguestbook.css' );
		
		$this->tmpl['administrator'] = 0;
		$canAdmin	= PhocaguestbookHelperFront::canAdmin();
		if ($canAdmin) {
			$this->tmpl['administrator'] = 1;
		}
		
		//PARAMS
		$this->tmpl['captcha_method']			= $params->get( 'captcha_method', 1 );
		$this->tmpl['enable_editor']			= $params->get( 'enable_editor', 1 );
		$this->tmpl['table_width']				= $params->get( 'table_width', 400 );
		$this->tmpl['editor_width']				= $params->get( 'editor_width', 400 );
		$this->tmpl['editor_height']			= $params->get( 'editor_height', 200 );
		$this->tmpl['display_form']				= $params->get( 'display_form', 1 );
		$this->tmpl['date_format'] 				= $params->get( 'date_format','DATE_FORMAT_LC' );
		$this->tmpl['font_color'] 				= $params->get( 'font_color', '#000000' );
		$this->tmpl['second_font_color'] 		= $params->get( 'second_font_color', '#dddddd' );
		$this->tmpl['background_color'] 		= $params->get( 'background_color', '#C8DFF9' );
		$this->tmpl['border_color'] 			= $params->get( 'border_color','#E6E6E6' );
		$this->tmpl['display_name_form'] 		= $params->get( 'display_name_form', 2 );
		$this->tmpl['display_email_form']	 	= $params->get( 'display_email_form', 1 );
		$this->tmpl['display_title_form'] 		= $params->get( 'display_title_form', 2 );
		$this->tmpl['display_content_form'] 	= $params->get( 'display_content_form', 2 );
		$this->tmpl['display_website_form'] 	= $params->get( 'display_website_form', 0 );
		$this->tmpl['display_name'] 			= $params->get( 'display_name', 1 );
		$this->tmpl['display_email']			= $params->get( 'display_email', 1 );
		$this->tmpl['display_website']			= $params->get( 'display_website', 1 );
		$this->tmpl['username_or_name'] 		= $params->get( 'username_or_name', 0 );
		$this->tmpl['predefined_name'] 			= $params->get( 'predefined_name', '' );
		$this->tmpl['enable_html_purifier'] 	= $params->get( 'enable_html_purifier', 1 );
		$this->tmpl['display_path_editor']		= $params->get( 'display_path_editor', 1 );
		$this->tmpl['recaptcha_publickey']		= $params->get( 'recaptcha_publickey', '' );
		$this->tmpl['display_posts']			= $params->get( 'display_posts', 1 );
		$this->tmpl['enable_hidden_field'] 		= $params->get( 'enable_hidden_field', 0 );
		
		// - - - - - - - - - - -
		// Get data - all items
		$items		= $this->get('data');
		$guestbooks	= $this->get('guestbook');
		
		
		$this->tmpl['date_format']	= PhocaguestbookHelperFront::getDateFormat($this->tmpl['date_format']);
		$document->addCustomTag(PhocaguestbookHelperFront::setCaptchaReloadJS());
		if ($this->tmpl['enable_editor'] == 1) {
			$document->addCustomTag(PhocaguestbookHelperFront::setTinyMCEJS());
			$document->addCustomTag(PhocaguestbookHelperFront::displaySimpleTinyMCEJS($this->tmpl['display_path_editor']));
		}
		
		// - - - - - - - - - - -
		// Fill the form in case, you get data from post (e.g. user send data, but with no valid captcha
		// We send him back to the form but without lossing data
		
		$post				= JRequest::get( 'post' );
		$post['content']	= JRequest::getVar( 'pgbcontent', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$cid				= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$id					= JRequest::getVar( 'id', '', 'get', 'string' );
		$post['catid'] 		= (int) $cid[0];
		
		if(isset($guestbooks->report) && $guestbooks->report == 1) {
			
			//This is a report guestbook:
			$this->tmpl['display_posts'] = 0;
			if ((!isset($post['title'])) || (isset($post['title']) && $post['title'] == '')) {
				$reportTitle	= JRequest::getVar( 'reporttitle', '', 'get', 'string' );
				$post['title']	= htmlspecialchars(strip_tags($reportTitle));
			}
			
		}
		
		
		if ((int)$id < 1) {
			echo '<div id="phocaguestbook"><div class="error">'.JText::_('COM_PHOCAGUESTBOOK_WARNING_GUESTBOOK_NOT_SELECTED').'</div></div>';
			return true;
		}
		
		if (isset($post['pgusername'])) { // if not there is other code to solve it - see below
			$post['username']	= $post['pgusername'];
		}
		
		// HTML Purifier - - - - - - - - - - 
		if ($this->tmpl['enable_html_purifier'] == 0) {
			$filterTags		= '';//preg_split( '#[,\s]+#', trim( ) ); // black list method is used
			$filterAttrs	= '';//preg_split( '#[,\s]+#', trim( ) ); // black list method is used
		
			$filter	= new JFilterInput( $filterTags, $filterAttrs, 1, 1, 1 );
			$post['content']	= $filter->clean( $post['content'] );
		} else {		

			require_once( JPATH_COMPONENT.DS.'assets'.DS.'library'.DS.'HTMLPurifier.standalone.php' );
			$configP = HTMLPurifier_Config::createDefault();
			$configP->set('Core.Encoding', 'UTF-8');
			$configP->set('HTML.Doctype', 'XHTML 1.0 Transitional');
			$configP->set('HTML.TidyLevel', 'medium');
			$configP->set('HTML.Allowed','strong,em,p[style],span[style],img[src|width|height|alt|title],li,ul,ol,a[href],u,strike,br');
			$purifier = new HTMLPurifier($configP);
			$post['content'] = $purifier->purify($post['content']);
		}
		
		// - - - - - - - - - -
		// Add username and user e-mail if user is login
		if ($this->tmpl['username_or_name'] == 1) {
			if ($user->name && trim($user->name !='')) {
				$form_username = $user->name;
			} else {
				$form_username = $this->tmpl['predefined_name'];
			}
		} else {
			if ($user->username && trim($user->username !='')) {
				$form_username = $user->username;
			} else {
				$form_username = $this->tmpl['predefined_name'];
			}
		}
		
		if ($user->email && trim($user->email !='')) {
			$form_email = $user->email;
		} else {
			$form_email = '';
		}
		
		// - - - - - - - - - - -
		// !!!! Add content to the fields
		
		// - - - - - - - - - - -
		//Create new object, if user fill not all data, no redirection and he gets the data he added (he doesn't loss it)
		$formdata = new JObject();
		if (isset($post['content']))	{$formdata->set('content', $post['content']);}
		else							{$formdata->set('content', '');}
		if (isset($post['username']))	{$formdata->set('username', $post['username']);}
		else							{$formdata->set('username', $form_username);}
		if (isset($post['email']))		{$formdata->set('email', $post['email']);}
		else							{$formdata->set('email', $form_email);}
		if (isset($post['title']))		{$formdata->set('title', $post['title']);}
		else							{$formdata->set('title', '');}
		if (isset($post['website']))	{$formdata->set('website', $post['website']);}
		else							{
			
			if ($this->tmpl['display_website_form'] == 2) {
				$formdata->set('website', 'http://');//required
			} else {
				$formdata->set('website', '');// not required
			}
		}
		
		if ($this->tmpl['enable_editor'] == 1) {
			$this->tmpl['editor'] = PhocaguestbookHelperFront::displayTextArea('pgbcontent',  $formdata->content , (int)$this->tmpl['editor_width'].'px', (int)$this->tmpl['editor_height'].'px', '60', '80', false );
		} else {
			$this->tmpl['editor'] = '<textarea id="pgbcontent" name="pgbcontent" cols="45" rows="10" style="width: '.(int)$this->tmpl['editor_width'].'px; height:'.(int)$this->tmpl['editor_height'].'px;" class="pgbinput" >'.$formdata->content.'</textarea>';
		
		}
		
		
		
		$pagination	= &$this->get('pagination');
		$this->tmpl['fwfa']	= explode( ',', trim( $params->get( 'forbidden_word_filter', '' ) ) );
		$this->tmpl['fwwfa']	= explode( ',', trim( $params->get( 'forbidden_whole_word_filter', '' ) ) );
		


		
		
		
		
		/*$this->tmpl['formemail'] = 1;
		if ($params->get( 'display_email_form' ) != '')	{$this->tmpl['formemail'] = $params->get( 'display_email_form' );}
		
		//Add requirement V A L U E S
		$this->tmpl['title'] = 1;
		if ($params->get( 'require_title' ) != '')		{$this->tmpl['title'] = $params->get( 'require_title' );}
		
		/*$this->tmpl['username'] = 1;
		if ($params->get( 'require_username' ) != '')	{$this->tmpl['username'] = $params->get( 'require_username' );}
		*/
		/*$this->tmpl['email'] = 0;
		if ($params->get( 'require_email' ) != '')			{$this->tmpl['email'] = $params->get( 'require_email' );}

		// if we disable email form field and name form field we cannot require these items
		/*if ($this->tmpl['display_name_form'] == 0) 					{$this->tmpl['username'] = 0;}
		if ($this->tmpl['formemail'] == 0) 					{$this->tmpl['email'] = 0;}*/
		/*
		$this->tmpl['content'] = 1;
		if ($params->get( 'require_content' ) != '')		{$this->tmpl['content'] = $params->get( 'require_content' );}
		*/
		$this->tmpl['registered_users_only'] = $params->get( 'registered_users_only', 0 );
		$this->tmpl['ft']						= PhocaguestbookHelperFront::getInfo();
		$this->tmpl['form_position'] 			= $params->get( 'form_position', 0 );
		$this->tmpl['max_url'] 					= $params->get( 'max_url', 5);
		$this->tmpl['enable_captcha']	 		= $params->get( 'enable_captcha', 1 );
		$this->tmpl['enable_captcha_users'] 	= $params->get( 'enable_captcha_users', 0 );
		$this->tmpl['captcha_id']				= PhocaguestbookHelperFront::getCaptchaId($this->tmpl['enable_captcha']);
		
		
		// Captcha not for registered
		if ((int)$this->tmpl['enable_captcha_users'] == 1) {
			if ((int)$user->id > 0) {
				$this->tmpl['enable_captcha'] = 0;
			}
		}
		
		//-----------------------------------------------------------------------------------------------
		// !!!! 1. Server Side Checking controll
		//-----------------------------------------------------------------------------------------------
		//Form Variables --------------------------------------------------------------------------------
		//captcha is wrong,we cannot redirect the page,we display message this way
		//DISPLAY MESSAGES WHICH YOU GET FROM CONTROLL FILE - (CONTROLLERS - phocaguestbook.php)

		$smB 				= '<small style="color:#fc0000;">';
		$smE				= '</small><br />';
		$this->tmpl['errmsg_captcha'] 	= '';
		$this->tmpl['errmsg_top'] 		= '';
		if (JRequest::getVar( 'captcha-msg', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_captcha'] .= '<tr><td>&nbsp;</td><td colspan="3">'.$smB.JText::_('COM_PHOCAGUESTBOOK_WRONG_CAPTCHA' ).'</small></td></tr>';
		}

		if (JRequest::getVar( 'title-msg-1', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_NO_TITLE' ).$smE;
		}
		if (JRequest::getVar( 'title-msg-2', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_BAD_TITLE' ). $smE;
		}
		if (JRequest::getVar( 'username-msg-1', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_NO_USERNAME' ). $smE;
		}
		if (JRequest::getVar( 'username-msg-2', 0, 'get', 'int' ) == 1){
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_BAD_USERNAME' ). $smE;
		}
		if (JRequest::getVar( 'username-msg-3', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_USERNAME_EXISTS' ). $smE;
		}
		if (JRequest::getVar( 'email-msg-1', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_NO_EMAIL' ). $smE;
		}
		if (JRequest::getVar( 'email-msg-2', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_BAD_EMAIL' ). $smE;
		}
		if (JRequest::getVar( 'email-msg-3', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_EMAIL_EXISTS' ). $smE;
		}
		if (JRequest::getVar( 'website-msg-1', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_NO_WEBSITE' ). $smE;
		}
		if (JRequest::getVar( 'website-msg-2', 0, 'get', 'int' ) == 1) {
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_BAD_WEBSITE' ). $smE;
		}
		if (JRequest::getVar( 'content-msg-1', 0, 'get', 'int' ) == 1) {	
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_NO_CONTENT' ). $smE;
		}
		if (JRequest::getVar( 'content-msg-2', 0, 'get', 'int' ) == 1) {	
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_BAD_CONTENT' ). $smE;
		}
		if (JRequest::getVar( 'ip-msg-1', 0, 'get', 'int' ) == 1) {	
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_IP_BAN_NO_ACCESS' ). $smE;
		}
		if (JRequest::getVar( 'reguser-msg-1', 0, 'get', 'int' ) == 1) {	
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_REG_USER_ONLY_NO_ACCESS' ). $smE;
		}
		if (JRequest::getVar( 'denyurl-msg-1', 0, 'get', 'int' ) == 1) {	
			$this->tmpl['errmsg_top'] .= $smB . JText::_('COM_PHOCAGUESTBOOK_DENY_URL' ). $smE;
		}
		
	
		
		//Form Variables --------------------------------------------------------------------------------
		
		//-----------------------------------------------------------------------------------------------
		// !!!! 2. Before Server Side Checking controll, don't show form (but there is a server side
		//         checking, it means, if the user hack the form which is not displayed to him
		//         there is a server checking controll too.
		//-----------------------------------------------------------------------------------------------
		//Don't show form, is IP Ban is wrong
	/*	$ip_ban			= trim( $params->get( 'ip_ban', '' ) );
		$ip_ban_array	= explode( ',', $ip_ban );
		
		$i = '192.68.25.23';
		$this->tmpl['ipa'] 	= 1;//display
		if (is_array($ip_ban_array)) {
			foreach ($ip_ban_array as $value) {
				
				if ($i == trim($value)) {
					$this->tmpl['ipa'] = 0;
					echo "ano";
					break;// found
				}
			}
		}*/
		$post['ip']		= $_SERVER["REMOTE_ADDR"];
		
		$ip_ban			= trim( $params->get( 'ip_ban' ) );
		$ip_ban_array	= explode( ',', $ip_ban );
		$this->tmpl['ipa'] 			= 1;//display
		
		if (is_array($ip_ban_array)) {
			foreach ($ip_ban_array as $valueIp) {
				//if ($post['ip'] == trim($value)) {
				if ($valueIp != '') {
					if (strstr($post['ip'], trim($valueIp)) && strpos($post['ip'], trim($valueIp))==0) {
						$this->tmpl['ipa'] = 0;
						JRequest::setVar( 'ip-msg-1', 	1, 'get',true );
						break;
					}
				}
			}
		}
		
		// Display or not to display the form
		// If user is registered - return 1, if not return 0, if not but the form can be displayed for not registered, return 1
		$this->tmpl['registered_users_only']	= PhocaguestbookHelperFront::isRegisteredUser($this->tmpl['registered_users_only'],$user->id );
		$this->tmpl['show_form']				= 1;
		
		if ($this->tmpl['ipa'] == 0) {
			$this->tmpl['show_form']	= 0;
			$this->tmpl['ipa_msg'] 	= '<p>' . JText::_('COM_PHOCAGUESTBOOK_IP_BAN_NO_ACCESS') . '</p>';
		} else {
			$this->tmpl['ipa_msg'] 	= '';
		} 
		
		if ($this->tmpl['registered_users_only'] == 0){
			$this->tmpl['show_form']	= 0;
			$this->tmpl['reguser_msg']= '<p>' . JText::_('COM_PHOCAGUESTBOOK_REG_USER_ONLY_NO_ACCESS'). '</p>';
		} else {
			$this->tmpl['reguser_msg']='';
		} 
		
		// Recaptcha
		if ((int)$this->tmpl['captcha_id'] == 4) {
			require_once( JPATH_COMPONENT.DS.'helpers'.DS.'recaptchalib.php' );
			
			// Session of standard captcha is not used
			$session 	=& JFactory::getSession();
			$tmpl['session_suffix']		= $params->get('session_suffix');
			$session->set('pgbsess'.$tmpl['session_suffix'], '');
		}
		
		// Display or hide form
		if ($this->tmpl['show_form'] == 1) {
			if ($this->tmpl['display_form'] == 0 ) {
				JHTML::_('behavior.framework', true);
				// if user posted a message and get some error warning (captcha, ...) the form should be open	
				if ($this->tmpl['errmsg_captcha'] == '' && $this->tmpl['errmsg_top'] == '') {
					$hide 	= '.hide()';
					$open	= 0;
				} else {
					$hide 	= '';
					$open	= 1;
				}
		
				$document->addScriptDeclaration(
				 ' window.addEvent(\'domready\', function() {'."\n"
				.'  var pgVSlide = new Fx.Slide(\'pg-guestbook\')'.$hide.';'."\n"
				.'  var status	= '.$open."\n"
				.'  $(\'pg-open-guestbook\').addEvent(\'click\', function(e){'."\n"
				.'   e.stop();'."\n"
				.'    if (status == 0) {'."\n"
				.'	   pgVSlide.slideIn();'."\n"
				.'	   status = 1;'."\n"
				.'    } else {'."\n"
				.'	   pgVSlide.slideOut();'."\n"
				.'	   status = 0;'."\n"
				.'    }'."\n"
				.'  });'."\n"
				.' });'."\n");

			}
		}
		
		// Hidden Field
		$this->tmpl['hidden_field_output'][1] = $this->tmpl['hidden_field_output'][2] = $this->tmpl['hidden_field_output'][3] = $this->tmpl['hidden_field_output'][4] = $this->tmpl['hidden_field_output'][5] = '';
		if ($this->tmpl['enable_hidden_field'] 	== 1) {
			$session 				=& JFactory::getSession();
			$session_suffix			= $params->get('session_suffix');
			$hiddenSession			= 'pgbsesshf'.$session_suffix;
			$fieldPos				= PhocaguestbookHelperFront::setHiddenFieldPos($this->tmpl['display_title_form'], $this->tmpl['display_name_form'], $this->tmpl['display_email_form'], $this->tmpl['display_website_form'], $this->tmpl['display_content_form']);

			$session->set($hiddenSession.'name', PhocaguestbookHelperFront::getRandomString(mt_rand(6,10)));
			$session->set($hiddenSession.'id', 'pgb'.$session->get($hiddenSession.'name'));
			$session->set($hiddenSession.'class', 'pgb'.PhocaguestbookHelperFront::getRandomString(mt_rand(6,10)));

			$this->tmpl['hidden_field_output'][$fieldPos] = '<input type="text" name="'.$session->get($hiddenSession.'name').'" size="32" maxlength="200" id="'.$session->get($hiddenSession.'id').'" class="pgbinput '.$session->get($hiddenSession.'class').'" />';
			$document->addCustomTag('<style type="text/css"> .'.$session->get($hiddenSession.'class').' { '."\n\t".'display: none;'."\n".
			'}</style>');
		}
		// End hidden field
		
		//$this->assignRef( 'tmpl' ,			$this->tmpl);
		$this->assignRef( 'id' ,			$id);		
		$this->assignRef( 'formdata' ,		$formdata);//captcha is wrong, add the same values via POST into form as they were
		$this->assignRef( 'items' ,			$items);
		$this->assignRef( 'guestbooks', 	$guestbooks);
		$this->assignRef( 'params' ,		$params);
		$this->assignRef( 'pagination', 	$pagination);
		$this->assign('action',	$uri->toString());
		//$this->_prepareDocument();
		parent::display($tpl);
	}
	
	protected function _prepareDocument() {
		
	}
}
?>
