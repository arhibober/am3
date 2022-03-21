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


class JFormFieldPhocaGuestbookGuestbook extends JFormField
{
	protected $type 		= 'PhocaGuestbookGuestbook';

	protected function getInput() {
		
		$db = &JFactory::getDBO();

		$query = 'SELECT a.title AS text, a.id AS value'
		. ' FROM #__phocaguestbook_books AS a'
		. ' WHERE a.published = 1'
		. ' ORDER BY a.ordering';
		
		
		
		$db->setQuery( $query );
		$guestbooks = $db->loadObjectList();
		
		array_unshift($guestbooks, JHTML::_('select.option', '', '- '.JText::_('COM_PHOCAGUESTBOOK_SELECT_GUESTBOOK').' -', 'value', 'text'));

		return JHTML::_('select.genericlist',  $guestbooks, $this->name, 'class="inputbox"', 'value', 'text', $this->value, $this->id );

		
	}
}
?>