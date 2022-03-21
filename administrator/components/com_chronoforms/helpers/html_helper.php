<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class HtmlHelper {
	var $data;
	function __construct(){
		
	}
	function input($fieldname = '', $fieldoptions = array()){
		$output = '';
		$tag = array();
		if(!isset($fieldoptions['type'])){
			$fieldoptions['type'] = 'text';
			$fieldoptions['tag'] = 'input';			
		}
		$tag['type'] = $fieldoptions['type'];
		$tag['name'] = $fieldname;
		
		//prepare validation classes
    	if(isset($fieldoptions['validations']) && !empty($fieldoptions['validations'])){
    		$validation_classes = explode(",", $fieldoptions['validations']);
    		$validation_classes = array_map('trim', $validation_classes);
    		if($fieldoptions['type'] == 'checkbox_group'){
    			//$validation_classes = array('%checkSelection');
    		}
    		$field_class = array();
    		if(isset($fieldoptions['class'])){
    			$field_class = array($fieldoptions['class']);
    		}
    		$field_class[] = "validate['".implode("','", $validation_classes)."']";
			$fieldoptions['class'] = implode(' ', $field_class);
			unset($fieldoptions['validations']);
		}else{
			unset($fieldoptions['validations']);
		}
		//set id if not set
		if(!isset($fieldoptions['id'])){
			$fieldoptions['id'] = $this->slug($fieldname);
		}
		//reset id if empty
		if(isset($fieldoptions['id']) && !strlen(trim($fieldoptions['id']))){
			unset($fieldoptions['id']);
		}
		//apply the label class
		if(isset($fieldoptions['label']) && !is_array($fieldoptions['label']) && ($fieldoptions['label'] !== false)){
			$fieldoptions['label'] = array('text' => $fieldoptions['label']);
		}
		if(!isset($fieldoptions['label'])){
			$fieldoptions['label'] = false;
		}
		//check if radio to add the div
    	/*if(isset($fieldoptions['type']) && ($fieldoptions['type'] == 'radio') && isset($fieldoptions['label']['text']) && strlen($fieldoptions['label']['text'])){
			$fieldoptions['before'] = '<lable class="ccms_form_label">'.$fieldoptions['label']['text'].'</label>';
    		if(!isset($fieldoptions['legend'])){
    			$fieldoptions['legend'] = false;
    		}
		}*/
		//prepare the tooltips
    	if(isset($fieldoptions['tooltip'])){
    		if(strlen(trim($fieldoptions['tooltip']))){
	    		if(!isset($fieldoptions['after'])){
	    			$fieldoptions['after'] = '';
	    		}
	    		//$tiplink = '<a href="#" title="'.$fieldoptions['label']['text'].'::'.$fieldoptions['tooltip'].'">?</a>';
				$tiplink = '<a href="#">?</a>';
				$fieldoptions['after'] .= '<div title="'.$fieldoptions['label']['text'].'" rel="'.nl2br($fieldoptions['tooltip']).'" class="tooltipimg">'.$tiplink.'</div>';
    		}
			unset($fieldoptions['tooltip']);
		}
    	//prepare the descriptions
    	if(isset($fieldoptions['smalldesc'])){
    		if(strlen(trim($fieldoptions['smalldesc']))){
	    		if(!isset($fieldoptions['after'])){
	    			$fieldoptions['after'] = '';
	    		}
				$fieldoptions['after'] .= '<div class="small-message">'.nl2br($fieldoptions['smalldesc']).'</div>';
    		}
			unset($fieldoptions['smalldesc']);
		}
		//add clear div
		if(!isset($fieldoptions['after'])){
    		$fieldoptions['after'] = '';
    	}
		$fieldoptions['after'] .= '<div class="clear"></div>';
		$fieldoptions['after'] .= '<div id="error-message-'.str_replace('[]', '', $fieldname).'"></div>';
		//set container div options
    	if(!isset($fieldoptions['type'])){
			$fieldoptions['type'] = 'text';
		}
		$div_class = array();
		$div_class[] = 'ccms_form_element';
		$etype = $fieldoptions['type'];
		if(isset($fieldoptions['multiple']) && $fieldoptions['multiple'] == 'checkbox'){
			$etype = 'checkboxgroup';
		}
		$div_class[] = 'cfdiv_'.$etype;
		if(isset($fieldoptions['label_over']) && $fieldoptions['label_over'] == true){
			$div_class[] = 'label_over';
			unset($fieldoptions['label_over']);
		}
    	if(isset($fieldoptions['radios_over']) && $fieldoptions['radios_over'] == true){
			$div_class[] = 'radios_over';
			unset($fieldoptions['radios_over']);
		}
		if(!isset($fieldoptions['div'])){
			$fieldoptions['div'] = array();
		}
		$div_prefix = $fieldoptions['label']['text'] ? $this->slug($fieldoptions['label']['text']) : $fieldname;
		$divContainer = array_merge($fieldoptions['div'], array('id' => $this->slug($div_prefix).'_container_div', 'class' => implode(' ', $div_class)));
		unset($fieldoptions['div']);
		//add before prefix
		$beforeOutput = '';
		if(isset($fieldoptions['before'])){
    		$beforeOutput = $fieldoptions['before'];
			unset($fieldoptions['before']);
    	}
		//add after prefix
		$afterOutput = '';
		if(isset($fieldoptions['after'])){
    		$afterOutput = $fieldoptions['after'];
			unset($fieldoptions['after']);
    	}
		
		if(isset($fieldoptions['default']) && (!isset($fieldoptions['value']) || empty($fieldoptions['value']))){
			$field_value = $fieldoptions['default'];
			unset($fieldoptions['default']);
		}
		
		//check form data
		if(isset($this->data) && !empty($this->data)){
			if(is_array($this->data)){
				if(isset($this->data[$fieldname])){
					$field_value = $this->data[$fieldname];
				}
			}else if(is_object($this->data)){
				if(isset($this->data->$fieldname)){
					$field_value = $this->data->$fieldname;
				}
			}
		}
		
		if(!isset($fieldoptions['value']) && isset($field_value)){
			$fieldoptions['value'] = $field_value;
		}
		
		//merge tag with fieldoptions
		$tag = array_merge($fieldoptions, $tag);
		if(isset($tag['label'])){
			unset($tag['label']);
		}
		//print_r2($fieldoptions);
		switch($fieldoptions['type']){			
			case 'submit':
				unset($fieldoptions['value']);
				$output .= '<input';
				foreach($tag as $k => $v){
					$output .= ' '.$k.'="'.$v.'"';
				}
				$output .= ' />'."\n";
				unset($fieldoptions['label']);
				break;
			case 'textarea':
				$output .= '<textarea';
				$value = '';
				if(isset($tag['value'])){
					$value = $tag['value'];
					unset($tag['value']);
				}
				foreach($tag as $k => $v){
					$output .= ' '.$k.'="'.$v.'"';
				}
				$output .= '>'.$value.'</textarea>'."\n";
				break;
			case 'select':
				if(isset($fieldoptions['value']) && !empty($fieldoptions['value'])){
					$tag['selected'] = $fieldoptions['value'];
				}
				$output .= '<select';
				$options = array();
				if(isset($tag['options']) && is_array($tag['options'])){
					$options = $tag['options'];
					unset($tag['options']);
					$selected = false;
					if(isset($tag['selected'])){
						$selected = $tag['selected'];
						unset($tag['selected']);
					}
				}
				$empty = false;
				if(isset($tag['empty'])){
					if(!empty($tag['empty']))
					$empty = '<option value="">'.$tag['empty'].'</option>'."\n";
					unset($tag['empty']);
				}
				if(isset($tag['multiple']) && $tag['multiple'] == 1){
					$tag['multiple'] = 'multiple';
					if(strpos($tag['name'], '[]') === false){
						$tag['name'] = $tag['name'].'[]';
					}
				}
				foreach($tag as $k => $v){
					$output .= ' '.$k.'="'.$v.'"';
				}
				$output .= '>'."\n";
				if($empty){
					$output .= $empty;
				}
				foreach($options as $k => $option){
					$output .= '<option value="'.$k.'"'.($selected == $k ? ' selected' : '').'>'.$option.'</option>'."\n";
				}
				$output .= '</select>'."\n";
				break;
			case 'radio':
				unset($fieldoptions['value']);
				$options = array();
				if(isset($tag['options']) && is_array($tag['options'])){
					$options = $tag['options'];
					unset($tag['options']);
				}
				foreach($options as $k => $option){
					$output .= '<input type="'.$fieldoptions['type'].'" name="'.$fieldname.'" id="'.$this->slug($fieldname.'-'.$k).'" title="'.$tag['title'].'" value="'.$k.'" class="'.$tag['class'].'">'."\n";
					$output .= '<label for="'.$this->slug($fieldname.'-'.$k).'">'.$option.'</label>'."\n";
				}
				break;
			case 'checkbox_group':
				unset($fieldoptions['value']);
				$fieldoptions['type'] = 'checkbox';
				$options = array();
				if(isset($tag['options']) && is_array($tag['options'])){
					$options = $tag['options'];
					unset($tag['options']);
					$checked = false;
					if(isset($tag['checked'])){
						$checked = $tag['checked'];
						unset($tag['checked']);
					}
				}
				foreach($options as $k => $option){
					$output .= '<input type="'.$fieldoptions['type'].'" name="'.$fieldname.'[]" id="'.$this->slug($fieldname.'-'.$k).'" title="'.$tag['title'].'" value="'.$k.'"'.($checked == $k ? ' checked="checked"' : '').' class="'.$tag['class'].'">'."\n";
					$output .= '<label for="'.$this->slug($fieldname.'-'.$k).'">'.$option.'</label>'."\n";
				}
				break;
			case 'checkbox':
				if(isset($tag['checked'])){
					if((bool)$tag['checked'] === true){
						$tag['checked'] = 'checked';
					}else{
						unset($tag['checked']);
					}
				}
				$full_label = false;
				if(isset($tag['label_position']) && !empty($tag['label_position'])){
					if($tag['label_position'] == 'right'){
						$full_label = true;
					}
					$tag['class'] = strlen($tag['class']) ? $tag['class'].' label_'.$tag['label_position'] : 'label_'.$tag['label_position'];
				}
				unset($tag['label_position']);
				
				$output .= '<input';
				foreach($tag as $k => $v){
					$output .= ' '.$k.'="'.$v.'"';
				}
				$output .= ' />'."\n";
				if(isset($fieldoptions['label']) && $fieldoptions['label'] !== false){
					$class = '';
					if($full_label){
						$class = ' class="full_label"';
					}
					$afterOutput = '<label'.(isset($fieldoptions['id']) ? ' for="'.$fieldoptions['id'].'"' : '').$class.'>'.$fieldoptions['label']['text'].'</label>'.$afterOutput;
					unset($fieldoptions['label']);
				}
				break;
			case 'hidden':
				$output .= '<input';
				foreach($tag as $k => $v){
					$output .= ' '.$k.'="'.$v.'"';
				}
				$output .= ' />'."\n";
				$divContainer = '';
				break;
			case 'datetime':
				if(isset($tag['timeonly']) && (int)$tag['timeonly'] == 1){
					$tag['class'] = empty($tag['class']) ? 'cf_time_picker' : $tag['class'].' cf_time_picker';
				}
				if(isset($tag['addtime']) && (int)$tag['addtime'] == 1 && (int)$tag['timeonly'] == 0){
					$tag['class'] = empty($tag['class']) ? 'cf_datetime_picker' : $tag['class'].' cf_datetime_picker';
				}
				if((int)$tag['timeonly'] == 0 && (int)$tag['addtime'] == 0){
					$tag['class'] = empty($tag['class']) ? 'cf_date_picker' : $tag['class'].' cf_date_picker';
				}
				if(isset($tag['timeonly']))unset($tag['timeonly']);
				if(isset($tag['addtime']))unset($tag['addtime']);
				unset($tag['tag']);
				$tag['type'] = 'text';
				$output .= '<input';
				foreach($tag as $k => $v){
					$output .= ' '.$k.'="'.$v.'"';
				}
				$output .= ' />'."\n";
				break;
			case 'custom':
				$output = $tag['code'];
				if((int)$tag['clean'] == 1){
					$divContainer = '';
					$beforeOutput = '';
					$afterOutput = '';
				}
				break;
			case 'text':
			case 'password':			
			case 'file':
			default:
				$output .= '<input';
				foreach($tag as $k => $v){
					$output .= ' '.$k.'="'.$v.'"';
				}
				$output .= ' />'."\n";
				break;
		}
		
		if(isset($fieldoptions['label']) && $fieldoptions['label'] !== false){
			$beforeOutput .= '<label'.(isset($fieldoptions['id']) ? ' for="'.$fieldoptions['id'].'"' : '').'>'.$fieldoptions['label']['text'].'</label>';
			unset($fieldoptions['label']);
		}
		
		if(!empty($divContainer)){
			$output = '<div class="'.$divContainer['class'].'" id="'.$divContainer['id'].'">'.$beforeOutput.$output.$afterOutput.'</div>';
		}
		
		return $output;
	}
	
	function slug($str, $replacer = "_"){
		$str = strtolower(trim($str));
		$str = preg_replace('/[^a-z0-9{}'.$replacer.']/', $replacer, $str);
		$str = preg_replace('/'.$replacer.'+/', $replacer, $str);
		return $str;
	}
}