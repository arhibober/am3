<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class ChronoFormsInputCheckbox{
	function load($clear){
		if($clear){
			$element_params = array(
								'label_for' => 'input_checkbox_{n}',
								'label_id' => 'input_checkbox_{n}_label',
								'label_text' => 'Label Text',
								'label_position' => 'left',
								'hide_label' => '0',
								'input_name' => 'input_checkbox_{n}',
								'input_id' => '',
								'input_title' => '',
								'input_value' => '1',
								'checked' => '0',
								'validations' => '',
								'tooltip' => '',
								'instructions' => '');
		}
		return array('element_params' => $element_params);
	}
}
?>