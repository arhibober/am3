<div class="dragable" id="input_hidden">hidden Box</div>
<div class="element_code" id="input_hidden_element">
	<label for="<?php echo $element_params['input_id']; ?>" id="input_hidden_{n}_label" class="hidden_label"><?php echo 'Hidden field here'; ?></label>
	<input type="hidden" name="hidden_{n}_input_name" id="hidden_{n}_input_id" value="<?php echo $element_params['input_value']; ?>" />
	<input type="hidden" name="chronofield[{n}][input_hidden_{n}_input_name]" id="input_hidden_{n}_input_name" value="<?php echo $element_params['input_name']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_hidden_{n}_input_id]" id="input_hidden_{n}_input_id" value="<?php echo $element_params['input_id']; ?>" />
    <input type="hidden" name="chronofield[{n}][input_hidden_{n}_input_value]" id="input_hidden_{n}_input_value" value="<?php echo $element_params['input_value']; ?>" />
    <input type="hidden" id="chronofield_id_{n}" name="chronofield_id" value="{n}" />
    <input type="hidden" name="chronofield[{n}][tag]" value="input" />
    <input type="hidden" name="chronofield[{n}][type]" value="hidden" />
</div>
<div class="element_config" id="input_hidden_element_config">
	<?php echo $HtmlHelper->input('input_hidden_{n}_input_name_config', array('type' => 'text', 'label' => 'Field Name', 'class' => 'small_input', 'value' => '')); ?>
	<?php echo $HtmlHelper->input('input_hidden_{n}_input_id_config', array('type' => 'text', 'label' => 'Field id', 'class' => 'small_input', 'value' => '')); ?>
	<?php echo $HtmlHelper->input('input_hidden_{n}_input_value_config', array('type' => 'text', 'label' => 'Field default value', 'class' => 'small_input', 'value' => '')); ?>
</div>