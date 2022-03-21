<div class="dragable" id="input_submit">Submit Button</div>
<div class="element_code" id="input_submit_element">
	<input type="button" name="submit_{n}_input_name" id="submit_{n}_input_id" value="<?php echo $element_params['input_value']; ?>" />
	<input type="hidden" name="chronofield[{n}][input_submit_{n}_input_name]" id="input_submit_{n}_input_name" value="<?php echo $element_params['input_name']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_submit_{n}_input_id]" id="input_submit_{n}_input_id" value="<?php echo $element_params['input_id']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_submit_{n}_input_value]" id="input_submit_{n}_input_value" value="<?php echo $element_params['input_value']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_submit_{n}_input_class]" id="input_submit_{n}_input_class" value="<?php echo $element_params['input_class']; ?>" />
    <input type="hidden" id="chronofield_id_{n}" name="chronofield_id" value="{n}" />
    <input type="hidden" name="chronofield[{n}][tag]" value="input" />
    <input type="hidden" name="chronofield[{n}][type]" value="submit" />
</div>
<div class="element_config" id="input_submit_element_config">
	<?php echo $HtmlHelper->input('input_submit_{n}_input_name_config', array('type' => 'text', 'label' => 'Name', 'class' => 'small_input', 'value' => '')); ?>
	<?php echo $HtmlHelper->input('input_submit_{n}_input_id_config', array('type' => 'text', 'label' => 'ID', 'class' => 'small_input', 'value' => '')); ?>
	<?php echo $HtmlHelper->input('input_submit_{n}_input_value_config', array('type' => 'text', 'label' => 'Text', 'class' => 'small_input', 'value' => '')); ?>
	<?php echo $HtmlHelper->input('input_submit_{n}_input_class_config', array('type' => 'text', 'label' => 'Class', 'class' => 'small_input', 'value' => '')); ?>
</div>