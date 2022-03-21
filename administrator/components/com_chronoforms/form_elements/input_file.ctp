<div class="dragable" id="input_file">File Upload</div>
<div class="element_code" id="input_file_element">
	<label for="<?php echo $element_params['input_id']; ?>" id="input_file_{n}_label" class="text_label"><?php echo $element_params['label_text']; ?></label>
	<input type="file" name="file_{n}_input_name" id="file_{n}_input_id" />
	<input type="hidden" name="chronofield[{n}][input_file_{n}_label_text]" id="input_file_{n}_label_text" value="<?php echo $element_params['label_text']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_file_{n}_input_name]" id="input_file_{n}_input_name" value="<?php echo $element_params['input_name']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_file_{n}_input_id]" id="input_file_{n}_input_id" value="<?php echo $element_params['input_id']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_file_{n}_input_class]" id="input_file_{n}_input_class" value="<?php echo $element_params['input_class']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_file_{n}_input_title]" id="input_file_{n}_input_title" value="<?php echo $element_params['input_title']; ?>" />
    <textarea name="chronofield[{n}][input_file_{n}_validations]" id="input_file_{n}_validations" style="display:none"><?php echo $element_params['validations']; ?></textarea>
    <textarea name="chronofield[{n}][input_file_{n}_instructions]" id="input_file_{n}_instructions" style="display:none"><?php echo $element_params['instructions']; ?></textarea>
    <textarea name="chronofield[{n}][input_file_{n}_tooltip]" id="input_file_{n}_tooltip" style="display:none"><?php echo $element_params['tooltip']; ?></textarea>
    <input type="hidden" id="chronofield_id_{n}" name="chronofield_id" value="{n}" />
    <input type="hidden" name="chronofield[{n}][tag]" value="input" />
    <input type="hidden" name="chronofield[{n}][type]" value="file" />
</div>
<div class="element_config" id="input_file_element_config">
	<?php echo $PluginTabsHelper->Header(array('general' => 'General', 'validation' => 'Validation'), 'input_file_element_config_{n}'); ?>
	<?php echo $PluginTabsHelper->tabStart('general'); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_input_name_config', array('type' => 'text', 'label' => 'Field Name', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_input_id_config', array('type' => 'text', 'label' => 'Field ID', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_input_class_config', array('type' => 'text', 'label' => 'Field Class', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_input_title_config', array('type' => 'text', 'label' => 'Field title', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_label_text_config', array('type' => 'text', 'label' => 'Label Text', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_instructions_config', array('type' => 'textarea', 'label' => 'Instructions for users', 'rows' => 5, 'cols' => 50)); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_tooltip_config', array('type' => 'textarea', 'label' => 'Tooltip', 'rows' => 5, 'cols' => 50)); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('validation'); ?>
		<?php echo $HtmlHelper->input('input_file_{n}_validations_config', array('type' => 'checkbox', 'label' => 'Required', 'class' => 'small_input', 'value' => 'required', 'rule' => "split", 'splitter' => ",")); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
</div>