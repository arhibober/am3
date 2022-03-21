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
 
class PhocaGuestbookCpViewPhocaGuestbookbs extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null) {
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		JHTML::stylesheet('administrator/components/com_phocaguestbook/assets/phocaguestbook.css' );
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();
		parent::display($tpl);
		
	}

	
	function addToolbar() {
	
		require_once JPATH_COMPONENT.'/helpers/phocaguestbookbs.php';
	
		$state	= $this->get('State');
		$canDo	= PhocaGuestbookBsHelper::getActions($state->get('filter.book_id'));
	
		JToolBarHelper::title( JText::_( 'COM_PHOCAGUESTBOOK_BOOKS' ), 'guestbook.png' );
	
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('phocaguestbookb.add','JTOOLBAR_NEW');
		}
	
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('phocaguestbookb.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {

			JToolBarHelper::divider();
			JToolBarHelper::custom('phocaguestbookbs.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('phocaguestbookbs.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}
	
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList( 'COM_PHOCAGUESTBOOK_WARNING_DELETE_ITEMS' , 'phocaguestbookbs.delete', 'COM_PHOCAGUESTBOOK_DELETE');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocaguestbook', true );
	}
}
?>