<div class="dragable" id="cfaction_db_save">DB Save</div>
<!--start_element_code-->
<div class="element_code" id="cfaction_db_save_element">
	<label class="action_label" style="display: block; float:none!important;">DB Save</label>
	<input type="hidden" name="chronoaction[{n}][action_db_save_{n}_table_name]" id="action_db_save_{n}_table_name" value="<?php echo $action_params['table_name']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_save_{n}_model_id]" id="action_db_save_{n}_model_id" value="<?php echo $action_params['model_id']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_save_{n}_save_under_modelid]" id="action_db_save_{n}_save_under_modelid" value="<?php echo $action_params['save_under_modelid']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_save_{n}_enabled]" id="action_db_save_{n}_enabled" value="<?php echo $action_params['enabled']; ?>" />
	
	<input type="hidden" id="chronoaction_id_{n}" name="chronoaction_id[{n}]" value="{n}" />
	<input type="hidden" name="chronoaction[{n}][type]" value="db_save" />
</div>
<!--end_element_code-->
<div class="element_config" id="cfaction_db_save_element_config">
	<?php echo $HtmlHelper->input('action_db_save_{n}_enabled_config', array('type' => 'select', 'label' => 'Enabled', 'options' => array(0 => 'No', 1 => 'Yes'))); ?>
		
	<?php
		$database = JFactory::getDBO();
		$tables = $database->getTableList();
		$options = array();
		foreach($tables as $table){
			$options[$table] = $table;
		}
	?>
	<?php echo $HtmlHelper->input('action_db_save_{n}_table_name_config', array('type' => 'select', 'label' => 'Table', 'options' => $options, 'empty' => " - ", 'class' => 'medium_input', 'smalldesc' => "The db table to which the data will be saved.")); ?>
	<?php echo $HtmlHelper->input('action_db_save_{n}_model_id_config', array('type' => 'text', 'label' => "Model ID", 'class' => 'medium_input', 'value' => '', 'smalldesc' => "1- The array key under which the data to be saved will be expected in the \$_POST array.<br />2- The array key under which the saved data array will exist after the save process. e.g:\$form->data[model_id]")); ?>
	<?php echo $HtmlHelper->input('action_db_save_{n}_save_under_modelid_config', array('type' => 'select', 'label' => 'Save Under Model ID', 'options' => array(0 => 'No', 1 => 'Yes'), 'class' => 'medium_input', 'smalldesc' => "Should we save the data coming under ths Model ID ONLY ? if yes then your data array should include some array of values under a key name equals your model_id value or no form data will be saved.<br /> If you don't know what to do then leave it as <strong>NO</strong>")); ?>
	
</div>