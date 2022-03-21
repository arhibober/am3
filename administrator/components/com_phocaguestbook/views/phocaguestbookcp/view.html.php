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
jimport('joomla.html.pane');

class PhocaGuestbookCpViewPhocaGuestbookcp extends JView
{
	protected $tmpl;
	public function display($tpl = null) {
		JHtml::stylesheet( 'administrator/components/com_phocaguestbook/assets/phocaguestbook.css' );
		$this->tmpl['version'] = PhocaGuestbookHelper::getPhocaVersion();
		$this->addToolbar();
		parent::display($tpl);
		
	}
	
	function addToolbar() {
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocaguestbookcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaGuestbookCpHelper::getActions();
		JToolBarHelper::title( JText::_( 'COM_PHOCAGUESTBOOK_PG_CONTROL_PANEL' ), 'phoca.png' );
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_phocaguestbook');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help( 'screen.phocaguestbook', true );
	}
}
?>