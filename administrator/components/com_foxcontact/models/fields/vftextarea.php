<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldVFTextarea extends JFormField
{
	protected $type = 'VFTextarea';

	protected function getInput()
	{
		// Initialize some field attributes.
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$columns	= $this->element['cols'] ? ' cols="'.(int) $this->element['cols'].'"' : '';
		$rows		= $this->element['rows'] ? ' rows="'.(int) $this->element['rows'].'"' : '';

		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Now $this->value contains certainly a value, but it may be
		// 1) the default value (usually in uppercase) to be translated or
		// 2) the value previously filled and saved by the user
		// If we are in the first case, we have to translate the string to obtain the correct value
		if ($this->value == (string)$this->element['default']) $this->value = JText::_($this->element['default']);

		return '<textarea name="'.$this->name.'" id="'.$this->id.'"' .
				$columns.$rows.$class.$disabled.$onchange.'>' .
				htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') .
				'</textarea>';
	}
}
