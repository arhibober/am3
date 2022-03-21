<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
/* ensure that this file is not called from another file */
defined('_JEXEC') or die('Restricted access');
require_once(JApplicationHelper::getPath('admin_html')); 
require_once(JApplicationHelper::getPath('class'));
?>
<?php
//clean the $_POST data
$_POST = JRequest::get('post', JREQUEST_ALLOWRAW);
// case differentiation
switch($task){
	case "form_wizard":
	case "apply_wizard_changes":
		form_wizard($task);
		break;
	case "remove":
		delete_form();
		break;
	case "copy":
		copy_form();
		break;
	case "add":
	case "edit":
		edit_form();
		break;
	case "save":
	case "apply":
		save_form($task);
		break;
	case "create_table":
	case "save_table":
		create_table($task);
		break;
	case "list_data":
		list_data($task);
		break;
	case "show_data":
		show_data($task);
		break;
	case "publish":
	case "unpublish":
		publish($task);
		break;
	case "validatelicense":
		validatelicense($task);
		break;
	case "backup_forms":
		backup_forms();
		break;
	case "restore_forms":
		restore_forms();
		break;
	default:
		index();
		break;
}

function print_r2($array = array()){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

function index(){
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	//prepare the pagination
	$option = 'com_chronoforms';
	$limit = $mainframe->getUserStateFromRequest($option.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int'); 
	$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
	// count entries
	$database->setQuery("SELECT count(*) FROM #__chronoforms");
	$total = $database->loadResult();
	jimport('joomla.html.pagination'); 		
	$pageNav = new JPagination($total, $limitstart, $limit);
	//load forms data
	$val = __getValStatus();
	$database->setQuery("SELECT * FROM #__chronoforms ORDER BY id LIMIT $pageNav->limitstart,$pageNav->limit");
	$forms = $database->loadObjectList();
	if(!empty($forms)){
		foreach($forms as $k => $form){
			//load actions
			$query = "SELECT * FROM `#__chronoform_actions` WHERE `chronoform_id` = '".$form->id."' AND `enabled` = '1' ORDER BY `order`";
			$database->setQuery($query);
			$forms[$k]->form_actions = $database->loadObjectList();
		}
	}
	HTML_ChronoForms::index($forms, $pageNav, $val);
}

function publish($task = 'publish'){
	if(isset($_POST['cb']) && !empty($_POST['cb'])){
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		$published = ($task == 'publish') ? 1 : 0;
		$database->setQuery("UPDATE #__chronoforms SET published='".$published."' WHERE id='".$_POST['cb'][0]."'");
		if(!$database->query()){
			JError::raiseWarning(100, $row->getError());
			$mainframe->redirect("index.php?option=com_chronoforms");
		}
		$mainframe->redirect("index.php?option=com_chronoforms", "Updated successfully!");
	}
}

function edit_form(){
	$form = null;
	if(isset($_POST['cb']) && !empty($_POST['cb'])){
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__chronoforms WHERE id='".$_POST['cb'][0]."'");
		$form = $database->loadObject();
	}
	HTML_ChronoForms::edit($form);
}

function save_form($task = 'save'){
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$row = JTable::getInstance('chronoforms', 'Table');
	if(isset($_POST['params']) && !empty($_POST['params']) && is_array($_POST['params'])){
		$params = array();
		foreach($_POST['params'] as $k => $v){
			$params[] = $k.'='.$v;
		}
		$_POST['params'] = implode("\n", $params);
	}
	$post = JRequest::get('post', JREQUEST_ALLOWRAW);
	if(!$row->bind($post)){
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect("index.php?option=com_chronoforms");
	}
	if(!$row->store()){
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect("index.php?option=com_chronoforms");
	}
	if($task == 'apply'){
		if(isset($row->id) && !empty($row->id)){
			$database->setQuery("SELECT * FROM #__chronoforms WHERE id='".$row->id."'");
			$form = $database->loadObject();
		}
		
		HTML_ChronoForms::edit($form);
	}else{
		$mainframe->redirect("index.php?option=com_chronoforms", "Saved successfully.");
	}
}

function delete_form(){
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	//delete the form with all its actions
	if(isset($_POST['cb']) && !empty($_POST['cb'])){
		foreach($_POST['cb'] as $form_id){
			$database->setQuery("DELETE FROM #__chronoforms WHERE id='".$form_id."'");
			if(!$database->query()){
				JError::raiseWarning(100, $row->getError());
				$mainframe->redirect("index.php?option=com_chronoforms");
			}
			$database->setQuery("DELETE FROM #__chronoform_actions WHERE chronoform_id='".$form_id."'");
			if(!$database->query()){
				JError::raiseWarning(100, $row->getError());
				$mainframe->redirect("index.php?option=com_chronoforms");
			}
		}
	}
	$mainframe->redirect("index.php?option=com_chronoforms", "Deleted successfully.");
}

function copy_form(){
	$mainframe = JFactory::getApplication();
	$database =& JFactory::getDBO();
	$row =& JTable::getInstance('chronoforms', 'Table');
	if(isset($_POST['cb']) && !empty($_POST['cb'])){		
		$row->load($_POST['cb'][0]);
		$row->id = '';
		$row->name = $row->name."-Copy";
		if(!$row->store()){
			JError::raiseWarning(100, $row->getError());
			$mainframe->redirect("index.php?option=com_chronoforms");
		}
		//copy actions as well
		$new_id = $row->id;
		unset($row);
		$query = "SELECT `id` FROM `#__chronoform_actions` WHERE `chronoform_id` = '".$_POST['cb'][0]."'";
		$database->setQuery($query);
		$row_ids = $database->loadResultArray(); 
		foreach($row_ids as $id){
			$row =& JTable::getInstance('chronoformActions', 'Table');
			$row->load($id);
			$row->id = '';
			$row->chronoform_id = $new_id;
			if(!$row->store()){
				JError::raiseWarning(100, $row->getError());
				$mainframe->redirect("index.php?option=com_chronoforms");
			}
		}
	}
	$mainframe->redirect("index.php?option=com_chronoforms", "Form successfully copied.");
}

function backup_forms(){
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	if(isset($_POST['cb']) && !empty($_POST['cb'])){
		$output = '';
		foreach($_POST['cb'] as $form_id){
			$database->setQuery("SELECT * FROM #__chronoforms WHERE id='".$form_id."'");
			$form = $database->loadAssoc();
			$database->setQuery("SELECT * FROM #__chronoform_actions WHERE chronoform_id='".$form_id."' ORDER BY `order` ASC");
			$formactions = $database->loadAssocList();
			$output .= "<__FORM_START__><__FORM_ROW_START__>".base64_encode(serialize($form))."<__FORM_ROW_END__><__FORM_ACTIONS_START__>".base64_encode(serialize($formactions))."<__FORM_ACTIONS_END__><__FORM_END__>"."\n";
		}
		//get the domain name
		$uri = JFactory::getURI();
		preg_match('/http(s)*:\/\/(.*?)\//i', $uri->root(), $matches);
		$domain = $matches[2];
		//download the file
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "Opera";
		}
		elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT'])) {
			$UserBrowser = "IE";
		} else {
			$UserBrowser = '';
		}
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
		@ob_end_clean();
		ob_start();
	
		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	
		if ($UserBrowser == 'IE') {
			header('Content-Disposition: inline; filename="' . "CFV4_FormsBackup_ON_".$domain."_".date('d_M_Y_H:i:s').'.cf4bak"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else {
			header('Content-Disposition: attachment; filename="' . "CFV4_FormsBackup_ON_".$domain."_".date('d_M_Y_H:i:s').'.cf4bak"');
			header('Pragma: no-cache');
		}
		print $output;
		exit();
	}
}

function restore_forms(){
	$mainframe = JFactory::getApplication();	
	$database = JFactory::getDBO();
	jimport('joomla.utilities.error');
	jimport('joomla.filesystem.file');
	$files = $_FILES;
	if(is_array($files) && !empty($files)){
		//the file has been uploaded
		$file = $files['file'];
		$filename = $file['name'];
		$exten = explode(".", $filename);
		if($exten[count($exten)-1] == 'cf4bak'){			
			$path = JPATH_BASE.DS.'cache';
			$uploadedfile = JFile::upload($file['tmp_name'], $path.DS.$filename);
			if(!$uploadedfile){
				JError::raiseWarning(100, "UPLAOD FAILED".": ".$file['error']);
				$mainframe->redirect("index.php?option=com_chronoforms");
			}else{
				$data = file_get_contents($path.DS.$filename);
				preg_match_all('/<__FORM_START__>(.*?)<__FORM_END__>/is', $data, $forms_data);
				//print_r2($forms_data);
				if(isset($forms_data[1]) && !empty($forms_data[1])){
					foreach($forms_data[1] as $k => $form){
						//extract form row data
						preg_match('/<__FORM_ROW_START__>(.*?)<__FORM_ROW_END__>/is', $form, $form_row_data);
						if(isset($form_row_data[1]) && !empty($form_row_data[1])){
							$form_row_data = unserialize(base64_decode($form_row_data[1]));
							unset($form_row_data['id']);
							$row = JTable::getInstance('chronoforms', 'Table');
							if(!$row->bind($form_row_data)){
								JError::raiseWarning(100, $row->getError());
							}
							if(!$row->store()){
								JError::raiseWarning(100, $row->getError());
							}
							$chronoform_id = $row->id;
						}
						//extract form actions data
						preg_match('/<__FORM_ACTIONS_START__>(.*?)<__FORM_ACTIONS_END__>/is', $form, $form_actions_data);
						if(isset($form_actions_data[1]) && !empty($form_actions_data[1])){
							$form_actions_data = unserialize(base64_decode($form_actions_data[1]));
							foreach($form_actions_data as $form_action){
								unset($form_action['id']);
								$form_action['chronoform_id'] = $chronoform_id;
								$row = JTable::getInstance('chronoformactions', 'Table');
								if(!$row->bind($form_action)){
									JError::raiseWarning(100, $row->getError());
								}
								if(!$row->store()){
									JError::raiseWarning(100, $row->getError());
								}
							}
						}
					}
				}
				$mainframe->redirect("index.php?option=com_chronoforms", "Forms restored successfully.");
			}
		}else{
			//wrong file extension
			JError::raiseWarning(100, "The file uploaded was not a Chronoforms V4 forms backup file.");
			$mainframe->redirect("index.php?option=com_chronoforms");
		}
	}else{
		//no file, render the upload page
		HTML_ChronoForms::restore_forms();
	}
}

function form_wizard($task = 'form_wizard'){
	$mainframe = JFactory::getApplication();
	$form = null;
	$formactions = null;
	if($task == 'form_wizard'){
		if(isset($_POST['form_id']) || isset($_GET['form_id'])){
			$form_id = isset($_POST['form_id']) ? (int)$_POST['form_id'] : (int)$_GET['form_id'];
			if($form_id > 0){
				//load existing form
				$mainframe = JFactory::getApplication();
				$database = JFactory::getDBO();
				$database->setQuery("SELECT * FROM #__chronoforms WHERE id='".$form_id."'");
				$form = $database->loadObject();
				$database->setQuery("SELECT * FROM #__chronoform_actions WHERE chronoform_id='".$form_id."' ORDER BY `order` ASC");
				$formactions = $database->loadObjectList();
				//print_r2(unserialize(base64_decode($form->events_actions_map)));
			}
		}else{
			if(!empty($_POST)){
				_save_form_wizard();
				$mainframe->redirect("index.php?option=com_chronoforms", "Form was successfully saved.");
			}
		}
	}else{
		//apply task
		if(!empty($_POST)){
			$form_id = isset($_POST['data']['Chronoform']['id']) ? (int)$_POST['data']['Chronoform']['id'] : (int)$_GET['form_id'];
			$save_id = _save_form_wizard();
			if(!$form_id){
				$form_id = $save_id;
			}
			$mainframe->redirect("index.php?option=com_chronoforms&task=form_wizard&form_id=".$form_id, "Changes applied successfully.");
		}
	}
	HTML_ChronoForms::form_wizard($form, $formactions);
}

function list_data(){
	$mainframe = JFactory::getApplication();
	$database =& JFactory::getDBO();
	if(isset($_POST['cb']) && !empty($_POST['cb'])){
		$index = $_POST['cb'][0];
		$_POST['table_name'] = $_POST['table_name'][$index];
	}
	if((isset($_POST['table_name']) && !empty($_POST['table_name'])) || (isset($_GET['table_name']) && !empty($_GET['table_name']))){
		$table_name = isset($_POST['table_name']) ? $_POST['table_name'] : $_GET['table_name'];
		//load some table data
		$result = $database->getTableFields(array($table_name), false);
		$table_fields = $result[$table_name];
		$primary = '';
		foreach($table_fields as $table_field => $field_data){
			if($field_data->Key == 'PRI'){
				$primary = $table_field;
			}
		}
		//prepare the pagination
		$option = 'com_chronoforms';
		$limit = $mainframe->getUserStateFromRequest($option.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int'); 
		$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
		// count entries
		$database->setQuery("SELECT count(*) FROM `".$table_name."`");
		$total = $database->loadResult();
		jimport('joomla.html.pagination'); 		
		$pageNav = new JPagination($total, $limitstart, $limit);
		//load the data
		$query = "SELECT * FROM `".$table_name."`";
		if(!empty($primary)){
			$query .= " ORDER BY ".$primary;
		}
		$query .= " LIMIT $pageNav->limitstart,$pageNav->limit";
		$database->setQuery($query);
		$table_data = $database->loadObjectList();
		HTML_ChronoForms::list_data($table_name, $table_fields, $table_data, $pageNav);
	}else{
		JError::raiseWarning(100, "No tables were selected.");
		$mainframe->redirect("index.php?option=com_chronoforms");
	}
}

function show_data(){
	$mainframe = JFactory::getApplication();
	$database =& JFactory::getDBO();
	if((isset($_POST['table_name']) && !empty($_POST['table_name'])) || (isset($_GET['table_name']) && !empty($_GET['table_name']))){
		$table_name = isset($_POST['table_name']) ? $_POST['table_name'] : $_GET['table_name'];
		//load some table data
		$result = $database->getTableFields(array($table_name), false);
		$table_fields = $result[$table_name];
		$primary = '';
		foreach($table_fields as $table_field => $field_data){
			if($field_data->Key == 'PRI'){
				$primary = $table_field;
			}
		}
		if(empty($primary)){
			JError::raiseWarning(100, "No table key found.");
			$mainframe->redirect("index.php?option=com_chronoforms");
		}
		//show data
		if(isset($_POST['cb']) && !empty($_POST['cb'])){
			$database->setQuery("SELECT * FROM ".$table_name." WHERE ".$primary."='".$_POST['cb'][0]."'");
			$row_data = $database->loadObject();
			HTML_ChronoForms::show_data($table_name, $table_fields, $row_data);
		}else{
			JError::raiseWarning(100, "Invalid record.");
			$mainframe->redirect("index.php?option=com_chronoforms");
		}
	}else{
		JError::raiseWarning(100, "Table doesn't exist!");
		$mainframe->redirect("index.php?option=com_chronoforms");
	}
}

function create_table($task = 'create_table'){
	$mainframe = JFactory::getApplication();
	$database =& JFactory::getDBO();
	//switch task
	if($task == 'save_table'){
		if(isset($_POST['field_name']) && !empty($_POST['field_name']) && !empty($_POST['_cf_table_name'])){
			$create = array("CREATE TABLE IF NOT EXISTS `".$_POST['_cf_table_name']."` (");
			$primary_key = "";
			foreach($_POST['field_name'] as $k => $name){
				$name = trim($name);
				if(!empty($name) && isset($_POST['enabled'][$k]) && ((int)$_POST['enabled'][$k] == 1)){
					$length = "";
					if(!empty($_POST['field_length'][$k])){
						$length = "(".$_POST['field_length'][$k].")";
					}
					$default = "";
					if(!empty($_POST['field_default'][$k])){
						$default = " default '".$_POST['field_default'][$k]."'";
					}
					$extra = "";
					if(!empty($_POST['field_extra'][$k])){
						$extra = " ".$_POST['field_extra'][$k];
					}
					$create[] = "`".$name."` ".$_POST['field_type'][$k].$length.$default.$extra.",";
					if(isset($_POST['field_key'][$k]) && (int)$_POST['field_key'][$k] == 1){
						$primary_key = "PRIMARY KEY  (`".$name."`)";
					}
				}
			}
			if(!empty($primary_key)){
				$create[] = $primary_key;
			}
			$create[] = ");";
			$create = implode("\n", $create);
			$database->setQuery($create);
			if(!$database->query()){
				JError::raiseWarning(100, $row->getError());
				$mainframe->redirect("index.php?option=com_chronoforms");
			}
			$mainframe->redirect("index.php?option=com_chronoforms", "Table successfully created.");
		}
	}else{
		$row =& JTable::getInstance('chronoforms', 'Table');
		if(isset($_POST['cb']) && !empty($_POST['cb'])){		
			$row->load($_POST['cb'][0]);
			$form_code = $row->content;
			$fields_names = _getFormFieldsNames($form_code);		
		}else{
			JError::raiseWarning(100, 'Invalid record!');
			$mainframe->redirect("index.php?option=com_chronoforms");
		}
		$defaults = array(
			'cf_id' => array('type' => 'INT', 'length' => '11', 'default' => '', 'key' => 'PRI', 'extra' => 'auto_increment', 'enabled' => 1),
			'cf_uid' => array('type' => 'VARCHAR', 'length' => '255', 'default' => '', 'key' => '', 'extra' => '', 'enabled' => 1),
			'cf_created' => array('type' => 'DATETIME', 'length' => '', 'default' => '', 'key' => '', 'extra' => '', 'enabled' => 1),
			'cf_modified' => array('type' => 'DATETIME', 'length' => '', 'default' => '', 'key' => '', 'extra' => '', 'enabled' => 1),
			'cf_ipaddress' => array('type' => 'VARCHAR', 'length' => '255', 'default' => '', 'key' => '', 'extra' => '', 'enabled' => 1),
			'cf_user_id' => array('type' => 'VARCHAR', 'length' => '255', 'default' => '', 'key' => '', 'extra' => '', 'enabled' => 1)
		);
		foreach($fields_names as $name){
			$defaults[$name] = array('type' => 'VARCHAR', 'length' => '255', 'default' => '', 'key' => '', 'extra' => '', 'enabled' => 1);
		}
	}
	HTML_ChronoForms::create_table($row, $defaults);
}

function __getValStatus(){
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$database->setQuery("SELECT * FROM #__extensions WHERE `element` = 'com_chronoforms' AND `type` = 'component'");
	$result = $database->loadObject();
	if(!empty($result)){
		$params = new JParameter($result->params);
		return (bool)$params->get('licensevalid', 0);
	}else{
		return false;
	}
}

function _getFormFieldsNames($form_code){
	$fields_names = array();
	$pattern_input = '/name=("|\')([^(>|"|\')]*?)("|\')/i';
    preg_match_all($pattern_input, $form_code, $matches);
	foreach($matches[2] as $match){
		if(strpos($match, '[]')){
			$match = str_replace('[]', '', $match);
		}
		$fields_names[] = trim($match);
	}
	$fields_names = array_unique($fields_names);
	return $fields_names;
}

function _save_form_wizard(){
	//generate XML code for the form
	$chronoform = array();
	$formdata = array();			
	if(!empty($_POST['chronofield'])){
		foreach($_POST['chronofield'] as $key => $fielddata){
			$formdata['field_'.$key] = array();
			foreach($fielddata as $fieldname => $fieldvalue){
				$formdata['field_'.$key][$fieldname] = $fieldvalue;
			}
		}
	}
	//if easy mode, load the preset events actions
	if(isset($_POST['wizard_mode']) && $_POST['wizard_mode'] == 'easy'){
		$_POST['chronoaction'][3] = array('type' => 'show_html');
		$_POST['chronoaction'][5] = array('type' => 'event_loop');
		$_POST['chronoaction'][7] = array('type' => 'event_loop');
	}
	//prepare the actions details for the model
	$formactionsdata = array();
	$action_count = 0;
	if(!empty($_POST['chronoaction'])){
		foreach($_POST['chronoaction'] as $key => $actiondata){
			$type = $_data['ChronoformAction'][$action_count]['type'] = $actiondata['type'];
			$_data['ChronoformAction'][$action_count]['order'] = $key;
			
			if(isset($actiondata['action_'.$actiondata['type'].'_'.$key.'_enabled'])){
				$_data['ChronoformAction'][$action_count]['enabled'] = $actiondata['action_'.$actiondata['type'].'_'.$key.'_enabled'];
				unset($actiondata['action_'.$actiondata['type'].'_'.$key.'_enabled']);
			}else{
				$_data['ChronoformAction'][$action_count]['enabled'] = 1;
			}
			//$_data['ChronoformAction'][$action_count]['event'] = $actiondata['action_'.$actiondata['type'].'_'.$key.'_event'];					
			//unset($actiondata['action_'.$actiondata['type'].'_'.$key.'_event']);
			if(isset($actiondata['action_'.$actiondata['type'].'_'.$key.'_content1'])){
				$_data['ChronoformAction'][$action_count]['content1'] = $actiondata['action_'.$actiondata['type'].'_'.$key.'_content1'];
				unset($actiondata['action_'.$actiondata['type'].'_'.$key.'_content1']);
			}
			unset($actiondata['type']);
			
			foreach($actiondata as $actionname => $actionvalue){
				$actiondata[str_replace('action_'.$type.'_'.$key.'_', '', $actionname)] = $actionvalue;
				unset($actiondata[$actionname]);
			}
			$_data['ChronoformAction'][$action_count]['params'] = $actiondata;					
			$action_count++;
		}
	}	
	
	$chronoform['formcode'] = $formdata;
	/*print_r2($_data);
	die();
	/*echo '<pre>';
	print_r($formdata);
	print_r($_data);
	print_r($_POST['_form_actions_events_map']);
	echo '</pre>';*/
	
	//save form
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$row = JTable::getInstance('chronoforms', 'Table');
	if(isset($_POST['data']['Chronoform']['id']) && !empty($_POST['data']['Chronoform']['id'])){
		$_data['Chronoform']['id'] = $_POST['data']['Chronoform']['id'];
		if(isset($_POST['wizard_mode'])){// && $_POST['wizard_mode'] == 'easy'){
			//load the form
			$database = JFactory::getDBO();
			$database->setQuery("SELECT * FROM #__chronoforms WHERE id='".$_data['Chronoform']['id']."'");
			$form = $database->loadObject();
			$_data['Chronoform']['params'] = 'form_mode='.$_POST['wizard_mode'].''."\n".trim($form->params)."\n".'form_mode='.$_POST['wizard_mode'];
		}
	}else{
		$_data['Chronoform']['form_type'] = 1; //this is a new form, set it as WIZARD form
		if(isset($_POST['wizard_mode'])){// && $_POST['wizard_mode'] == 'easy'){
			$_data['Chronoform']['params'] = 'form_mode='.$_POST['wizard_mode'];
		}
	}
	$_data['Chronoform']['name'] = $_POST['chronoform_name'];
	$_data['Chronoform']['published'] = $_POST['chronoform_published'];
	if(isset($_POST['form_type']) && (int)$_POST['form_type'] != 0){
		$_data['Chronoform']['content'] = _processWizardCode($formdata);
		$_data['Chronoform']['wizardcode'] = var_export($formdata, true);
		/*if(strpos($form->form_details->content, 'validate[') !== false){
			
		}*/
	}
	//get the actions events map
	$_data['Chronoform']['events_actions_map'] = base64_encode(serialize($_POST['_form_actions_events_map']['myform']));
	//if easy mode, load the preset events map
	if(isset($_POST['wizard_mode']) && $_POST['wizard_mode'] == 'easy'){
		$_data['Chronoform']['events_actions_map'] = "YToxOntzOjY6ImV2ZW50cyI7YToyOntzOjQ6ImxvYWQiO2E6MTp7czo3OiJhY3Rpb25zIjthOjQ6e3M6MTg6ImNmYWN0aW9uX2xvYWRfanNfMCI7czowOiIiO3M6MTk6ImNmYWN0aW9uX2xvYWRfY3NzXzEiO3M6MDoiIjtzOjIzOiJjZmFjdGlvbl9sb2FkX2NhcHRjaGFfMiI7czowOiIiO3M6MjA6ImNmYWN0aW9uX3Nob3dfaHRtbF8zIjtzOjA6IiI7fX1zOjY6InN1Ym1pdCI7YToxOntzOjc6ImFjdGlvbnMiO2E6OTp7czoyNDoiY2ZhY3Rpb25fY2hlY2tfY2FwdGNoYV80IjthOjE6e3M6NjoiZXZlbnRzIjthOjI6e3M6Mzc6ImNmYWN0aW9uZXZlbnRfY2hlY2tfY2FwdGNoYV80X3N1Y2Nlc3MiO3M6MDoiIjtzOjM0OiJjZmFjdGlvbmV2ZW50X2NoZWNrX2NhcHRjaGFfNF9mYWlsIjthOjE6e3M6NzoiYWN0aW9ucyI7YToxOntzOjIxOiJjZmFjdGlvbl9ldmVudF9sb29wXzUiO3M6MDoiIjt9fX19czoyMzoiY2ZhY3Rpb25fdXBsb2FkX2ZpbGVzXzYiO2E6MTp7czo2OiJldmVudHMiO2E6Mjp7czozNjoiY2ZhY3Rpb25ldmVudF91cGxvYWRfZmlsZXNfNl9zdWNjZXNzIjtzOjA6IiI7czozMzoiY2ZhY3Rpb25ldmVudF91cGxvYWRfZmlsZXNfNl9mYWlsIjthOjE6e3M6NzoiYWN0aW9ucyI7YToxOntzOjIxOiJjZmFjdGlvbl9ldmVudF9sb29wXzciO3M6MDoiIjt9fX19czoyMjoiY2ZhY3Rpb25fY3VzdG9tX2NvZGVfOCI7czowOiIiO3M6MTg6ImNmYWN0aW9uX2RiX3NhdmVfOSI7czowOiIiO3M6MTc6ImNmYWN0aW9uX2VtYWlsXzEwIjtzOjA6IiI7czoxNzoiY2ZhY3Rpb25fZW1haWxfMTEiO3M6MDoiIjtzOjE3OiJjZmFjdGlvbl9lbWFpbF8xMiI7czowOiIiO3M6MjM6ImNmYWN0aW9uX2N1c3RvbV9jb2RlXzEzIjtzOjA6IiI7czozMToiY2ZhY3Rpb25fc2hvd190aGFua3NfbWVzc2FnZV8xNCI7czowOiIiO319fX0=";
	}
	
	if(!$row->bind($_data['Chronoform'])){
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect("index.php?option=com_chronoforms");
	}
	if(!$row->store()){
		JError::raiseWarning(100, $row->getError());
		$mainframe->redirect("index.php?option=com_chronoforms");
	}
	$chronoform_id = $row->id;
	//save actions
	if(isset($_data['ChronoformAction']) && !empty($_data['ChronoformAction'])){
		//delete previous actions to save new ones
		if(isset($_POST['data']['Chronoform']['id']) && !empty($_POST['data']['Chronoform']['id'])){
			$database->setQuery("DELETE FROM #__chronoform_actions WHERE chronoform_id='".$_POST['data']['Chronoform']['id']."'");
			if(!$database->query()){
				JError::raiseWarning(100, $row->getError());
				$mainframe->redirect("index.php?option=com_chronoforms");
			}
		}
		//save all new actions
		foreach($_data['ChronoformAction'] as $action){
			$row = JTable::getInstance('chronoformactions', 'Table');
			$action['chronoform_id'] = $chronoform_id;
			$params = array();
			if(isset($action['params']) && is_array($action['params'])){
				foreach($action['params'] as $k => $param){
					$params[] = $k.'='.$param;
				}
				$action['params'] = implode("\n", $params);
			}
			if(!$row->bind($action)){
				JError::raiseWarning(100, $row->getError());
				$mainframe->redirect("index.php?option=com_chronoforms");
			}
			if(!$row->store()) {
				JError::raiseWarning(100, $row->getError());
				$mainframe->redirect("index.php?option=com_chronoforms");
			}
		}
	}
	return $chronoform_id;
}

function _processWizardCode($formdata){
	$content = '';
	require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_chronoforms".DS."helpers".DS."html_helper.php");
	$HtmlHelper = new HtmlHelper();
	foreach($formdata as $formdata_key => $formdata_element){
		$field_header = $formdata_element['tag'].'_'.$formdata_element['type'].'_'.str_replace('field_', '', $formdata_key);
		$formcontent_item_array = array();
		$field_name = $formdata_element[$field_header.'_input_name'];
		//disable label if empty
		if(isset($formdata_element[$field_header.'_label_text']) && strlen($formdata_element[$field_header.'_label_text'])){
			$formcontent_item_array['label'] = $formdata_element[$field_header.'_label_text'];				
		}else{
			$formcontent_item_array['label'] = false;
		}
		switch($formdata_element['type']){
		case 'select':
			if(strlen($formdata_element[$field_header.'_showempty'])){
				$showempty = $formdata_element[$field_header.'_showempty'];
			}else{
				$showempty = false;
			}
			$formcontent_item_array['empty'] = $showempty;
			if((bool)$formdata_element[$field_header.'_multiple']){
				$formcontent_item_array['multiple'] = (bool)$formdata_element[$field_header.'_multiple'];
			}
			$formcontent_item_array['size'] = $formdata_element[$field_header.'_size'];
			$formcontent_item_array['selected'] = $formdata_element[$field_header.'_selected'];
			$temp_options = explode("\n", $formdata_element[$field_header.'_options']);
			foreach($temp_options as $temp_option){
				$temp_option_details = explode('=', $temp_option);
				$formcontent_item_array['options'][strval($temp_option_details[0])] = trim($temp_option_details[1]);
			}
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['class'] = $formdata_element[$field_header.'_input_class'];
			$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
			$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
			$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
			$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'textarea':
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['default'] = $formdata_element[$field_header.'_input_value'];
			$formcontent_item_array['cols'] = $formdata_element[$field_header.'_input_cols'];
			$formcontent_item_array['rows'] = $formdata_element[$field_header.'_input_rows'];
			$formcontent_item_array['class'] = $formdata_element[$field_header.'_input_class'];
			$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
			$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
			$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
			$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'radio':
			if(!empty($formdata_element[$field_header.'_input_value'])){
				$formcontent_item_array['value'] = $formdata_element[$field_header.'_input_value'];
			}
			$temp_options = explode("\n", $formdata_element[$field_header.'_options']);
			foreach($temp_options as $temp_option){
				$temp_option_details = explode('=', $temp_option);
				$formcontent_item_array['options'][strval($temp_option_details[0])] = trim($temp_option_details[1]);
			}
			$formcontent_item_array['legend'] = $formdata_element[$field_header.'_legend'];
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
			$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
			$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
			$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'checkbox_group':
			$formcontent_item_array['selected'] = $formdata_element[$field_header.'_checked'];
			$temp_options = explode("\n", $formdata_element[$field_header.'_options']);
			foreach($temp_options as $temp_option){
				$temp_option_details = explode('=', $temp_option);
				$formcontent_item_array['options'][strval($temp_option_details[0])] = trim($temp_option_details[1]);
			}
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
			$formcontent_item_array['multiple'] = 'checkbox';
			$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
			$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
			$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'checkbox':
			$formcontent_item_array['checked'] = (bool)$formdata_element[$field_header.'_checked'];
			$formcontent_item_array['value'] = $formdata_element[$field_header.'_input_value'];
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
			$formcontent_item_array['label_position'] = $formdata_element[$field_header.'_label_position'];
			$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
			$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
			$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'hidden':
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['default'] = $formdata_element[$field_header.'_input_value'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'submit':
			$formcontent_item_array['name'] = $formdata_element[$field_header.'_input_name'];
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['class'] = $formdata_element[$field_header.'_input_class'];
			$formcontent_item_array['value'] = $formdata_element[$field_header.'_input_value'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'file':
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['class'] = $formdata_element[$field_header.'_input_class'];
			$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
			$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
			$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
			$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		case 'text':
		case 'password':
			$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
			$formcontent_item_array['default'] = $formdata_element[$field_header.'_input_value'];
			$formcontent_item_array['maxlength'] = $formdata_element[$field_header.'_input_maxlength'];
			$formcontent_item_array['size'] = $formdata_element[$field_header.'_input_size'];
			$formcontent_item_array['class'] = $formdata_element[$field_header.'_input_class'];
			$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
			$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
			$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
			$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
			$formcontent_item_array['type'] = $formdata_element['type'];
			break;
		default:				
			if(isset($formdata_element[$field_header.'_input_id'])){
				$formcontent_item_array['id'] = $formdata_element[$field_header.'_input_id'];
				unset($formdata_element[$field_header.'_input_id']);
			}
			if(isset($formdata_element[$field_header.'_input_value'])){
				$formcontent_item_array['default'] = $formdata_element[$field_header.'_input_value'];
				unset($formdata_element[$field_header.'_input_value']);
			}
			if(isset($formdata_element[$field_header.'_input_maxlength'])){
				$formcontent_item_array['maxlength'] = $formdata_element[$field_header.'_input_maxlength'];
				unset($formdata_element[$field_header.'_input_maxlength']);
			}
			if(isset($formdata_element[$field_header.'_input_size'])){
				$formcontent_item_array['size'] = $formdata_element[$field_header.'_input_size'];
				unset($formdata_element[$field_header.'_input_size']);
			}
			if(isset($formdata_element[$field_header.'_input_class'])){
				$formcontent_item_array['class'] = $formdata_element[$field_header.'_input_class'];
				unset($formdata_element[$field_header.'_input_class']);
			}
			if(isset($formdata_element[$field_header.'_input_title'])){
				$formcontent_item_array['title'] = $formdata_element[$field_header.'_input_title'];
				unset($formdata_element[$field_header.'_input_title']);
			}
			if(isset($formdata_element[$field_header.'_validations'])){
				$formcontent_item_array['validations'] = $formdata_element[$field_header.'_validations'];
				unset($formdata_element[$field_header.'_validations']);
			}
			if(isset($formdata_element[$field_header.'_instructions'])){
				$formcontent_item_array['smalldesc'] = $formdata_element[$field_header.'_instructions'];
				unset($formdata_element[$field_header.'_instructions']);
			}
			if(isset($formdata_element[$field_header.'_tooltip'])){
				$formcontent_item_array['tooltip'] = $formdata_element[$field_header.'_tooltip'];
				unset($formdata_element[$field_header.'_tooltip']);
			}
			if(isset($formdata_element['real_type'])){
				$formcontent_item_array['type'] = $formdata_element['real_type'];
				unset($formdata_element['real_type']);
				unset($formdata_element['type']);
				unset($formdata_element['tag']);
			}
			//unset the name and the label			
			unset($formdata_element[$field_header.'_input_name']);
			unset($formdata_element[$field_header.'_label_text']);
			//load the field params array
			foreach($formdata_element as $k => $v){
				$formcontent_item_array[str_replace($field_header.'_', '', $k)] = $formdata_element[$k];
			}
			break;
		}
		$content .= $HtmlHelper->input($field_name, $formcontent_item_array);		
	}
	return $content;
}

function validationconnect($type, $host, $port='80', $path='/', $data=array()) {
	$mainframe = JFactory::getApplication();
    $_err = 'lib sockets::'.__FUNCTION__.'(): ';
	$str = '';
	$d = array();
    //switch($type) { case 'http': $type = ''; case 'ssl': continue; default: die($_err.'bad $type'); }
	
    if(!empty($data)){
		foreach($data as $k => $v){
			$strarr[] = urlencode($k).'='.urlencode($v);
		}
	}
	$str = implode('&', $strarr);
	$result = '';
	//echo $str;
    $fp = fsockopen($host, $port, $errno, $errstr, 30);
    if(!$fp){
		//$mainframe->redirect( "index.php?option=com_chronoforms", $_err.$errstr.$errno);
		$result = 'error';
		//die($_err.$errstr.$errno);
	}else{
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ".strlen($str)."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $str."\r\n\r\n");
       
        while(!feof($fp)){		
			$d[] = fgets($fp,4096);
		}
        fclose($fp);
		$result = $d[count($d) - 1];
    } return $result;
}

function validatelicense(){
	if(isset($_POST['instantcode'])){
		validatelicensedata();
	}else{
		$database = JFactory::getDBO();
		$query = "SELECT * FROM `#__extensions` WHERE `element` = 'com_chronoforms' AND `type` = 'component'";
		$database->setQuery( $query );
		$result = $database->loadObject();
		$params = new JParameter($result->params);
		HTML_ChronoForms::validatelicense($params);
	}
}

function validatelicensedata(){
	$mainframe = JFactory::getApplication();
	$uri = JFactory::getURI();
	preg_match('/http(s)*:\/\/(.*?)\//i', $uri->root(), $matches);
	$database = JFactory::getDBO();
	$query     = "SELECT * FROM `#__extensions` WHERE `element` = 'com_chronoforms' AND `type` = 'component'";
	$database->setQuery( $query );
	$result = $database->loadObject();
	//$configs = JComponentHelper::getParams('com_chronoforms');
	$configs = new JParameter($result->params);
	$postfields = array();
	if(isset($_POST['licensecode']) && !empty($_POST['licensecode'])){
		$configs->set('licensecode', $_POST['licensecode']);
	}
	$postfields['license_key'] = $configs->get('licensecode', '');
	$postfields['domain_name'] = $matches[2];
	$postfields['pid'] = $_POST['pid'];
	$validstatus = false;
	
	if(empty($postfields['license_key'])){
		$mainframe->redirect("index.php?option=com_chronoforms&task=validatelicense", 'You didn\'t enter your validation key.');
	}
	
	if(trim(JRequest::getVar('instantcode')) && $configs->get('licensecode', '')){
		$step1 = base64_decode(trim(JRequest::getVar('instantcode')));
		$step2 = str_replace(substr(md5(str_replace('www.', '', strtolower($matches[2]))), 0, 7), '', $step1);
		$step3 = str_replace(substr(md5(str_replace('www.', '', strtolower($matches[2]))), - strlen(md5(str_replace('www.', '', strtolower($matches[2])))) + 7), '', $step2);
		$step4 = str_replace(substr($configs->get('licensecode', ''), 0, 10), '', $step3);
		$step5 = str_replace(substr($configs->get('licensecode', ''), - strlen($configs->get('licensecode', '')) + 10), '', $step4);
		//echo (int)$step5;return;
		//if((((int)$step5 + (24 * 60 * 60)) > strtotime(date('d-m-Y H:i:s')))||(((int)$step5 - (24 * 60 * 60)) < strtotime(date('d-m-Y H:i:s')))){
		if(((int)$step5 < (strtotime("now") + (24 * 60 * 60)))&&((int)$step5 > (strtotime("now") - (24 * 60 * 60)))){
			$query = "SELECT * FROM `#__extensions` WHERE `element` = 'com_chronoforms' AND `type` = 'component'";
			$database->setQuery( $query );
			$result = $database->loadObject();
			$newline = "\n";
			if($result){
				//$newparams = 'showtipoftheday='.$configs->get('showtipoftheday', 1).$newline.'licensecode='.$configs->get('licensecode', '').$newline.'licensevalid=1';
				$newparams = new JParameter($result->params);
				$newparams->set('licensevalid', 1);
				$newparams->set('licensecode', $configs->get('licensecode', ''));
				$newparams = $newparams->toString();
				$database->setQuery( "UPDATE `#__extensions` SET params='".$newparams."' WHERE extension_id='".$result->extension_id."'");
				if (!$database->query()) {
					JError::raiseWarning(100, $database->getErrorMsg());
					$mainframe->redirect( "index.php?option=com_chronoforms" );
				}
			}
			$mainframe->redirect( "index.php?option=com_chronoforms", 'Your key was validated successfully' );
		}else{
			//$mainframe->redirect( "index.php?option=com_chronoforms", 'Invalid instant code' );
		}
	}
	
	if(function_exists('fsockopen')){	
		$validstatus = validationconnect('http', 'www.chronoengine.com', $port='80', $path='/index.php?option=com_chronocontact&task=extra&chronoformname=validateLicense', $postfields);
	}
	
	if((!$validstatus)||($validstatus == 'error')||!function_exists('fsockopen')){
		if (!function_exists('curl_init')){
			$validstatus = false;
		}else{
			$fields = ''; 
			$ch = curl_init();
			//$postfields = array();
			foreach( $postfields as $key => $value ) $fields .= "$key=" . urlencode( $value ) . "&";			
			curl_setopt($ch, CURLOPT_URL, 'http://www.chronoengine.com/index.php?option=com_chronocontact&task=extra&chronoformname=validateLicense');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $fields, "& " ));
			if($configs->get('curlproxy', '')){
				curl_setopt($ch, CURLOPT_PROXY, $configs->get('curlproxy'));
			}
			$output = curl_exec($ch);
			curl_close($ch);
			$validstatus = $output;
		}
	}
	//return $validstatus;
	if($validstatus == 'valid'){
		if($configs->get('licensecode', '')){
			//if(!$configs->get('licensevalid', '')){
				$query     = "SELECT * FROM `#__extensions` WHERE `element` = 'com_chronoforms' AND `type` = 'component'";
				$database->setQuery( $query );
				$result = $database->loadObject();
				$newline = "\n";
				if($result){
					//$newparams = 'showtipoftheday='.$configs->get('showtipoftheday', 1).$newline.'licensecode='.$configs->get('licensecode', '').$newline.'licensevalid=1';
					$newparams = new JParameter($result->params);
					$newparams->set('licensevalid', 1);
					$newparams->set('licensecode', $configs->get('licensecode', ''));
					$newparams = $newparams->toString();
					$database->setQuery( "UPDATE `#__extensions` SET params='".$newparams."' WHERE extension_id='".$result->extension_id."'");
					if (!$database->query()) {
						JError::raiseWarning(100, $database->getErrorMsg());
						$mainframe->redirect( "index.php?option=com_chronoforms" );
					}
				}
			//}
		}
		$mainframe->redirect( "index.php?option=com_chronoforms", 'Your Install was validated successfully' );
	}else if($validstatus == 'invalid'){
		$query     = "SELECT * FROM `#__extensions` WHERE `element` = 'com_chronoforms' AND `type` = 'component'";
		$database->setQuery( $query );
		$result = $database->loadObject();
		$newline = "\n";
		if($result){
			//$newparams = 'showtipoftheday='.$configs->get('showtipoftheday', 1).$newline.'licensecode='.$configs->get('licensecode', '').$newline.'licensevalid=0';
			$newparams = new JParameter($result->params);
			$newparams->set('licensevalid', 0);
			$newparams->set('licensecode', $configs->get('licensecode', ''));
			$newparams = $newparams->toString();
			$database->setQuery( "UPDATE `#__extensions` SET params='".$newparams."' WHERE extension_id='".$result->extension_id."'");
			if (!$database->query()) {
				JError::raiseWarning(100, $database->getErrorMsg());
				$mainframe->redirect( "index.php?option=com_chronoforms" );
			}
		}
		$mainframe->redirect( "index.php?option=com_chronoforms", 'We couldn\'t validate your key because of some wrong data used' );
	}else{
		if(trim(JRequest::getVar('instantcode'))){
			$step1 = base64_decode(trim(JRequest::getVar('instantcode')));
			$step2 = str_replace(substr(md5(str_replace('www.', '', strtolower($matches[2]))), 0, 7), '', $step1);
			$step3 = str_replace(substr(md5(str_replace('www.', '', strtolower($matches[2]))), - strlen(md5(str_replace('www.', '', strtolower($matches[2])))) + 7), '', $step2);
			$step4 = str_replace(substr($configs->get('licensecode', ''), 0, 10), '', $step3);
			$step5 = str_replace(substr($configs->get('licensecode', ''), - strlen($configs->get('licensecode', '')) + 10), '', $step4);
			//echo (int)$step5;return;
			//if((((int)$step5 + (24 * 60 * 60)) > strtotime(date('d-m-Y H:i:s')))||(((int)$step5 - (24 * 60 * 60)) < strtotime(date('d-m-Y H:i:s')))){
			if(((int)$step5 < (strtotime("now") + (24 * 60 * 60)))&&((int)$step5 > (strtotime("now") - (24 * 60 * 60)))){
				$query     = "SELECT * FROM `#__extensions` WHERE `element` = 'com_chronoforms' AND `type` = 'component'";
				$database->setQuery( $query );
				$result = $database->loadObject();
				$newline = "\n";
				if($result){
					//$newparams = 'showtipoftheday='.$configs->get('showtipoftheday', 1).$newline.'licensecode='.$configs->get('licensecode', '').$newline.'licensevalid=1';
					$newparams = new JParameter($result->params);
					$newparams->set('licensevalid', 1);
					$newparams->set('licensecode', $configs->get('licensecode', ''));
					$newparams = $newparams->toString();
					$database->setQuery( "UPDATE `#__extensions` SET params='".$newparams."' WHERE extension_id='".$result->extension_id."'");
					if (!$database->query()) {
						JError::raiseWarning(100, $database->getErrorMsg());
						$mainframe->redirect( "index.php?option=com_chronoforms" );
					}
				}
				$mainframe->redirect( "index.php?option=com_chronoforms", 'Your key was validated successfully' );
			}else{
				$mainframe->redirect( "index.php?option=com_chronoforms", 'Invalid instant code' );
			}
		}else{
			$query = "SELECT * FROM `#__extensions` WHERE `element` = 'com_chronoforms' AND `type` = 'component'";
			$database->setQuery( $query );
			$result = $database->loadObject();
			$newline = "\n";
			if($result){
				//$newparams = 'showtipoftheday='.$configs->get('showtipoftheday', 1).$newline.'licensecode='.$configs->get('licensecode', '').$newline.'licensevalid=0';
				$newparams = new JParameter($result->params);
				$newparams->set('licensevalid', 0);
				$newparams->set('licensecode', $configs->get('licensecode', ''));
				$newparams = $newparams->toString();
				$database->setQuery("UPDATE `#__extensions` SET params='".$newparams."' WHERE extension_id='".$result->extension_id."'");
				if (!$database->query()) {
					JError::raiseWarning(100, $database->getErrorMsg());
					$mainframe->redirect( "index.php?option=com_chronoforms" );
				}
			}
			$mainframe->redirect( "index.php?option=com_chronoforms", 'We couldn\'t validate your key because your hosting server doesn\'t have neither the CURL library nor the fsockopen functions or they may exist but don\'t function properly, please contact your host admin to fix them or contact us <a href="http://www.chronoengine.com/contactus.html">here</a> Or at this email address : webmaster@chronoengine.com' );
		}
	}
}
?>