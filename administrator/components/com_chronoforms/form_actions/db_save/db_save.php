<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionDbSave{
	var $formname;
	var $formid;
	var $group = array('id' => 'db_operations', 'title' => 'DB Operations');
	var $details = array('title' => 'DB Table save', 'tooltip' => 'Save some data to some database table name.');
	function run($form, $actiondata){
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		
		$params = new JParameter($actiondata->params);
		$table_name = $params->get('table_name', '');
		if(!empty($table_name)){
			$model_id = $params->get('model_id', '');
			if(empty($model_id)){
				$model_id = 'chronoform_data';
			}
			//generate a dynamic model for the table
			$result = $database->getTableFields(array($table_name), false);
			$table_fields = $result[$table_name];
			$dynamic_model_code = array();
			$dynamic_model_code[] = "<?php";	
			$dynamic_model_code[] = "if (!class_exists('Table".str_replace($mainframe->getCfg('dbprefix'), '', $table_name)."')) {";
			$dynamic_model_code[] = "class Table".str_replace($mainframe->getCfg('dbprefix'), '', $table_name)." extends JTable {";	
			$primary = 'id';
			foreach($table_fields as $table_field => $field_data){
				$dynamic_model_code[] = "var \$".$table_field." = null;";
				if($field_data->Key == 'PRI')$primary = $table_field;
			}
			$dynamic_model_code[] = "function __construct(&\$database) {";
			$dynamic_model_code[] = "parent::__construct('".$table_name."', '".$primary."', \$database);";
			$dynamic_model_code[] = "}";
			$dynamic_model_code[] = "}";
			$dynamic_model_code[] = "}";
			$dynamic_model_code[] = "?>";
			$dynamic_model = implode("\n", $dynamic_model_code);
			eval("?>".$dynamic_model);
			//load some variables
			$user = JFactory::getUser();
			$defaults = array(
				'cf_uid' => md5(uniqid(rand(), true)),
				'cf_created' => date('Y-m-d H:i:s', time()),
				'cf_ipaddress' => $_SERVER["REMOTE_ADDR"],
				'cf_user_id' => $user->id
			);
			$row = JTable::getInstance(str_replace($mainframe->getCfg('dbprefix'), '', $table_name), 'Table');
			if((int)$params->get('save_under_modelid', 0) != 1 && !isset($form->data[$model_id])){
				$form->data[$model_id] = $form->data;
			}
			if(!isset($form->data[$model_id])){
				$form->data[$model_id] = array();
			}
			//check if new record or updated one
			if(isset($form->data[$model_id][$primary]) && !empty($form->data[$model_id][$primary])){
				//don't merge, just set a modified date
				$form->data[$model_id] = array_merge(array('cf_modified' => date('Y-m-d H:i:s', time())), $form->data[$model_id]);
			}else{
				$form->data[$model_id] = array_merge($defaults, $form->data[$model_id]);
			}
			if(!$row->bind($form->data[$model_id])){
				$form->debug[] = $row->getError();
			}
			if(!$row->store()){
				$form->debug[] = $row->getError();
			}
			$form->data[$model_id][$primary] = $form->data[strtolower($model_id.'_'.$primary)] = $row->$primary;
		}
	}
	
	function load($clear){
		if($clear){
			$action_params = array(
				'table_name' => '',
				'enabled' => 1,
				'model_id' => 'chronoform_data',
				'save_under_modelid' => 0
			);
		}
		return array('action_params' => $action_params);
	}
}
?>