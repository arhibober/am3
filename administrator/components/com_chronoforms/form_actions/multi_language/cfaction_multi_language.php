<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionMultiLanguageHelper{
	function apply($form, $actiondata, $output = ''){
		$params = new JParameter($actiondata->params);
		$lang = JFactory::getLanguage();
		if($lang->_lang == $params->get('lang_tag', '')){
			$lang_strings = explode("\n", $actiondata->content1);
			foreach($lang_strings as $lang_string){
				if(!empty($lang_string) && strpos($lang_string, "=") !== false){
					$texts = explode("=", $lang_string);
					if(empty($output)){
						$form->form_output = str_replace($texts[0], $texts[1], $form->form_output);
					}else{
						$output = str_replace($texts[0], $texts[1], $output);
					}
				}
			}
		}
		
		if(!empty($output)){
			return $output;
		}
    }
}
?>