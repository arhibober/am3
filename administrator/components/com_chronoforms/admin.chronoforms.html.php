<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
/* ensure that this file is called from another file */
defined('_JEXEC') or die('Restricted access');
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::Base().'components/com_chronoforms/css/cc.css');
//admin links
JSubMenuHelper::addEntry(JText::_('COM_CHRONOFORMS_FORMS_MANAGER'), 'index.php?option=com_chronoforms');
JSubMenuHelper::addEntry(JText::_('COM_CHRONOFORMS_WIZARD'), 'index.php?option=com_chronoforms&amp;task=form_wizard');
JSubMenuHelper::addEntry(JText::_('COM_CHRONOFORMS_EASY_WIZARD'), 'index.php?option=com_chronoforms&amp;task=form_wizard&amp;wizard_mode=easy');
JSubMenuHelper::addEntry(JText::_('COM_CHRONOFORMS_VALIDATE'), 'index.php?option=com_chronoforms&amp;task=validatelicense');

JHtml::_('behavior.framework', true);

class HTML_ChronoForms {	
	function form_wizard($form = null, $formactions = null){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."form_wizard.php");
		echo JHTML::_('behavior.keepalive');
	}
	
	function index($forms = array(), $pageNav = null, $params = null){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."index.php");
	}
	
	function edit($form = null){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."edit.php");
		echo JHTML::_('behavior.keepalive');
	}
	
	function create_table($row = null, $defaults = array()){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."create_table.php");
		echo JHTML::_('behavior.keepalive');
	}
	
	function list_data($table_name = '', $table_fields = array(), $table_data = array(), $pageNav = null){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."list_data.php");
	}
	
	function show_data($table_name = '', $table_fields = array(), $row_data = array()){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."show_data.php");
	}
	
	function validatelicense($params){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."validatelicense.php");
	}
	
	function restore_forms(){
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."views".DS."restore_forms.php");
		echo JHTML::_('behavior.keepalive');
	}
}