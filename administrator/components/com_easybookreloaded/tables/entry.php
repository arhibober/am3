<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableEntry extends JTable
{
     /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
    var $gbip = null;
    var $gbname = null;
    var $gbmail = null;
    var $gbmailshow = null;
    var $gbloca = null;
    var $gbpage = null;
    var $gbvote = null;
    var $gbtext = null;
    var $gbdate = null;
    var $gbtitle = null;
    var $gbcomment = null;
	var $published = null;
	var $gbicq = null;
	var $gbaim = null;
	var $gbmsn = null;
	var $gbyah = null;
	var $gbskype = null;

     /**
     * Constructor
     *
     * @param object Database connector object
     */
    function TableEntry( &$db )
	{
        parent::__construct('#__easybook', 'id', $db);
    }
}
?>