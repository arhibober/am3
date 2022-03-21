<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableBadword extends JTable
{
     /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
    var $word = null;

     /**
     * Constructor
     *
     * @param object Database connector object
     */
    function TableBadword( &$db ) 
	{
        parent::__construct('#__easybook_badwords', 'id', $db);
    }
}
?>