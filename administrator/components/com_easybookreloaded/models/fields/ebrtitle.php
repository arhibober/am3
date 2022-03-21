<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */
 
defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldEBRTitle extends JFormField
{
	protected $type = 'EBRTitle';
	
	protected function getInput() 
	{
		return '';
	}
	
	protected function getLabel() 
	{
		// Label vergrößern
		JHTML::stylesheet('administrator/components/com_easybookreloaded/css/easybookreloaded.css');
		echo '<div class="clr"></div>';
		
		if ($this->element['default']) 
		{
			return '<div style="padding: 5px 5px 5px 0; font-size: 16px;"><strong>'. JText::_($this->element['default']) . '</strong></div>';
		} 
		else 
		{
			return parent::getLabel();
		}
		echo '<div class="clr"></div>';
	}
}
?>