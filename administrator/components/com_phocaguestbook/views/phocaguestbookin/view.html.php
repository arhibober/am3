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
jimport( 'joomla.application.component.view' );

class PhocaGuestbookCpViewPhocaGuestbookIn extends JView
{
	public $tmpl;
	
	public function display($tpl = null) {
		JHTML::stylesheet( 'administrator/components/com_phocaguestbook/assets/phocaguestbook.css' );
		$this->tmpl['version'] = PhocaGuestbookHelper::getPhocaVersion();
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		JToolBarHelper::title( JText::_( 'COM_PHOCAGUESTBOOK_PG_INFO' ), 'info.png' );
		JToolBarHelper::cancel( 'cancel', 'COM_PHOCAGUESTBOOK_CLOSE' );
		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocaguestbook', true );
	}
}
?>
