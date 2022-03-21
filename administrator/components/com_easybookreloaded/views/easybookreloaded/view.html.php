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

 /**
 * Easybook View
 *
 * @package    Easybook
 */
class EasybookReloadedViewEasybookReloaded extends JView
{
	 /**
	 * Easybook view display method
	 * @return void
	 **/
	function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
               
        JToolBarHelper::title(JText::_('Easybookreloaded'), 'easybookreloaded');
        JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
        JToolBarHelper::deleteList();
        JToolBarHelper::editListX();
        JToolBarHelper::addNewX();
        JToolBarHelper::preferences('com_easybookreloaded', '500');
		JHTML::_('stylesheet', 'easybookreloaded.css', 'administrator/components/com_easybookreloaded/css/');
		
        // Get data from the model
        $items = $this->get('Data');
		$pagination = $this->get('Pagination');
		// $version = $this->get('Version'); Deprecated from old Easybook

		$this->assign('version', "<span style='border-bottom: dotted 1px #b9b9b9; padding-right: 5px; padding-left: 5px;'><strong><a href='http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded' target='_blank' title='Easybook Reloaded'>EasyBook Reloaded "._EASYBOOK_VERSION."</a></strong></span>");
		
		$this->assignRef('pagination', $pagination);
        $this->assignRef('items', $items);

        parent::display($tpl);
    }
}
?>