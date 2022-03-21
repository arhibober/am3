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

class PhocaGuestbookCpViewPhocaGuestbook extends JView
{
	protected $state;
	protected $item;
	protected $form;
	protected $tmpl;
	
	public function display($tpl = null) {
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

		JHTML::stylesheet('administrator/components/com_phocaguestbook/assets/phocaguestbook.css' );

		$this->addToolbar();
		parent::display($tpl);
	}
	
	
	protected function addToolbar() {
		
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'phocaguestbooks.php';
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= PhocaGuestbooksHelper::getActions($this->state->get('filter.item_id'), $this->item->id);
		//$paramsC 	= JComponentHelper::getParams('com_phocaguestbook');

		

		$text = $isNew ? JText::_( 'COM_PHOCAGUESTBOOK_NEW' ) : JText::_('COM_PHOCAGUESTBOOK_EDIT');
		JToolBarHelper::title(   JText::_( 'COM_PHOCAGUESTBOOK_ITEM' ).': <small><small>[ ' . $text.' ]</small></small>' , 'guestbook.png');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolBarHelper::apply('phocaguestbook.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('phocaguestbook.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::addNew('phocaguestbook.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		}
	
		if (empty($this->item->id))  {
			JToolBarHelper::cancel('phocaguestbook.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('phocaguestbook.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.phocaguestbook', true );
	}
}
?>
