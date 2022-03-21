<div class="dragable" id="cfaction_db_multi_record_loader">DB Multi Record Loader</div>
<!--start_element_code-->
<div class="element_code" id="cfaction_db_multi_record_loader_element">
	<label class="action_label" style="display: block; float:none!important;">DB Multi Record Loader</label>
	<div id="cfactionevent_db_multi_record_loader_{n}_found" class="form_event good_event">
		<label class="form_event_label">On Record Found</label>
	</div>
	<div id="cfactionevent_db_multi_record_loader_{n}_notfound" class="form_event bad_event">
		<label class="form_event_label">On Empty Result</label>
	</div>
	<!--
	<div id="cfactionevent_db_multi_record_loader_{n}_nodata" class="form_event bad_event">
		<label class="form_event_label">On No/Empty Param Passed</label>
	</div>
	-->
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_dbfield]" id="action_db_multi_record_loader_{n}_dbfield" value="<?php echo $action_params['dbfield']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_table_name]" id="action_db_multi_record_loader_{n}_table_name" value="<?php echo $action_params['table_name']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_request_param]" id="action_db_multi_record_loader_{n}_request_param" value="<?php echo $action_params['request_param']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_load_data]" id="action_db_multi_record_loader_{n}_load_data" value="<?php echo $action_params['load_data']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_model_id]" id="action_db_multi_record_loader_{n}_model_id" value="<?php echo $action_params['model_id']; ?>" />
	
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_fields]" id="action_db_multi_record_loader_{n}_fields" value="<?php echo $action_params['fields']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_enable_association]" id="action_db_multi_record_loader_{n}_enable_association" value="<?php echo $action_params['enable_association']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_associated_models]" id="action_db_multi_record_loader_{n}_associated_models" value="<?php echo $action_params['associated_models']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_db_multi_record_loader_{n}_group_model_data]" id="action_db_multi_record_loader_{n}_group_model_data" value="<?php echo $action_params['group_model_data']; ?>" />
	
	<textarea name="chronoaction[{n}][action_db_multi_record_loader_{n}_content1]" id="action_db_multi_record_loader_{n}_content1" style="display:none"><?php echo $action_params['content1']; ?></textarea>
    
	<input type="hidden" id="chronoaction_id_{n}" name="chronoaction_id[{n}]" value="{n}" />
	<input type="hidden" name="chronoaction[{n}][type]" value="db_multi_record_loader" />
</div>
<!--end_element_code-->
<div class="element_config" id="cfaction_db_multi_record_loader_element_config">
	<?php echo $PluginTabsHelper->Header(array('basic' => 'Basic', 'advanced' => 'Advanced', 'help' => 'Help'), 'authorize_net_config_{n}'); ?>
		<?php echo $PluginTabsHelper->tabStart('basic'); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_dbfield_config', array('type' => 'text', 'label' => "DB Field", 'class' => 'medium_input', 'value' => '', 'smalldesc' => "The field name which will be used to query the table record, if left empty then all records will be loaded.")); ?>
		<?php
			$database = JFactory::getDBO();
			$tables = $database->getTableList();
			$options = array();
			foreach($tables as $table){
				$options[$table] = $table;
			}
		?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_table_name_config', array('type' => 'select', 'label' => 'Table', 'options' => $options, 'empty' => " - ", 'class' => 'medium_input', 'smalldesc' => "The table name to load the data from.")); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_request_param_config', array('type' => 'text', 'label' => "Request Param", 'class' => 'medium_input', 'value' => '', 'smalldesc' => "The param name which will exist in the request url to the form, its value will be used to load the target db record.")); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_model_id_config', array('type' => 'text', 'label' => "Model ID", 'class' => 'medium_input', 'value' => '', 'smalldesc' => "The key under which the loaded record data will be stored in the form->data array.<br />this is obligatory, if left empty then a camilized version of the table name will be used, e.g: jos_my_table = JosMyTable")); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_fields_config', array('type' => 'text', 'label' => "Fields", 'class' => 'big_input', 'label_over' => true, 'smalldesc' => "List of comma separated fields names to load from this table (field_name1,field_name2..etc), leave empty to load all fields.")); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('advanced'); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_load_data_config', array('type' => 'select', 'label' => 'Load Data', 'options' => array(0 => 'No', 1 => 'Yes'), 'class' => 'medium_input', 'smalldesc' => "Do you want to load the data of this model OR just use it in the models associations ?")); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_enable_association_config', array('type' => 'select', 'label' => 'Enable Associations', 'options' => array(0 => 'No', 1 => 'Yes'), 'class' => 'medium_input', 'smalldesc' => "Do you want to enable the associations for this model ? this will allow you to load data from multiple tables and have them associated together.")); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_associated_models_config', array('type' => 'text', 'label' => "Associated Models", 'class' => 'big_input', 'label_over' => true, 'smalldesc' => "list of models ids to associate this one with, comma separated, exactly as they are in other 'Multi DB Record Loader' configs.")); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_group_model_data_config', array('type' => 'select', 'label' => 'Group Model Data', 'options' => array(0 => 'No', 1 => 'Yes'), 'class' => 'medium_input', 'smalldesc' => "This will add a bit of overhead on the server, but will remove any duplicates from teh results and will group any associated models data under the same item.")); ?>
		<?php echo $HtmlHelper->input('action_db_multi_record_loader_{n}_content1_config', array('type' => 'textarea', 'label' => 'WHERE statement', 'rows' => 10, 'cols' => 50, 'smalldesc' => "The code used for the WHERE statement, some notes: <br />
			1 - leave empty to use the default request param with column name formula (associations not enabled), OR to load ALL records (associations enabled).<br />
			2 - don't use the WHERE word.<br />
			3 - in case of associations, pay attention to write the join rule, e.g: `User`.`id` = `Profile`.`user_id`<br />
			4 - in case of associations, if other associated models have WHERE statements then all the WHERE data will be appended using AND.<br />
			5 - You can use PHP code with tags.
		")); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('help'); ?>
		<p>
			<ul>
				<li>Select the table name to load the data from</li>
				<li>Your table must have a primary key.</li>
				<li>Please give your table a unique model id, e.g: jos_content can have "Article".</li>
				<li>If you disable "Load Data" then no db query will run for this model, this is useful when you only need to create an association with another model loading the data.</li>
				<li>If you enable the associations then your model will check for any associated models to load the data from.</li>
				<li>Associated models should have a list of models connected to the current model somehow, these models should be loaded through similar actions in the same form event.</li>
				<li>Grouping model data is useful when you have lots of duplicated records in the data returned, this is usually the case when you have 1 or more models associated.</li>
			</ul>
		</p>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	
</div>