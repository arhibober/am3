<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class ChronoFormsInputSelect{
	function load($clear){
		if($clear){
			$element_params = array(
								'label_for' => 'input_select_{n}',
								'label_id' => 'input_select_{n}_label',
								'label_text' => 'Label Text',
								'hide_label' => '0',
								'input_name' => 'input_select_{n}',
								'input_id' => '',
								'input_class' => '',
								'input_title' => '',
								'selected' => '',
								'options' => 'No=No'."\n".'Yes=Yes',
								'showempty' => '',
								'multiple' => '0',
								'size' => '1',
								'validations' => '',
								'tooltip' => '',
								'instructions' => '');
		}
		return array('element_params' => $element_params);
	}
}
?>