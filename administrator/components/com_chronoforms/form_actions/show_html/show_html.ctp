<div class="dragable" id="cfaction_show_html">Show html</div>
<!--start_element_code-->
<div class="element_code" id="cfaction_show_html_element">
	<label class="action_label" style="display: block; float:none!important;">Show html</label>
	<input type="hidden" name="chronoaction[{n}][action_show_html_{n}_data_republish]" id="action_show_html_{n}_data_republish" value="<?php echo $action_params['data_republish']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_show_html_{n}_display_errors]" id="action_show_html_{n}_display_errors" value="<?php echo $action_params['display_errors']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_show_html_{n}_curly_replacer]" id="action_show_html_{n}_curly_replacer" value="<?php echo $action_params['curly_replacer']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_show_html_{n}_load_token]" id="action_show_html_{n}_load_token" value="<?php echo $action_params['load_token']; ?>" />
	<input type="hidden" name="chronoaction[{n}][action_show_html_{n}_keep_alive]" id="action_show_html_{n}_keep_alive" value="<?php echo $action_params['keep_alive']; ?>" />
	<input type="hidden" id="chronoaction_id_{n}" name="chronoaction_id[{n}]" value="{n}" />
	<input type="hidden" name="chronoaction[{n}][type]" value="show_html" />
</div>
<!--end_element_code-->
<div class="element_config" id="cfaction_show_html_element_config">
	<?php echo $HtmlHelper->input('action_show_html_{n}_data_republish_config', array('type' => 'select', 'label' => 'Republish form data', 'options' => array(0 => 'No', 1 => 'Yes (recommended)'), 'smalldesc' => 'Try to republish the form data in case the form has been reloaded because of some error.')); ?>
	<?php echo $HtmlHelper->input('action_show_html_{n}_display_errors_config', array('type' => 'select', 'label' => 'Display Fields Errors', 'options' => array(0 => 'No', 1 => 'Yes (recommended)'), 'smalldesc' => 'Display server side errors below fields, for this to work you need to have this code in the place you want the error to appear at:<br />&lt;div id="error-message-FIELD_NAME_HERE"&gt;&lt;/div&gt;<br /><br />*This is added automatically when using the wizard.')); ?>
	<?php echo $HtmlHelper->input('action_show_html_{n}_load_token_config', array('type' => 'select', 'label' => 'Load Security Token', 'options' => array(0 => 'No', 1 => 'Yes (recommended)'), 'smalldesc' => 'Do you want to load the security token ? this is used to verify that form code has not been changed by the user before submission, you need to have the "Check Token" action to check it.')); ?>
	<?php echo $HtmlHelper->input('action_show_html_{n}_keep_alive_config', array('type' => 'select', 'label' => 'Load Keep Alive', 'options' => array(0 => 'No', 1 => 'Yes'), 'smalldesc' => 'Load the keep alive element ? this will ensure that the user session does not expire while having the form opened.')); ?>
	<?php echo $HtmlHelper->input('action_show_html_{n}_curly_replacer_config', array('type' => 'select', 'label' => 'Curly brackets replacer', 'options' => array(0 => 'No', 1 => 'Yes'), 'smalldesc' => 'Replace any fields names inside curly brackets, this will work only if you have some data in the $form->data array.')); ?>	
</div>