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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.input');

class TablePhocaGuestbook extends JTable
{
	function __construct(& $db) {
		parent::__construct('#__phocaguestbook_items', 'id', $db);
	}
	
	function check() {
		/*
		// If in frontend it is not required
		if (trim( $this->title ) == '') {
			$this->setError( JText::_( 'COM_PHOCAGUESTBOOK_ITEM_MUST_HAVE_TITLE') );
			return false;
		}*/

		if(empty($this->alias)) {
			$this->alias = $this->title;
		}
		if(empty($this->date)) {
			$this->date = JFactory::getDate()->toMySQL();
		}
		
		$this->alias = PhocaGuestbookHelper::getAliasName($this->alias);
		return true;
	}
}
?>