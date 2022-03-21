<div class="dragable" id="input_radio">radio Box</div>
<div class="element_code" id="input_radio_element">
	<label id="input_radio_{n}_label" class="text_label"><?php echo $element_params['label_text']; ?></label>
	<?php //echo $form->radio('Chronoforms.'.$element_params['input_name'], $element_params['options'], array('value' => $element_params['input_value'], 'legend' => $element_params['legend'])); ?>
	<span class="temp_div_container">
		<?php
		$temp_options = explode("\n", $element_params['options']);
		$element_params['options'] = array();
		foreach($temp_options as $temp_option){
			$temp_option_details = explode('=', $temp_option);
			$element_params['options'][strval($temp_option_details[0])] = trim($temp_option_details[1]);
		}
		?>
		<?php foreach($element_params['options'] as $radio_option_value => $radio_option_text): ?>
		<input type="radio" name="radio_{n}_input_name" id="radio_{n}_input_id" />
		<label for="<?php echo $element_params['input_id'].$radio_option_value; ?>"><?php echo $radio_option_text; ?></label>
		<?php endforeach; ?>
	</span>
	<?php 
	$options = '';
	foreach($element_params['options'] as $radio_option_value => $radio_option_text){
		if(!empty($options)){
			$options .= "\n";
		}
		$options .= $radio_option_value.'='.$radio_option_text;
	}
	?>
	<input type="hidden" name="chronofield[{n}][input_radio_{n}_label_text]" id="input_radio_{n}_label_text" value="<?php echo $element_params['label_text']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_radio_{n}_input_name]" id="input_radio_{n}_input_name" value="<?php echo $element_params['input_name']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_radio_{n}_input_id]" id="input_radio_{n}_input_id" value="<?php echo $element_params['input_id']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_radio_{n}_input_title]" id="input_radio_{n}_input_title" value="<?php echo $element_params['input_title']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_radio_{n}_input_value]" id="input_radio_{n}_input_value" value="<?php echo $element_params['input_value']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_radio_{n}_legend]" id="input_radio_{n}_legend" value="<?php echo $element_params['legend']; ?>" />
    <textarea name="chronofield[{n}][input_radio_{n}_options]" id="input_radio_{n}_options" style="display:none"><?php echo $options; ?></textarea>
    <textarea name="chronofield[{n}][input_radio_{n}_validations]" id="input_radio_{n}_validations" style="display:none"><?php echo $element_params['validations']; ?></textarea>
    <textarea name="chronofield[{n}][input_radio_{n}_instructions]" id="input_radio_{n}_instructions" style="display:none"><?php echo $element_params['instructions']; ?></textarea>
    <textarea name="chronofield[{n}][input_radio_{n}_tooltip]" id="input_radio_{n}_tooltip" style="display:none"><?php echo $element_params['tooltip']; ?></textarea>
    <input type="hidden" id="chronofield_id_{n}" name="chronofield_id" value="{n}" />
    <input type="hidden" name="chronofield[{n}][tag]" value="input" />
    <input type="hidden" name="chronofield[{n}][type]" value="radio" />
</div>
<div class="element_config" id="input_radio_element_config">
	<?php echo $PluginTabsHelper->Header(array('general' => 'General', 'validation' => 'Validation'), 'input_radio_element_config_{n}'); ?>
	<?php echo $PluginTabsHelper->tabStart('general'); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_input_name_config', array('type' => 'text', 'label' => 'Field Name', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_input_id_config', array('type' => 'text', 'label' => 'Field ID', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_input_title_config', array('type' => 'text', 'label' => 'Field title', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_input_value_config', array('type' => 'text', 'label' => 'Field Default Value', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_label_text_config', array('type' => 'text', 'label' => 'Label Text', 'class' => 'small_input', 'value' => '')); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_options_config', array('type' => 'textarea', 'label' => 'Options', 'rows' => 5, 'cols' => 50, 'operation' => "multi_option", 'operation_fieldtype' => "radio", 'smalldesc' => 'in value=text multi line format.')); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_instructions_config', array('type' => 'textarea', 'label' => 'Instructions for users', 'rows' => 5, 'cols' => 50)); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_tooltip_config', array('type' => 'textarea', 'label' => 'Tooltip', 'rows' => 5, 'cols' => 50)); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
	<?php echo $PluginTabsHelper->tabStart('validation'); ?>
		<?php echo $HtmlHelper->input('input_radio_{n}_validations_config', array('type' => 'checkbox', 'label' => 'Required', 'class' => 'small_input', 'value' => 'required', 'rule' => "split", 'splitter' => ",")); ?>
	<?php echo $PluginTabsHelper->tabEnd(); ?>
</div>