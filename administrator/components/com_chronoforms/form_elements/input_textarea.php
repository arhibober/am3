<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class ChronoFormsInputTextarea{
	function load($clear){
		if($clear){
			$element_params = array(
								'label_for' => 'input_textarea_{n}',
								'label_id' => 'input_textarea_{n}_label',
								'label_text' => 'Label Text',
								'hide_label' => '0',
								'input_name' => 'input_textarea_{n}',
								'input_id' => '',
								'input_value' => '',
								'input_class' => '',
								'input_title' => '',
								'validations' => '',
								'instructions' => '',
								'input_rows' => '12',
								'tooltip' => '',
								'input_cols' => '45');
		}
		return array('element_params' => $element_params);
	}
}
?>