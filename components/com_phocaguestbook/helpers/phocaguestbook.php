<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

class PhocaguestbookHelperFront
{
	function setTinyMCEJS()
	{
		$js = "\t<script type=\"text/javascript\" src=\"".JURI::root()."media/editors/tinymce/jscripts/tiny_mce/tiny_mce.js\"></script>\n";
		return $js;
	}
	
	function setCaptchaReloadJS()
	{
	/*	$js = "\t". '<script type="text/javascript">function reloadCaptcha() {    var capObj = document.getElementById(\'phocacaptcha\');    if (capObj) {        capObj.src = capObj.src +            (capObj.src.indexOf(\'?\') > -1 ? \'&\' : \'?\') + Math.random();    }} </script>' . "\n";
		*/
		$js  = "\t". '<script type="text/javascript">'."\n".'var pcid = 0;'."\n"
		     . 'function reloadCaptcha() {' . "\n"
			 . 'now = new Date();' . "\n"
			 . 'var capObj = document.getElementById(\'phocacaptcha\');' . "\n"
			 . 'if (capObj) {' . "\n"
			 . 'capObj.src = capObj.src + (capObj.src.indexOf(\'?\') > -1 ? \'&amp;pcid[\'+ pcid +\']=\' : \'?pcid[\'+ pcid +\']=\') + Math.ceil(Math.random()*(now.getTime()));' . "\n"
			 . 'pcid++;' . "\n"
			 . ' }' . "\n"
			 . '}'. "\n"
			 . '</script>' . "\n";
			
			return $js;
	}
	
	
	function displaySimpleTinyMCEJS($displayPath = 0) {

		
	
		$js =	'<script type="text/javascript">' . "\n";
		$js .= 	 'tinyMCE.init({'. "\n"
					.'mode : "textareas",'. "\n"
					.'theme : "advanced",'. "\n"
					.'language : "en",'. "\n"
					.'plugins : "emotions",'. "\n"
					.'editor_selector : "mceEditor",'. "\n"					
					.'theme_advanced_buttons1 : "bold, italic, underline, separator, strikethrough, justifyleft, justifycenter, justifyright, justifyfull, bullist, numlist, undo, redo, link, unlink, separator, emotions",'. "\n"
					.'theme_advanced_buttons2 : "",'. "\n"
					.'theme_advanced_buttons3 : "",'. "\n"
					.'theme_advanced_toolbar_location : "top",'. "\n"
					.'theme_advanced_toolbar_align : "left",'. "\n";
		if ($displayPath == 1) {
			$js .= 'theme_advanced_path_location : "bottom",'. "\n";
		}
		$js .=		 'extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
});' . "\n";
		$js .=	'</script>';
		return $js;

	}
	
	function displayTextArea($name='content',$content='', $width=50, $height=50, $col=10, $row=10, $buttons = false)
	{
		if (is_numeric( $width )) {
			$width .= 'px';
		}
		if (is_numeric( $height )) {
			$height .= 'px';
		}
		$editor  = "<textarea id=\"$name\" name=\"$name\" cols=\"$col\" rows=\"$row\" style=\"width:{$width}; height:{$height};\" class=\"mceEditor\">$content</textarea>\n" . $buttons;

		return $editor;
	}
	
	function checkWordChar($string, $length) {
		if (JString::strlen($string) < $length || JString::strlen($string) == $length) {
			return $string;
		} else {
			return false;
		}
	}
	
	function wordDelete($string,$length,$end) {
		if (JString::strlen($string) < $length || JString::strlen($string) == $length) {
			return $string;
		} else {
			return JString::substr($string, 0, $length) . $end;
		}
	}
	
	function getDateFormat($dateFormat) {
		switch ($dateFormat) {
			case 1:
			$dateFormat = 'd. F Y';
			break;
			case 2:
			$dateFormat = 'd/m/y';
			break;
			case 3:
			$dateFormat = 'd. m. Y';
			break;
		}
		return $dateFormat;
	}
	
	/*
	 *
	 * TODO: now we are working with only two groups: 1.. registers, 2.. special
	 * So we only check these groups
	 */
	public function getNeededAccessLevels() {
	
		// TODO: can be stored in parameters
		return array(2,3,4);
	}
	
	/*
	 * Check if user's groups access rights (e.g. user is public, registered, special) can meet needed Levels
	 */
	
	public function isAccess($userLevels, $neededLevels) {
		
		$rightGroupAccess = 0;
		
		// User can be assigned to different groups
		foreach($userLevels as $keyuserLevels => $valueuserLevels) {
			foreach($neededLevels as $keyneededLevels => $valueneededLevels) {
			
				if ((int)$valueneededLevels == (int)$valueuserLevels) {
					$rightGroupAccess = 1;
					break;
				}
			}
			if ($rightGroupAccess == 1) {
				break;
			}
		}
		return (boolean)$rightGroupAccess;
	}
	
	public function canAdmin() {
		$user = JFactory::getUser();
		
		if (isset($user->groups)) {
			foreach ($user->groups as $key => $value) {
				if (strtolower($value) == strtolower('administrator') || strtolower($value) == strtolower('super users')) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function toArray($value = FALSE) {
		if ($value == FALSE) {
			return array(0 => 0);
		} else if (empty($value)) {
			return array(0 => 0);
		} else if (is_array($value)) {
			return $value;
		} else {
			return array(0 => $value);
		}
	
	}
	
	function getInfo() {
		return base64_decode('PGRpdiBzdHlsZT0idGV4dC1hbGlnbjogcmlnaHQ7IGNvbG9yOiNkM2QzZDM7Ij5Qb3dlcmVkIGJ5IDxhIGhyZWY9Imh0dHA6Ly93d3cucGhvY2EuY3oiIHN0eWxlPSJ0ZXh0LWRlY29yYXRpb246IG5vbmU7IiB0YXJnZXQ9Il9ibGFuayIgdGl0bGU9IlBob2NhLmN6Ij5QaG9jYTwvYT4gPGEgaHJlZj0iaHR0cDovL3d3dy5waG9jYS5jei9waG9jYWd1ZXN0Ym9vayIgc3R5bGU9InRleHQtZGVjb3JhdGlvbjogbm9uZTsiIHRhcmdldD0iX2JsYW5rIiB0aXRsZT0iUGhvY2EgR3Vlc3Rib29rIj5HdWVzdGJvb2s8L2E+PC9kaXY+');
	}
	
	function isRegisteredUser($registeredUsersOnly = 1, $userId) {
		if ($registeredUsersOnly == 1) {
			if ( $userId > 0 ) {
				$registeredUsersOnly = 1;// display form - user is registered, registration required
			} else {
				$registeredUsersOnly = 0;// display no form - user is not registered, registration is required
			}
		} else {
			$registeredUsersOnly = 1;// user is not registered, registration is NOT required - care all as registered
		}
		return $registeredUsersOnly;
	}
	

	function isURLAddress($url) {
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
	
	
	function getCaptchaId($captchaMethod) {
		
		
		switch ((int)$captchaMethod){
			case 20:
				$c = array(1,2,3,4);
				$r = mt_rand(0,3);
				return $c[$r];
			break;
			
			case 21:
				$c = array(1,2,4);
				$r = mt_rand(0,2);
				return $c[$r];
			break;
			
			case 22:
				$c = array(1,3,4);
				$r = mt_rand(0,2);
				return $c[$r];
			break;
			
			case 23:
				$c = array(2,3,4);
				$r = mt_rand(0,2);
				return $c[$r];
			break;
			
			case 24:
				$c = array(1,4);
				$r = mt_rand(0,1);
				return $c[$r];
			break;
			
			case 25:
				$c = array(2,4);
				$r = mt_rand(0,1);
				return $c[$r];
			break;
			
			case 26:
				$c = array(3,4);
				$r = mt_rand(0,1);
				return $c[$r];
			break;
			
			
			
			case 10:
				$c = array(1,2,3);
				$r = mt_rand(0,2);
				return $c[$r];
			break;
			
			case 11:
				$c = array(1,2);
				$r = mt_rand(0,1);
				return $c[$r];
			break;
			
			case 12:
				$c = array(1,3);
				$r = mt_rand(0,1);
				return $c[$r];
			break;
			
			case 13:
				$c = array(2,3);
				$r = mt_rand(0,1);
				return $c[$r];
			break;
			
			case 4:
				return 4;
			break;
			
			case 3:
				return 3;
			break;
			
			case 2:
				return 2;
			break;
			
			case 1:
			default:
				return 1;
			break;
		}
		
		return 1;
	}
	
	function getRequiredSign($required = 0) {
		
		$paramsC 				= JComponentHelper::getParams('com_phocaguestbook') ;
		$display_required_sign	= $paramsC->get( 'display_required_sign', 1 );
		
		if ((int)$display_required_sign == 1) {
			if ($required == 2) {
				return '&nbsp;<b style="color:red">*</b>';
			}
			return '';
		} else {
			return ':';
		}
	}
	
	function getRandomString($length = '') {
		
		$code = md5(uniqid(rand(), true));
		if ($length != '' && (int)$length > 0) {
			$length = $length - 1;
			return chr(rand(97,122)) . substr($code, 0, $length);
		} else {
			return chr(rand(97,122)) . $code;
		}
	}
	
	function setHiddenFieldPos($title, $name, $email, $website, $content) {
		$form = array();
		if ((int)$title > 0) {
			$form[] = 1;
		}
		if ((int)$name > 0) {
			$form[] = 2;
		}
		if ((int)$email > 0) {
			$form[] = 3;
		}
		if ((int)$website > 0) {
			$form[] = 4;
		}
		if ((int)$content > 0) {
			$form[] = 5;
		}
		
		$value = mt_rand(0,count($form) - 1);
		
		return $form[$value];
		
	}
	
	public function getLangSef($langCode) {
		$langSef = '';
		if ($langCode != '') {
			jimport('joomla.language.helper');
			$code = JLanguageHelper::getLanguages('lang_code');
			if (isset($code[$langCode]->sef)) {
				$langSef = $code[$langCode]->sef;
			}
		}
		return $langSef;
	}
	
}
?>