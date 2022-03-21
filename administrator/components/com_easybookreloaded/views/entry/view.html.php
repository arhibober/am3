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

class EasybookReloadedViewEntry extends JView
{

	function display($tpl = null)
	{
	    jimport('joomla.html.pane');
	    $mainframe = JFactory::getApplication();
	     
	    JHTML::_('stylesheet', 'easybookreloaded.css', 'administrator/components/com_easybookreloaded/css/');
	     
	    $entry = $this->get('Data');
	    $isNew = ($entry->id < 1);
	
	    $text = $isNew ? JText::_('New') : JText::_('Edit');
	    JToolBarHelper::title(JText::_( 'Entry').': <small><small>['. $text.']</small></small>', 'easybookreloaded');
	    JToolBarHelper::save();
	    if ($isNew) 
		{
	        JToolBarHelper::cancel();
	    } 
		else 
		{
	        // for existing items the button is renamed `close`
	        JToolBarHelper::cancel('cancel', 'Close');
	    }
		
		JHTML::_('behavior.calendar');
		
		$config = JFactory::getConfig();
		$offset = $config->getValue('config.offset');
		
		$date = JFactory::getDate($entry->gbdate);
		$date->setOffset($offset);
		$entry->gbdate = $date->toFormat();
		
	    $this->assignRef('entry', $entry);
	    parent::display($tpl);
	}
}
?>