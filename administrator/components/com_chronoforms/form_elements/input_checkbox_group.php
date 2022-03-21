<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class ChronoFormsInputCheckboxGroup{
	function load($clear){
		if($clear){
			$element_params = array(
								'label_for' => 'input_checkbox_group_{n}',
								'label_id' => 'input_checkbox_group_{n}_label',
								'label_text' => 'Label Text',
								'hide_label' => '0',
								'input_name' => 'input_checkbox_group_{n}',
								'input_id' => '',
								'input_title' => '',
								'checked' => '',
								'validations' => '',
								'instructions' => '',
								'tooltip' => '',
								'options' => "choice 1=Choice 1\nchoice 2=Choice 2\nchoice 3=Choice 3");
		}
		return array('element_params' => $element_params);
	}
}
?>