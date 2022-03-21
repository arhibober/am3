<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class ChronoFormsInputText{
	function load($clear){
		if($clear){
			$element_params = array(
								'label_for' => 'input_text_{n}',
								'label_id' => 'input_text_{n}_label',
								'label_text' => 'Label Text',
								'hide_label' => '0',
								'input_name' => 'input_text_{n}',
								'input_id' => '',
								'input_value' => '',
								'input_maxlength' => '150',
								'input_class' => '',
								'input_title' => '',
								'validations' => '',
								'instructions' => '',
								'tooltip' => '',
								'input_size' => '30');
		}
		return array('element_params' => $element_params);
	}
}
?>