<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldVFText extends JFormField
{
	protected $type = 'VFText';

	protected function getInput()
	{
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Now $this->value contains certainly a value, but it may be
		// 1) the default value (usually in uppercase) to be translated or
		// 2) the value previously filled and saved by the user
		// If we are in the first case, we have to translate the string to obtain the correct value
		if ($this->value == (string)$this->element['default']) $this->value = JText::_($this->element['default']);

		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
				$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';
	}
}
