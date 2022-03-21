<div class="dragable" id="input_textarea">Textarea</div>
<div class="element_code" id="input_textarea_element">
	<label for="<?php echo $element_params['input_id']; ?>" id="input_textarea_{n}_label" class="textarea_label"><?php echo $element_params['label_text']; ?></label>
	<textarea name="textarea_{n}_input_name" id="textarea_{n}_input_id"><?php echo $element_params['input_value']; ?></textarea>
	<input type="hidden" name="chronofield[{n}][input_textarea_{n}_label_text]" id="input_textarea_{n}_label_text" value="<?php echo $element_params['label_text']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_textarea_{n}_input_name]" id="input_textarea_{n}_input_name" value="<?php echo $element_params['input_name']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_textarea_{n}_input_id]" id="input_textarea_{n}_input_id" value="<?php echo $element_params['input_id']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_textarea_{n}_input_value]" id="input_textarea_{n}_input_value" value="<?php echo $element_params['input_value']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_textarea_{n}_input_cols]" id="input_textarea_{n}_input_cols" value="<?php echo $element_params['input_cols']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_textarea_{n}_input_rows]" id="input_textarea_{n}_input_rows" value="<?php echo $element_params['input_rows']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_textarea_{n}_input_class]" id="input_textarea_{n}_input_class" value="<?php echo $element_params['input_class']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_textarea_{n}_input_title]" id="input_textarea_{n}_input_title" value="<?php echo $element_params['input_title']; ?>" />
    <textarea name="chronofield[{n}][input_textarea_{n}_validations]" id="input_textarea_{n}_validations" style="display:none"><?php echo $element_params['validations']; ?></textarea>
    <textarea name="chronofield[{n}][input_textarea_{n}_instructions]" id="input_textarea_{n}_instructions" style="display:none"><?php echo $element_params['instructions']; ?></textarea>
    <textarea name="chronofield[{n}][input_textarea_{n}_tooltip]" id="input_textarea_{n}_tooltip" style="display:none"><?php echo $element_params['tooltip']; ?></textarea>
    <input type="hidden" id="chronofield_id_{n}" name="chronofield_id" value="{n}" />
    <input type="hidden" name="chronofield[{n}][tag]" value="input" />
    <input type="hidden" name="chronofield[{n}][type]" value="textarea" />
</div>
<div class="element_config" id="input_textarea_element_config">
	<?php echo $PluginTabsHelper->Header(array('general' => 'General', 'validation' => 'Validation'), 'input_textarea_element_config_{n}'); ?>
	<?php echo $PluginTabsHelper->tabStart('general'); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_input_name_config', array('type' => 'text', 'label' => 'Field Name', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_input_id_config', array('type' => 'text', 'label' => 'Field ID', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_input_value_config', array('type' => 'text', 'label' => 'Field Default Value', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_input_cols_config', array('type' => 'text', 'label' => 'Field Columns', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_input_rows_config', array('type' => 'text', 'label' => 'Field Rows', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_input_class_config', array('type' => 'text', 'label' => 'Field Class', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_input_title_config', array('type' => 'text', 'label' => 'Field title', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_label_text_config', array('type' => 'text', 'label' => 'Label Text', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_instructions_config', array('type' => 'textarea', 'label' => 'Instructions for users', 'rows' => 5, 'cols' => 50)); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_tooltip_config', array('type' => 'textarea', 'label' => 'Tooltip', 'rows' => 5, 'cols' => 50)); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('validation'); ?>
		<?php echo $HtmlHelper->input('input_textarea_{n}_validations_config', array('type' => 'checkbox', 'label' => 'Required', 'class' => 'small_input', 'value' => 'required', 'rule' => "split", 'splitter' => ",")); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
</div>