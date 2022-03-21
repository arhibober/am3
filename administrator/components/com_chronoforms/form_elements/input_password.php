<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class ChronoFormsInputPassword{
	function load($clear){
		if($clear){
			$element_params = array(
								'label_for' => 'input_password_{n}',
								'label_id' => 'input_password_{n}_label',
								'label_text' => 'Enter password',
								'hide_label' => '0',
								'input_name' => 'input_password_{n}',
								'input_id' => '',
								'input_value' => '',
								'input_class' => '',
								'input_title' => '',
								'input_maxlength' => '150',
								'validations' => '',
								'instructions' => '',
								'tooltip' => '',
								'input_size' => '30');
		}
		return array('element_params' => $element_params);
	}
}
?>