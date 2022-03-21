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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class PhocaGuestbookModelGuestbooki extends JModel
{
	var $_image_data = null;
	
	function &getData()
	{
		//$app	= JFactory::getApplication();
		$paramsC 		= JComponentHelper::getParams('com_phocaguestbook') ;
		$enable_captcha = $paramsC->get( 'enable_captcha', 1 );
		$captchaId		= PhocaguestbookHelperFront::getCaptchaId($enable_captcha);
		
		switch ((int)$captchaId) {
			case 3:
				$this->_image_data = PhocaguestbookHelperCaptchaTTF::createImageData();
			break;
			case 2:
				$this->_image_data = PhocaguestbookHelperCaptchaMath::createImageData();
			break;
			case 1:
			default:
				$this->_image_data = PhocaguestbookHelperCaptcha::createImageData();
			break;
		}
		return $this->_image_data;
	}
}
?>
