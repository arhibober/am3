<?php
/*
This file is part of "Fox Contact Form", a free Joomla! 1.6 Contact Form
You can redistribute it and/or modify it under the terms of the GNU General Public License
GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
Author: Demis Palma
Documentation at http://www.fox.ra.it/joomla-extensions/fox-contact-form.html
Copyright: 2011 Demis Palma
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

require_once(JPATH_COMPONENT . DS . "helpers" . DS . "fieldsbuilder.php");
require_once(JPATH_COMPONENT . DS . "helpers" . DS . "vfdebugger.php");

class FoxContactViewFoxContact extends JView
	{
	protected $application;
	protected $foxparams;
	protected $FieldsBuilder;
	protected $Debugger;

	// Overwriting JView display method
	function display($tpl = null) 
		{
		$this->application = &JFactory::getApplication();
		$this->foxparams = $this->application->getParams('com_foxcontact');
		// If params from another component is needed
		//$otherparams = JComponentHelper::getParams('com_media');

		$this->FieldsBuilder = new FieldsBuilder($this->foxparams, $this->foxparams, false);
		$this->Debugger = new VFDebugger($this->FieldsBuilder);

		if ($this->foxparams->get('menu-meta_description'))
			$this->document->setDescription($this->foxparams->get('menu-meta_description'));

		if ($this->foxparams->get('menu-meta_keywords'))
			$this->document->setMetadata('keywords', $this->foxparams->get('menu-meta_keywords'));

		$page_subheading = $this->foxparams->get("page_subheading", "");
		if (!empty($page_subheading)) $this->msg .= "<h2>" . $page_subheading . "</h2>";

		if (count($_POST))
			{
			if ($this->FieldsBuilder->IsValid())
				{
				if ($this->SendMail())
					{
					$this->BuildIntroText("email_sent_text");
					}
				else
					{
					$this->BuildIntroText("spam_detected_text");
					}
				}
			else
				{
				$this->BuildIntroText("missing_fields_text");
				$this->BuildForm();
				$this->BuildIntroText("bottom_text");
				}
			}
		else
			{
			$this->BuildIntroText("top_text");
			$this->BuildForm();
			$this->BuildIntroText("bottom_text");
			}

		if ($this->application->getCfg("debug"))
			{
			$this->msg .= $this->Debugger->Dump();
			$this->msg .= $this->FieldsBuilder->Dump();
			}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
			{
			JError::raiseError(666, implode('<br />', $errors));
			return false;
			}
		// Display the view
		parent::display($tpl);
		}


	function BuildForm()
		{
		$this->msg .= "<form id=\"emailForm\" class=\"foxform\" name=\"emailForm\" method=\"post\" action=\"\" >\n";
		
		for ($n = 0; $n < 2; ++$n) $this->BuildTextField("sender" . $n);  // Default Fields		
		for ($n = 0; $n < 10; ++$n) $this->BuildTextField("text" . $n);  // Text fields		
		for ($n = 0; $n < 3; ++$n) $this->BuildDropdownField("dropdown" . $n);  // Dropdown fields		
		for ($n = 0; $n < 3; ++$n) $this->BuildTextareaField("textarea" . $n);  // Text area fields
		for ($n = 0; $n < 5; ++$n) $this->BuildCheckboxField("checkbox" . $n);  // Check boxes
		// Submit button
		$this->msg .= "<div>";
		$this->msg .= "<button class=\"foxbutton\" type=\"submit\">" . JText::_('JSUBMIT') . "</button>";
		$this->msg .= "</div>";

		$this->msg .= "</form>";
		}


	// Build text for the top or the bottom of this page.
	// Parameters can be 'top_text', 'missing_fields_text', 'bottom_text', 'email_sent_text', 'spam_detected_text'
	function BuildIntroText($index)
		{
		if (empty($this->FieldsBuilder->Fields[$index]['Name'])) return;
		$this->msg .= '<div class="foxmessage" style="clear:both;">';
		$this->msg .= $this->FieldsBuilder->Fields[$index]['Name'];
		$this->msg .= "</div>";
		}


	// Build a single Text field
	function BuildTextField($index)
		{
		if (!isset($this->FieldsBuilder->Fields[$index]) || !$this->FieldsBuilder->Fields[$index]['Display']) return;

		$this->msg .= '<div style="clear:both;">';
		$this->msg .= "<label>" .
			$this->FieldsBuilder->Fields[$index]['Name'] .
			$this->MandatoryDescription($this->FieldsBuilder->Fields[$index]['Display']) .
			"</label>";
		$this->msg .= "<input class=\"" .
			$this->TextStyleByValidation($index) .
			"\" type=\"text\" value=\"" .
			$this->FieldsBuilder->Fields[$index]['Value'] . '" ' . // Example: $_POST[$_fieldname] = 555-12345 
			//'size="' . $this->FieldsBuilder->Fields[$index]['Width'] . '" '
			'style="' .
				'width:' . $this->FieldsBuilder->Fields[$index]['Width'] . $this->FieldsBuilder->Fields[$index]['Unit'] . ' !important;' .
				'" ' .
			'name="' . $this->FieldsBuilder->Fields[$index]['PostName'] . '"' .
			">" .                            
			$this->DescriptionByValidation($index);  // Example: *
		$this->msg .= "</div>\n";
		}


	// Build a single Dropdown box field
	function BuildDropdownField($index)
		{
		if (!isset($this->FieldsBuilder->Fields[$index]) || !$this->FieldsBuilder->Fields[$index]['Display']) return;

		$this->msg .= '<div style="clear:both;">';
		$this->msg .= "<label>" . $this->FieldsBuilder->Fields[$index]['Name'] . "</label>";
		$this->msg .= "<select class=\"" .
			$this->TextStyleByValidation($index) .
			"\" name=\"" . $this->FieldsBuilder->Fields[$index]['PostName'] . "\">";
		// Insert an empty option
		$this->msg .= "<option value=\"\"></option>";
		// and the actual options
		$options = explode(",", $this->FieldsBuilder->Fields[$index]['Values']);
		for ($o = 0; $o < count($options); ++$o)
			{
			$this->msg .= "<option value=\"" . $options[$o] . "\"";
			if ($this->FieldsBuilder->Fields[$index]['Value'] == $options[$o]) $this->msg .= " selected ";
			$this->msg .= ">" . $options[$o] . "</option>";
			}
		$this->msg .= "</select>" . $this->DescriptionByValidation($index);
		$this->msg .= "</div>\n";
		}


	// Build a single Check Box field
	function BuildCheckboxField($index)
		{
		if (!isset($this->FieldsBuilder->Fields[$index]) || !$this->FieldsBuilder->Fields[$index]['Display']) return;

		$this->msg .= '<div style="clear:both;">';
		$this->msg .= "<div class=\"" .
			$this->CheckboxStyleByValidation($index) .
			"\" style=\"float:left;margin-right:10px;padding:0;\"><input type=\"checkbox\" " .
			"value=\"" . JText::_('JYES') . "\" ";
			// Here, validation will be successfull, because there aren't post data, but it isn't a good right to activate che checkbox with the check
			// if (intval($this->FieldsBuilder->Fields[$index]['Value'])) $this->msg .= "checked=\"\"";
			if ($this->FieldsBuilder->Fields[$index]['Value'] == JText::_('JYES')) $this->msg .= "checked=\"\"";
			$this->msg .= "name=\"" . 
			$this->FieldsBuilder->Fields[$index]['PostName'] . "\"></div>" . $this->FieldsBuilder->Fields[$index]['Name'];
			$this->DescriptionByValidation($index);
		$this->msg .= "</div>\n";
		}


	// Build a Textarea field
	function BuildTextareaField($index)
		{		
		if (!isset($this->FieldsBuilder->Fields[$index]) || !$this->FieldsBuilder->Fields[$index]['Display']) return;

		$this->msg .= '<div style="clear:both;">';
		$this->msg .= "<label>" . $this->FieldsBuilder->Fields[$index]['Name'] . $this->MandatoryDescription($this->FieldsBuilder->Fields[$index]['Display']) . "</label>";
		$this->msg .= "<textarea " .
			'class="' . $this->TextStyleByValidation($index) . '" ' .
//			'cols="' . $this->FieldsBuilder->Fields[$index]['Width'] . '" ' .
//			'rows="' . $this->FieldsBuilder->Fields[$index]['Height'] . '" ' .
			'name="' . $this->FieldsBuilder->Fields[$index]['PostName'] . '" ' .
			'style="' .
				"width:" . $this->FieldsBuilder->Fields[$index]['Width'] . $this->FieldsBuilder->Fields[$index]['Unit'] . ' !important;' .
				"height:" . $this->FieldsBuilder->Fields[$index]['Height'] . 'px' . ' !important;' .  // Height in % doesn't always work
				'" ' .
			">" .
			$this->FieldsBuilder->Fields[$index]['Value'] .  // Inner Text
			"</textarea>" .
			$this->DescriptionByValidation($index);
		$this->msg .= "</div>\n";
		}


	function MandatoryDescription($fieldtype)
		{
		return ($fieldtype == 2) ? (" <b>*</b>") : "";
		}


	// Check a single field and return a string good for html output
	function DescriptionByValidation($index)
		{
		return $this->FieldsBuilder->Fields[$index]['IsValid'] ? "" : (" <span class=\"asterisk\">*</span>");
		}


	// Check a single field and return a string good for html output
	function TextStyleByValidation($index)
		{
		// No post data = first time here. return a grey border
		if (!count($_POST)) return "foxtext";
		// Return a green or red border
		return $this->FieldsBuilder->Fields[$index]['IsValid'] ? "validfoxtext" : "invalidfoxtext";
		}


	// Check a single field and return a string good for html output
	function CheckboxStyleByValidation($index)
		{
		if (!count($_POST)) return "foxcheckbox";
		// Return a green or red border
		return $this->FieldsBuilder->Fields[$index]['IsValid'] ? "validcheckbox" : "invalidcheckbox";
		}


	function SendMail()
		{
		// Todo: Is it safe against attacks to recipient Mail User Agent, or is it better to filter all fields?

		// To get the html code not parsed, use JREQUEST_ALLOWHTML or JREQUEST_ALLOWRAW
		// JRequest::getVar($varname, NULL, 'default', 'none', JREQUEST_ALLOWHTML);
//		$fromname = JRequest::getVar("sender0", NULL, 'POST');
//		$from = JRequest::getVar("sender1", NULL, 'POST');
		$fromname = $this->FieldsBuilder->Fields['sender0']['Value'];
		$from = $this->FieldsBuilder->Fields['sender1']['Value'];

		$mail =& JFactory::getMailer();
		$mail->setSender(array($from, $fromname));

		$recipients = explode(",", $this->foxparams->get("to_address", ""));
		foreach ($recipients as $recipient)
			{
			// Avoid to call $mail->add..() with an empty string, since explode(",", $string) returns al least 1 item, even if $string is empty
			if (empty($recipient)) continue;
			$mail->addRecipient($recipient);
			}

		$cc_addresses = explode(",", $this->foxparams->get("cc_address", ""));
		foreach ($cc_addresses as $cc)
			{
			// Avoid to call $mail->add..() with an empty string, since explode(",", $string) returns al least 1 item, even if $string is empty
			if (empty($cc)) continue;
			$mail->addCC($cc);
			}

		$bcc_addresses = explode(",", $this->foxparams->get("bcc_address", ""));
		foreach ($bcc_addresses as $bcc)
			{
			// Avoid to call $mail->add..() with an empty string, since explode(",", $string) returns al least 1 item, even if $string is empty
			if (empty($bcc)) continue;
			$mail->addBCC($bcc);
			}

		$mail->setSubject(JMailHelper::cleanSubject($this->foxparams->get("email_subject", "")));

		// Body
		$body = "";
		// Special fields
		for ($l = 0; $l < 2; ++$l) $body .= $this->AddToBody("sender" . $l);
		for ($l = 0; $l < 3; ++$l) $body .= $this->AddToBody("textarea" . $l);
		for ($l = 0; $l < 10; ++$l) $body .= $this->AddToBody("text" . $l);
		for ($l = 0; $l < 3; ++$l) $body .= $this->AddToBody("dropdown" . $l);
		for ($l = 0; $l < 5; ++$l) $body .= $this->AddToBody("checkbox" . $l);

		// If it was a spammer, just log this attempt, drop the email, and of corse notify the user with a false return value
		$spam_words = $this->foxparams->get("spam_words", "");
		if (intval($this->foxparams->get("spam_check", "")) && !empty($spam_words))
			{
			$arr_spam_words = explode(",", $spam_words);
			foreach($arr_spam_words as $word)
				{
				if (stripos($body, $word) !== false)
					{
					$this->log("Possible spam attempt was brutally blocked: " . $body);
					return false;
					}
				}
			// Spam ckeck successfull
			}
		// Spam check disabled

			// a blank line
		$body .= "\r\n";

			// Info about url
		$body .= $this->application->getCfg("sitename") . " - " . $this->CurrentURL() . "\r\n";

			// Info about client
		// Todo: Danger! Possible attack to recipient Mail User Agent, by submitting a fake and malformed $_SERVER['HTTP_USER_AGENT']
		$body .= "Client: " . $this->ClientIPaddress() . " - " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";

		jimport('joomla.mail.helper');
		$body = JMailHelper::cleanBody($body);
		$mail->setBody($body);

		if (($result = $mail->Send()) !== true) $this->setError(JText::_($mail->ErrorInfo));
		$this->log($body . "\r\n");
		// Todo: here the return value may be confused with a spam attempt
		return $result;
		}


	function AddToBody($index)
		{
		if (!isset($this->FieldsBuilder->Fields[$index]) || !$this->FieldsBuilder->Fields[$index]['Display']) return "";

		// How the admin labelled this field
		$fieldname = $this->FieldsBuilder->Fields[$index]['Name'];
		// How the user filled this field
		$fieldvalue = $this->FieldsBuilder->Fields[$index]['Value'];
		return $fieldname . ": " . $fieldvalue . "\r\n";		
		}


	function CurrentURL()
		{
		$url = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $url .= "s";
		$url .= "://";
		$url .= $_SERVER["SERVER_NAME"];
		if ($_SERVER["SERVER_PORT"] != "80") $url .= ":" . $_SERVER["SERVER_PORT"];
		$url .= $_SERVER["REQUEST_URI"];
		return $url;
		}


	function ClientIPaddress()
		{
		if (isset($_SERVER["REMOTE_ADDR"])) return $_SERVER["REMOTE_ADDR"];
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) return $_SERVER["HTTP_X_FORWARDED_FOR"];
		if (isset($_SERVER["HTTP_CLIENT_IP"])) return $_SERVER["HTTP_CLIENT_IP"];
		return "?";
		} 


	function log($buffer)
		{
		$handle = @fopen($this->application->getCfg("log_path") . DS . "foxcontact-" . md5($this->application->getCfg("secret")) . ".log", 'a+');
		if (!$handle) return;
		fwrite($handle, date("d/m/y H:i:s") . " " . $buffer . "\n");
		fclose($handle);
		}

	}
?>
