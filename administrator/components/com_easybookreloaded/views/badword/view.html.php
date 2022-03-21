<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class EasybookReloadedViewBadword extends JView
{
	function display($tpl = null)
	{
	     JHTML::_('stylesheet', 'easybookreloaded.css', 'administrator/components/com_easybookreloaded/css/');
	     
	    $word        =& $this->get('Data');
	    $isNew        = ($word->id < 1);
	
	    $text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
	    JToolBarHelper::title(   JText::_( 'Badword' ).': <small><small>[ ' . $text.' ]</small></small>', 'easybookreloaded' );
	    JToolBarHelper::save();
	    if ($isNew)  {
	        JToolBarHelper::cancel();
	    } else {
	        // for existing items the button is renamed `close`
	        JToolBarHelper::cancel( 'cancel', 'Close' );
	    }
	    $this->assignRef('badword', $word);
	    parent::display($tpl);
	}
}
?>