<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionShowHtmlHelper{
	function loadAction($form, $actiondata){
		$params = new JParameter($actiondata->params);
		$output = '';
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
		$document = JFactory::getDocument();
		if((int)$form->form_params->get('load_files', 1) > 0){
			//load form css files
			$CF_PATH = ($mainframe->isSite()) ? JURI::Base() : $uri->root();
			$document->addStyleSheet($CF_PATH.'components/com_chronoforms/css/frontforms.css');
		}
		if(($form->form_params->get('enable_jsvalidation', 1) || (strpos($form->form_details->content, 'validate[') !== false && $form->form_params->get('auto_detect_settings', 1))) && (int)$form->form_params->get('load_files', 1) > 0){
			//load js validation code
			$this->_loadValidationScripts($form);
		}
		$datetime_picker_selector = $form->form_params->get('datepicker_config', '');
		if((!empty($datetime_picker_selector) || strpos($form->form_details->content, 'cf_date_picker') !== false || strpos($form->form_details->content, 'cf_time_picker') !== false || strpos($form->form_details->content, 'cf_datetime_picker') !== false) && (int)$form->form_params->get('load_files', 1) > 0){
			//load js for the date time picker
			$this->_loadDatePickerScripts($form);
		}
		
		if((strpos($form->form_details->content, 'tooltipimg') !== false) && (int)$form->form_params->get('load_files', 1) > 0){
			//load the tooltips files
			$this->_loadToolTip($form);
		}
		
		if(!empty($form->validation_errors) && (int)$form->form_params->get('load_files', 1) > 0){
			//add the validation style
			$this->_loadSSValidation($form);
		}
		
		//check if form tags should be added or not
		if($form->form_params->get('add_form_tags', 1)){
			$output .= "<form ";
			$form_tag_array = array();
			if(trim($form->form_params->get('action_url', ''))){
				$form_tag_array[] = 'action="'.$form->form_params->get('action_url', '').'"';
			}else{
				$action_url = 'index.php?option=com_chronoforms&amp;chronoform='.$form->form_name;
				if($form->form_params->get('relative_url', 1) == 1){
					$action_url = $this->selfURL();
					preg_match_all('/(&*)event=([^&]+)/is', $action_url, $event_matches);
					$action_url = str_replace($event_matches[0], '', $action_url);
				}
				if($form->form_params->get('submit_action', 'submit') != 'self'){
					$separator = '&amp;';
					if(strpos($action_url, '?') === false){
						$separator = '?';
					}
					$action_url .= $separator.'event='.$form->form_params->get('submit_action', 'submit');
				}				
				//attach the itemid if exists
				$item_id = JRequest::getVar('Itemid', '');
				if(!empty($item_id) && strpos($action_url, 'Itemid') === false){
					$separator = '&amp;';
					if(strpos($action_url, '?') === false){
						$separator = '?';
					}
					$action_url .= $separator.'Itemid='.$item_id;
				}
				$form_tag_array[] = 'action="'.$action_url.'"';
			}
			$form_tag_array[] = 'name="'.$form->form_name.'"';
			$form_tag_array[] = 'id="chronoform_'.$form->form_name.'"';
			$enctype = '';
			$method = $form->form_params->get('form_method', 'post');
			if($method == 'file' || ($form->form_params->get('auto_detect_settings', 1) && (stripos($form->form_details->content, 'type="file"') !== false || stripos($form->form_details->content, "type='file'") !== false))){
				$method = 'post';
				$enctype = 'enctype="multipart/form-data"';
			}
			//add the form method
			$form_tag_array[] = 'method="'.$method.'"';
			//add the enctype if exists
			$form_tag_array[] = $enctype;
			//add the form class
			$form_tag_array[] = 'class="Chronoform"';
			//add any attachments
			if(trim($form->form_params->get('form_tag_attach', ''))){
				$form_tag_array[] = $form->form_params->get('form_tag_attach', '');
			}
			//build the form tag
			$output .= implode(" ", array_filter($form_tag_array)).'>';
			//$output .= $this->Form->create('ChronoformData', array('url' => $action_url, 'id' => 'ChronoForm_'.$formdetails['Chronoform']['name'], 'type' => 'file', 'class' => 'Chronoform'));
		}
		
		//show error messages
		$this->_showErrors($form);
		//echo $output;
		ob_start();
		eval('?>'.$form->form_details->content);
		$output .= ob_get_clean();
		//Load any form data
		if((int)$params->get('data_republish', 1) == 1){
			include_once(JPATH_SITE.DS.'components'.DS.'com_chronoforms'.DS.'libraries'.DS.'includes'.DS.'data_republish.php');
			$HTMLFormPostDataLoad = new HTMLFormPostDataLoad();
			//$HTMLFormPostDataLoad->validation_errors = $form->validation_errors;
			if(isset($form->data['chrono_verification']) && !empty($form->data['chrono_verification'])){
				$form->data['chrono_verification'] = '';
			}
			$output = $HTMLFormPostDataLoad->load($output, $form->data);
		}
		//Display any form errors
		if((int)$params->get('display_errors', 1) == 1){
			include_once(JPATH_SITE.DS.'components'.DS.'com_chronoforms'.DS.'libraries'.DS.'includes'.DS.'display_errors.php');
			$HTMLFormPostDisplayErrors = new HTMLFormPostDisplayErrors();
			$HTMLFormPostDisplayErrors->validation_errors = $form->validation_errors;
			$output = $HTMLFormPostDisplayErrors->load($output, $form->data);
		}
		//Replace curly fields names
		if((int)$params->get('curly_replacer', 1) == 1){
			$output = $form->curly_replacer($output, $form->data);
		}
		//load token if enabled
		if((int)$params->get('load_token', 1) == 1){
			$output .= JHTML::_('form.token');
		}
		//load keep alive if enabled
		if((int)$params->get('keep_alive', 0) == 1){
			JHTML::_('behavior.keepalive');
		}
		
		//$output = '';
		//check if form tags should be added or not
		if($form->form_params->get('add_form_tags', 1)){
			$output .= "</form>";
		}
		echo $output;
	}
	
	function _showErrors($form){
		if(!empty($form->validation_errors) && $form->form_params->get('show_top_errors', 1)){
			$mainframe = JFactory::getApplication();
			$uri = JFactory::getURI();
			$document =& JFactory::getDocument();
			$CF_PATH = ($mainframe->isSite()) ? JURI::Base() : $uri->root();
			$document->addStyleSheet($CF_PATH.'components/com_chronoforms/css/error.css');
			echo '<span class="cf_alert"><ol><li>'.implode('</li><li>', $this->_normalize($form->validation_errors)).'</li></ol></span>';
		}
	}
	
	function _normalize($array = array()){
		$return = array();
		foreach($array as $k => $v){
			if(is_array($v)){
				$return = array_merge($this->_normalize($v), $return);
			}else{
				$return[] = $v;
			}
		}
		return $return;
	}
	
	
    function _loadValidationScripts($form){
		$document =& JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
		$CF_PATH = ($mainframe->isSite()) ? JURI::Base() : $uri->root();
		$document->addStyleSheet($CF_PATH.'components/com_chronoforms/css/formcheck/theme/'.$form->form_params->get('jsvalidation_theme', 'classic').'/formcheck.css');
		$document->addStyleSheet($CF_PATH.'components/com_chronoforms/css/formcheck_fix.css');
		$document->addScript($CF_PATH.'components/com_chronoforms/js/formcheck/formcheck-yui.js');
		$document->addScript($CF_PATH.'components/com_chronoforms/js/formcheck/formcheck-max.js');
		$document->addScript($CF_PATH.'components/com_chronoforms/js/formcheck/lang/'.$form->form_params->get('jsvalidation_lang', 'en').'.js');
		ob_start();
		?>
		//<![CDATA[
			window.addEvent('domready', function() {
				<?php if($form->form_params->get('jsvalidation_errors', 1)): ?>
				formCheck = new FormCheckMax('<?php echo 'chronoform_'.$form->form_name; ?>', {
				<?php else: ?>
				formCheck = new FormCheck('<?php echo 'chronoform_'.$form->form_name; ?>', {
				<?php endif; ?>
					
				});									
			});
		//]]>
		<?php
		$script = ob_get_clean();
		$document->addScriptDeclaration($script);
    }
	
	function _loadDatePickerScripts($form){
		$document =& JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
		$CF_PATH = ($mainframe->isSite()) ? JURI::Base() : $uri->root();
		$document->addStyleSheet($CF_PATH.'components/com_chronoforms/css/datepicker/datepicker_dashboard.css');
		$document->addScript($CF_PATH.'components/com_chronoforms/js/datepicker/datepicker.js');
		$settings = array(
						"'.cf_date_picker', {pickerClass: 'datepicker_dashboard', inputOutputFormat: 'Y-m-d H:i:s', allowEmpty: true}", 
						"'.cf_datetime_picker', {pickerClass: 'datepicker_dashboard', inputOutputFormat: 'Y-m-d H:i:s', timePicker: true, allowEmpty: true, format: 'd-m-Y H:i:s'}", 
						"'.cf_time_picker', {pickerClass: 'datepicker_dashboard', inputOutputFormat: 'Y-m-d H:i:s', timePickerOnly: true, allowEmpty: true, format: 'd-m-Y H:i:s'}"
						);
		//$selector = 'dateTimePicker';
		$datetime_picker_selectors = $form->form_params->get('datepicker_config', '');
		if(!empty($datetime_picker_selectors)){
			$date_selectors = explode("|#|", $datetime_picker_selectors);
			foreach($date_selectors as $date_selector){
				$selector = "'.$date_selector'";
				$datePickerSettings = "{pickerClass: 'datepicker_dashboard', inputOutputFormat: 'Y-m-d H:i:s'}";
				if(strpos($date_selector, "::") !== false){
					$config = explode("::", $date_selector);
					$selector = "'.$config[0]'";
					$datePickerSettings = $config[1];
					//add the picker class if not set by the user
					if(strpos($datePickerSettings, "pickerClass") === false){
						$datePickerSettings = str_replace("}", ", pickerClass: 'datepicker_dashboard'}", $datePickerSettings);
					}
					//add the value format if not set by the user
					if(strpos($datePickerSettings, "inputOutputFormat") === false){
						$datePickerSettings = str_replace("}", ", inputOutputFormat: 'Y-m-d H:i:s'}", $datePickerSettings);
					}
				}
				$settings[] = $selector.', '.$datePickerSettings;
			}
		}
		ob_start();
		?>
		//<![CDATA[
			window.addEvent('load', function() {
				<?php foreach($settings as $setting): ?>
				new DatePicker(<?php echo $setting; ?>);<?php echo "\n"; ?>
				<?php endforeach; ?>
			});
		//]]>
		<?php
		$script = ob_get_clean();
		$document->addScriptDeclaration($script);
	}
	
	function _loadSSValidation($form){
		$document =& JFactory::getDocument();
		JHTML::_('behavior.mootools');
		ob_start();
		?>
		//<![CDATA[
			window.addEvent('domready', function() {
				$$('.error-message').each(function(element){
					if($chk(element.getParent('.ccms_form_element'))){
						element.getParent('.ccms_form_element').addClass('form-error');
					}
				});									
			});
		//]]>
		<?php
		$script = ob_get_clean();
		$document->addScriptDeclaration($script);
	}
    
    function _loadToolTip(){
		$mainframe = JFactory::getApplication();
		$uri = JFactory::getURI();
    	$document =& JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$CF_PATH = ($mainframe->isSite()) ? JURI::Base() : $uri->root();
		echo '<link href="'.$CF_PATH.'components/com_chronoforms/css/tooltip.css" rel="stylesheet" type="text/css" />';
		//JHTML::_('behavior.tooltip', '.tooltipimg');
		ob_start();
		?>
		//<![CDATA[
			window.addEvent('domready', function(){
				//create the tooltips
				var tipz = new Tips($$('div.tooltipimg'),{
					className: 'tooltipbox',
					fixed: true,
					hideDelay: 0,
					showDelay: 0
				});
			});
		//]]>
		<?php
		$script = ob_get_clean();
		$document->addScriptDeclaration($script);
    }
	
	function selfURL() {
		$uri =& JURI::getInstance();
		$inbetween = '';
		if($uri->getQuery())$inbetween = '?';
		return $uri->current().$inbetween.$uri->getQuery();
	}
}
?>