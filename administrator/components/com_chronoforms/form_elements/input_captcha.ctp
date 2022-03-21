<div class="dragable" id="input_captcha">Captcha Input</div>
<div class="element_code" id="input_captcha_element">
	<label for="<?php echo $element_params['input_id']; ?>" id="input_captcha_{n}_label" class="text_label"><?php echo $element_params['label_text']; ?></label>
	<input type="text" name="captcha_{n}_input_name" id="captcha_{n}_input_id" value="<?php echo $element_params['input_value']; ?>" maxlength="<?php echo $element_params['input_maxlength']; ?>" size="<?php echo $element_params['input_size']; ?>" />
	<input type="hidden" name="chronofield[{n}][input_captcha_{n}_label_text]" id="input_captcha_{n}_label_text" value="<?php echo $element_params['label_text']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_captcha_{n}_input_name]" id="input_captcha_{n}_input_name" value="<?php echo $element_params['input_name']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_captcha_{n}_input_id]" id="input_captcha_{n}_input_id" value="<?php echo $element_params['input_id']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_captcha_{n}_input_value]" id="input_captcha_{n}_input_value" value="<?php echo $element_params['input_value']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_captcha_{n}_input_maxlength]" id="input_captcha_{n}_input_maxlength" value="<?php echo $element_params['input_maxlength']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_captcha_{n}_input_size]" id="input_captcha_{n}_input_size" value="<?php echo $element_params['input_size']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_captcha_{n}_input_class]" id="input_captcha_{n}_input_class" value="<?php echo $element_params['input_class']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_captcha_{n}_input_title]" id="input_captcha_{n}_input_title" value="<?php echo $element_params['input_title']; ?>" />
    <textarea name="chronofield[{n}][input_captcha_{n}_validations]" id="input_captcha_{n}_validations" style="display:none"><?php echo $element_params['validations']; ?></textarea>
    <textarea name="chronofield[{n}][input_captcha_{n}_instructions]" id="input_captcha_{n}_instructions" style="display:none"><?php echo $element_params['instructions']; ?></textarea>
    <textarea name="chronofield[{n}][input_captcha_{n}_tooltip]" id="input_captcha_{n}_tooltip" style="display:none"><?php echo $element_params['tooltip']; ?></textarea>
    <input type="hidden" id="chronofield_id_{n}" name="chronofield_id" value="{n}" />
    <input type="hidden" name="chronofield[{n}][tag]" value="input" />
    <input type="hidden" name="chronofield[{n}][type]" value="captcha" />
	<input type="hidden" name="chronofield[{n}][real_type]" value="text" />
	<input type="hidden" name="chronofield[{n}][after]" value="{chronocaptcha_img}" />
</div>
<div class="element_config" id="input_captcha_element_config">
	<?php echo $PluginTabsHelper->Header(array('general' => 'General', 'validation' => 'Validation'), 'input_captcha_element_config_{n}'); ?>
	<?php echo $PluginTabsHelper->tabStart('general'); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_input_name_config', array('type' => 'text', 'label' => 'Field Name', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_input_id_config', array('type' => 'text', 'label' => 'Field ID', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_input_value_config', array('type' => 'text', 'label' => 'Field Default Value', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_input_maxlength_config', array('type' => 'text', 'label' => 'Field Max Length', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_input_size_config', array('type' => 'text', 'label' => 'Field Size', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_input_class_config', array('type' => 'text', 'label' => 'Field Class', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_input_title_config', array('type' => 'text', 'label' => 'Field title', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_label_text_config', array('type' => 'text', 'label' => 'Label Text', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_instructions_config', array('type' => 'textarea', 'label' => 'Instructions for users', 'rows' => 5, 'cols' => 50)); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_tooltip_config', array('type' => 'textarea', 'label' => 'Tooltip', 'rows' => 5, 'cols' => 50)); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('validation'); ?>
		<?php echo $HtmlHelper->input('input_captcha_{n}_validations_config', array('type' => 'checkbox', 'label' => 'Required', 'class' => 'small_input', 'value' => 'required', 'rule' => "split", 'splitter' => ",")); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
</div>