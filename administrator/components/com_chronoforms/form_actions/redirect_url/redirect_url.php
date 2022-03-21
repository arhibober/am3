<?php
/**
* CHRONOFORMS version 4.0
* Copyright (c) 2006 - 2011 Chrono_Man, ChronoEngine.com. All rights reserved.
* Author: Chrono_Man (ChronoEngine.com)
* @license		GNU/GPL
* Visit http://www.ChronoEngine.com for regular updates and information.
**/
class CfactionRedirectURL
{
	var $formname;
	var $formid;
	var $group = array('id' => 'curl', 'title' => 'CURL');
	var $details = array('title' => 'ReDirect URL', 'tooltip' => 'Configure a ReDirect URL.');
	
	function run($form, $actiondata)
	{
		$mainframe = JFactory::getApplication();
		$params = new JParameter($actiondata->params);
		$redirect_values = $this->paramsToArray($actiondata->content1);
		
		$redirect_url = JFactory::getURI($params->get('target_url'));
		$query = $redirect_url->getQuery();
		if ( $query ) {
			$temp = explode('&', $query);
			$temp_array = array();
			foreach ( $temp as $v ) {
				$redirect_values = array_merge($this->paramsToArray($v), $redirect_values);
			}
		}
		$query = $redirect_url->buildQuery($redirect_values); 
		$redirect_url->setQuery($query);
		$form->debug['redirect_url'][] = 'redirect_url_target_url: '.$params->get('target_url');
		$form->debug['redirect_url'][] = '$query: '.print_r($query, true);

		//add the response in the form data array
		$form->data['redirect_url'] = $redirect_url->toString();
	}
	
	function load($clear)
	{
		if ( $clear ) {
			$action_params = array(
				'target_url' => 'http://',
				'content1' => ''
			);
		}
		return array('action_params' => $action_params);
	}
	
	function paramsToArray($params='', $name='Parameter') 
	{
		if ( !$params ) {
			return false;
		}
		$list = explode("\n", trim($params));
		$return = array();
		foreach ( $list as $item ) {
			$item = trim($item);
			if ( !$item ) {
				$form->debug['redirect_url'][] = "Empty string found in the {$name} box";
				continue;
			}
			$fields_data = explode("=", $item, 2);
			if ( ! isset($fields_data[1]) || !$fields_data[1] ) {
				$form->debug['redirect_url'][] = "{$name} {$fields_data[0]} has no value set";
				continue;
			}
			$param = trim($fields_data[0]);
			$value = trim($fields_data[1]);
			if ( substr($value, 0, 1) == '{' && substr($value, -1, 1) == '}') {
				$value = substr($value, 1, strlen($value)-2);
				$value = trim($value);
				$var = JRequest::getVar($value);
				if ( is_array($var) ) {
					$return[$param] = array();
					foreach( $var as $k => $v) {
						$return[$param][$k] = $v;
					}
				} else {
					$return[$param] = $var;
				}
			} elseif ( $value == 'NULL' ) {
				$return[$param] = '';
			} else {
				$return[$param] = $value;
			}
		}
		return $return;
	}
}
?>