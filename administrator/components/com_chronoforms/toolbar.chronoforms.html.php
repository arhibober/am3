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
class menuChronoContact {

	function form_wizard_menu(){
		JToolBarHelper::title(JText::_('Form Wizard'));
		JToolBarHelper::save('form_wizard');
		JToolBarHelper::apply('apply_wizard_changes');
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
	
	function index_menu(){
		JToolBarHelper::title(JText::_('Forms Manager'));
		JToolBarHelper::addNew();
		JToolBarHelper::custom($task = 'copy', $icon = 'copy_f2.png', $iconOver = 'copy_f2.png', $alt = 'Copy form', $listSelect = true);
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		JToolBarHelper::divider();
		JToolBarHelper::custom($task = 'create_table', $icon = 'wizard.png', $iconOver = 'wizard.png', $alt = 'Create table', $listSelect = true);
		JToolBarHelper::custom($task = 'list_data', $icon = 'properties_f2.png', $iconOver = 'properties_f2.png', $alt = 'Show Data', $listSelect = true);
		JToolBarHelper::divider();
		JToolBarHelper::custom($task = 'backup_forms', $icon = 'backup.png', $iconOver = 'backup.png', $alt = 'Backup Forms', $listSelect = true);
		JToolBarHelper::custom($task = 'restore_forms', $icon = 'dbrestore.png', $iconOver = 'dbrestore.png', $alt = 'Restore Forms', $listSelect = false);
	}
	
	function edit_menu(){
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();		
	}
	
	function create_table_menu(){
		JToolBarHelper::save('save_table', 'Save');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();		
	}
	
	function list_data_menu(){
		JToolBarHelper::deleteList('', 'delete_data', 'Delete');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();		
	}
	
	function show_data_menu(){
		JToolBarHelper::cancel('list_data', 'Cancel');		
	}
	
	function cancel_menu(){
		JToolBarHelper::cancel('index', 'Cancel');		
	}
	
	function validatelicense_menu(){
		JToolBarHelper::title(JText::_('Validate installation'));
		JToolBarHelper::custom($task = 'validatelicense', $icon = 'bKey.png', $iconOver = 'bKey.png', $alt = 'Validate', $listSelect = false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
	}
}
?>