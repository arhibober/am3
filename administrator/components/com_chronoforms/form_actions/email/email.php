<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionEmail{
	function load($clear){
		if($clear){
			$action_params = array(
								'to' => '',
								'cc' => '',
								'bcc' => '',
								'subject' => '',
								'fromname' => '',
								'fromemail' => '',
								'replytoname' => '',
								'replytoemail' => '',
								'enabled' => 0,
								'action_label' => '',
								'recordip' => 1,
								'attachments' => '',
								'sendas' => 'html',
								'content1' => 'You may customize this message under the "Template" tab in the Email settings box.',
								'dto' => '',
								'dsubject' => '',
								'dfromname' => '',
								'dfromemail' => '',
								'dreplytoname' => '',
								'dreplytoemail' => '');
		}
		return array('action_params' => $action_params);
	}
	
	function run($form, $actiondata){
		$email_params = new JParameter($actiondata->params);
		$email_body = $actiondata->content1;
		ob_start();
		eval("?>".$email_body);
		$email_body = ob_get_clean();
		//build email template from defined fields and posted fields
		$email_body = $form->curly_replacer($email_body, $form->data);
		//add the IP if so
		if($email_params->get('recordip', 1)){
			if(strpos($email_body, '{IPADDRESS}') !== false){
				
			}else{
				$email_body .= "<br /><br />\n\nSubmitted by {IPADDRESS}";
			}
			$email_body = str_replace('{IPADDRESS}', $_SERVER['REMOTE_ADDR'], $email_body);
		}
		if($email_params->get('sendas', "html") == "html"){
			$email_body = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
			  <html>
				 <head>
					<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
					<base href=\"".JURI::base()."/\" />
					<title>Email</title>
				 </head>
				 
				 <body>$email_body</body>
			  </html>";
		}
		$fromname = (trim($email_params->get('fromname', ''))) ? trim($email_params->get('fromname', '')) : $form->data[trim($email_params->get('dfromname', ''))];
		$from = (trim($email_params->get('fromemail', ''))) ? trim($email_params->get('fromemail', '')) : $form->data[trim($email_params->get('dfromemail', ''))];
		$subject = (trim($email_params->get('subject', ''))) ? trim($email_params->get('subject', '')) : $form->data[trim($email_params->get('dsubject', ''))];
		// Recepients
		$recipients = array();
		if(trim($email_params->get('to', ''))){
			$recipients = explode(",", trim($email_params->get('to', '')));
		}
		if(trim($email_params->get('dto', ''))){
			$dynamic_recipients = explode(",", trim($email_params->get('dto', '')));
			foreach($dynamic_recipients as $dynamic_recipient){
				if($form->data[trim($dynamic_recipient)]){
					$recipients[] = $form->data[trim($dynamic_recipient)];
				}
			}
		}
		// CCs
		$ccemails = array();
		if(trim($email_params->get('cc', ''))){
			$ccemails = explode(",", trim($email_params->get('cc', '')));
		}
		if(trim($email_params->get('dcc', ''))){
			$dynamic_ccemails = explode(",", trim($email_params->get('dcc', '')));
			foreach($dynamic_ccemails as $dynamic_ccemail){
				if($form->data[trim($dynamic_ccemail)]){
					$ccemails[] = $form->data[trim($dynamic_ccemail)];
				}
			}
		}
		// BCCs
		$bccemails = array();
		if(trim($email_params->get('bcc', ''))){
			$bccemails = explode(",", trim($email_params->get('bcc', '')));
		}
		if(trim($email_params->get('dbcc', ''))){
			$dynamic_bccemails = explode(",", trim($email_params->get('dbcc', '')));
			foreach($dynamic_bccemails as $dynamic_bccemail){
				if($form->data[trim($dynamic_bccemail)]){
					$bccemails[] = $form->data[trim($dynamic_bccemail)];
				}
			}
		}
		// ReplyTo Names
		$replytonames = array();
		if(trim($email_params->get('replytoname', ''))){
			$replytonames = explode(",", trim($email_params->get('replytoname', '')));
		}
		if(trim($email_params->get('dreplytoname', ''))){
			$dynamic_replytonames = explode(",", trim($email_params->get('dreplytoname', '')));
			foreach($dynamic_replytonames as $dynamic_replytoname){
				if($form->data[trim($dynamic_replytoname)]){
					$replytonames[] = $form->data[trim($dynamic_replytoname)];
				}
			}
		}
		// ReplyTo Emails
		$replytoemails = array();
		if(trim($email_params->get('replytoemail', ''))){
			$replytoemails = explode(",", trim($email_params->get('replytoemail', '')));
		}
		if(trim($email_params->get('dreplytoemail', ''))){
			$dynamic_replytoemails = explode(",", trim($email_params->get('dreplytoemail', '')));
			foreach($dynamic_replytoemails as $dynamic_replytoemail){
				if($form->data[trim($dynamic_replytoemail)]){
					$replytoemails[] = $form->data[trim($dynamic_replytoemail)];
				}
			}
		}
		// Replies
		$replyto_email = $replytoemails;
		$replyto_name  = $replytonames;

		$mode = ($email_params->get('sendas', "html") == 'html') ? true : false;

		if(!$mode){
			$filter = JFilterInput::getInstance();
			$email_body = $filter->clean($email_body, 'STRING');
		}

		$email_attachments = array();
		if(strlen(trim($email_params->get("attachments", ""))) && !empty($form->files)){
			$attachments = explode(",", $email_params->get("attachments", ""));
			foreach($attachments as $attachment){
				if(isset($form->files[$attachment])){
					$email_attachments[] = $form->files[$attachment]['path'];
				}
			}
		}
		
		//dirty hack for now, need something better later
		//translate any email content
		foreach($form->form_actions as $form_action){
			if($form_action->type == 'multi_language'){
				$form->loadActionHelper('multi_language');
				$CfactionMultiLanguageHelper = new CfactionMultiLanguageHelper();
				$email_body = $CfactionMultiLanguageHelper->apply($form, $form_action, $email_body);
			}
		}
		//end translation
		
		$email_sent = JUtility::sendMail($from, $fromname, $recipients, $subject, $email_body, $mode, $ccemails, $bccemails, $email_attachments, $replyto_email, $replyto_name);
		
		if($email_sent){
			$form->debug['email'] = 'An email has been SENT successfully from ('.$fromname.')'.$from.' to '.implode(',', $recipients);
		}else{
			$form->debug['email'] = 'An email has failed to be sent from ('.$fromname.')'.$from.' to '.implode(',', $recipients);
		}
		$form->debug['email'] = $form->debug['email']."<br />Email template:<br /><br />".$email_body;
	}
}
?>