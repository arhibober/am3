<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

 /**
 * Easybook Reloaded Component Controller
 *
 * @package    Easybook Reloaded
 */
class EasybookReloadedControllerEntry extends JController
{
	var $_access = null;
	 /**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}

	function _add_edit()
	{
		$params = JComponentHelper::getParams('com_easybookreloaded');

		if ((_EASYBOOK_CANADD OR _EASYBOOK_CANEDIT) AND (!$params->get('offline')))
		{
			JRequest::setVar( 'view', 'entry' );
			JRequest::setVar( 'layout', 'form' );
			parent::display();
		}
		else
		{
			$msg = JText::_('ERROR_RIGHTS');
		    $type = 'message';
			$this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded', false ), $msg, $type);
		}
	}

	function add()
	{
		$this->_add_edit();
	}

	function edit()
	{
		$this->_add_edit();
	}

	 /**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$mainframe = JFactory::getApplication();
 		$uri  =  JFactory::getURI();
		$mail = JFactory::getMailer();
		$db   = JFactory::getDBO();
		$params = JComponentHelper::getParams( 'com_easybookreloaded' );
		$session = JFactory::getSession();
		jimport('joomla.utilities.simplecrypt');
		require_once(JPATH_SITE.DS.'components'.DS.'com_easybookreloaded'.DS.'helpers'.DS.'route.php');

		//get mail addresses of authorized users
		$query = 'SELECT email' .
				' FROM #__users' .
				' WHERE sendEmail = 1';
		$db->setQuery($query);
		$admins = $db->loadResultArray();

		// Zusätzliche E-Mail Adressen aus den Einstellungen holen
		if ($params->get('emailfornotification'))
		{
			$emailfornotification = explode(",", $params->get('emailfornotification'));
			foreach ($emailfornotification as $type)
			{
				$admins[] = trim($type);
			}
		}

		// Cache leeren
		$this->cleancache();

		$temp = JRequest::get();
		$temp['gbtext'] = JRequest::getVar('gbtext', NULL, 'post', 'none' ,JREQUEST_ALLOWRAW);

		if (isset($temp['id']))
		{
			$id = $temp['id'];
		}
		else
		{
			$id = 0;
		}

		$name = $temp['gbname'];
		if (!empty($temp['gbtitle']))
		{
			$title = $temp['gbtitle'];
		}
		else
		{
			$title = '';
		}
		$text = $temp['gbtext'];

		if (isset($temp['gbip']))
		{
			$gbip = $temp['gbip'];
		}
		else
		{
			$gbip = '0.0.0.0';
		}

		if (($id == 0 && _EASYBOOK_CANADD) || ($id != 0 && _EASYBOOK_CANEDIT))
		{
			$model = $this->getModel('entry');

			if ($row = $model->store())
			{
				if ($params->get('default_published', true))
				{
					$msg = JText::_('ENTRY_SAVED');
					$type = 'message';
				}
				else
				{
					$msg = JText::_('ENTRY_SAVED_BUT_HAS_TO_BE_APPROVED');
					$type = 'notice';
				}
				$link = JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded', false);

				// Benachrichtigungsmail an Administratoren und zusätzlicher E-Mail Adresse senden
				if ($id == 0 AND $params->get('send_mail', true))
				{
					// Hash für Links in der Mail generieren - Easybook Reloaded
					$hash = array();
					$hash['id'] = (int)$row->get('id');
					$hash['gbmail'] = md5($row->get('gbmail'));
					$hash['custom_secret'] = $params->get('secret_word');
					$hash['username'] = $row->get('gbname');
					$hash = serialize($hash);
					$crypt = new JSimpleCrypt();
					$hash = $crypt->encrypt($hash);
					$hash = base64_encode($hash);
					
					$db->setQuery("SELECT `id` FROM #__menu WHERE link = 'index.php?option=com_easybookreloaded&view=easybookreloaded' AND published = 1");
					$Itemid = $db->loadResult();

					$href = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRoute($row->get('id'), $Itemid);

					// Adminlinks verschlüsseln
					$hashmail_publish = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHashPublish($row->get('id'), $Itemid).$hash;
					$hashmail_comment = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHashComment($row->get('id'), $Itemid).$hash;
					$hashmail_edit = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHashEdit($row->get('id'), $Itemid).$hash;
					$hashmail_delete = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRouteHashDelete($row->get('id'), $Itemid).$hash;

					// Mailfunktion initialisieren
					$mail->setsubject(JTEXT::_('NEW_GUESTBOOKENTRY'));
					if ($params->get('send_mail_html'))
					{
						$mail->IsHTML(true);
						$mail->setbody(JTEXT::sprintf('A_NEW_GUESTBOOKENTRY_HAS_BEEN_WRITTEN_HTML', $uri->base(), $name, $title, $text, $href, $hashmail_publish, $hashmail_comment, $hashmail_edit, $hashmail_delete));
					}
					else
					{
						$mail->setbody(JTEXT::sprintf('A_NEW_GUESTBOOKENTRY_HAS_BEEN_WRITTEN', $uri->base(), $name, $title, $text, $href, $hashmail_publish, $hashmail_comment, $hashmail_edit, $hashmail_delete));
					}
					$mail->addrecipient($admins);
					// ReplyTo - Daten aus übergebenem Array bei erfolgreicher Eingabe übernehmen - 2.0.7.1
					$replyto = array($row->get('gbmail'), $row->get('gbname'));
					$mail->addReplyTo($replyto);
					$mail->send();
				}
			}
			else
			{
				$errors_output = array();
				$errors_array = array_keys($session->get('errors', null, 'easybookreloaded'));

				if (in_array("easycalccheck", $errors_array))
				{
					$errors_output[] = JTEXT::_('ERROR_EASYCALCCHECK');
				}
				else
				{
					if (in_array("name", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_NAME');
					}
					if (in_array("mail", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_MAIL');
					}
					if (in_array("title", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_TITLE');
					}
					if (in_array("text", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_TEXT');
					}
					if (in_array("aim", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_AIM');
					}
					if (in_array("icq", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_ICQ');
					}
					if (in_array("yah", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_YAH');
					}
					if (in_array("skype", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_SKYPE');
					}
					if (in_array("msn", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_MSN');
					}
					if (in_array("toomanylinks", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_TOOMANYLINKS');
					}
					if (in_array("iptimelock", $errors_array))
					{
						$errors_output[] = JTEXT::_('ERROR_TIMELOCK');
					}
				}

				$errors = implode(", ", $errors_output);

				$msg = JText::sprintf('PLEASE_VALIDATE_YOUR_INPUTS', $errors);
				$link = JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add&retry=true', false);
				$type = 'notice';

				$session->clear('errors', 'easybookreloaded');
			}
			$this->setRedirect($link, $msg, $type);
		}
		else
		{
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
		}
	}

	 /**
	 * comment record
	 * @return void
	 */
	function comment()
	{
		// Kommentarformular laden
		JRequest::setVar('view', 'entry');
		JRequest::setVar('layout', 'commentform');
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	 /**
	 * remove record
	 * @return void
	 */
	function remove()
	{
		$this->cleancache();
		// Load model and DELETE_ENTRY - redirect afterwards
		$model = $this->getModel('entry');
		if (!$model->delete())
		{
			$msg = JText::_('ERROR_ENTRY_COULD_NOT_BE_DELETED');
			$type = 'error';
		}
		else
		{
			$msg = JText::_('ENTRY_DELETED');
			$type = 'message';
		}
		$this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded', false), $msg, $type);
	}

	function publish()
	{
		$this->cleancache();
		$model = $this->getModel('entry');
		switch($model->publish())
		{
			case -1:
				$msg = JText::_('ERROR_COULD_NOT_CHANGE_PUBLISH_STATUS');
				$type = 'error';
				break;
			case 0:
				$msg = JText::_('ENTRY_UNPUBLISHED');
				$type = 'message';
				break;
			case 1:
				$msg = JText::_('ENTRY_PUBLISHED');
				$type = 'message';
				break;
		}
		$this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded', false), $msg, $type);
	}

	 /**
	 * save a comment
         * @return void
	*/
	function savecomment()
	{
		$this->cleancache();
		$model = $this->getModel('entry');
		if (!$row = $model->savecomment())
		{
		      $msg = JText::_('ERROR_COULD_NOT_SAVE_COMMENT');
		      $type = 'error';
		}
		else
		{
		    if (isset($row['inform']) AND $row['inform'] == 1)
			{
				$data = $model->getRow($row['id']);
				$uri = JFactory::getURI();
				$mail = JFactory::getMailer();
				$params = JComponentHelper::getParams('com_easybookreloaded');
				$temp = JRequest::get();
				require_once(JPATH_SITE.DS.'components'.DS.'com_easybookreloaded'.DS.'helpers'.DS.'route.php');

				$db = JFactory::getDBO();
				$db->setQuery("SELECT `id` FROM #__menu WHERE link = 'index.php?option=com_easybookreloaded&view=easybookreloaded' AND published = 1");
				$Itemid = $db->loadResult();
				
				$href = $uri->base().EasybookReloadedHelperRoute::getEasybookReloadedRoute($data->get('id'), $Itemid);
				$mail->setsubject(JTEXT::_('ADMIN_COMMENT_SUBJECT'));
				if ($params->get('send_mail_html'))
				{
					$mail->IsHTML(true);
					$mail->setbody(JTEXT::sprintf('ADMIN_COMMENT_BODY_HTML', $data->get('gbname'), $uri->base(), $href));
				}
				else
				{
					$mail->setbody(JTEXT::sprintf('ADMIN_COMMENT_BODY', $data->get('gbname'), $uri->base(), $href));
				}
				$mail->addrecipient($data->get('gbmail'));
				$mail->send();

				$msg = JText::_('COMMENT_SAVED_INFORM');
			}
			else
			{
				$msg = JText::_('COMMENT_SAVED');
			}
		    $type = 'message';
		}
		$this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded', false ), $msg, $type);
	}


	// Cache der Seiten (Cache Plugin) leeren, damit Einträge sofort sichtbar sind
	function cleancache()
	{
		$cache = JFactory::getCache();
		$id = md5(JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded', false));
		$cache->remove($id, 'page');
		$id_entry = md5(JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add', false));
		$cache->remove($id_entry, 'page');
		$id_entry_retry = md5(JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=add&retry=true', false));
		$cache->remove($id_entry_retry, 'page');
	}
}
?>
