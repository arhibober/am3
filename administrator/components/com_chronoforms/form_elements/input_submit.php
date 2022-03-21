<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class ChronoFormsInputSubmit{
	function load($clear){
		if($clear){
			$element_params = array(
								'input_name' => 'input_submit_{n}',
								'input_id' => '',
								'input_class' => '',
								'input_value' => 'Submit');
		}
		return array('element_params' => $element_params);
	}
}
?>