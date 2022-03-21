<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Guestbook
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

class PhocaGuestbookViewGuestbooki extends JView
{
	function display($tpl = null)
	{		
		ob_get_clean();
		$image_data = $this->get('Data');

		$session 	=& JFactory::getSession();
		$app 		= JFactory::getApplication();
		$params		= JComponentHelper::getParams('com_phocaguestbook') ;
		$tmpl['session_suffix']		= $params->get('session_suffix');
		$session->set('pgbsess'.$tmpl['session_suffix'], $image_data['outcome']);//Save image code to session to check with post data
		
		$this->assignRef( 'image',	$image_data['image'] );
		parent::display($tpl);
	}	
}
?>
