<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.utilities.date');
jimport('joomla.html.pane');
?><script language="javascript" type="text/javascript">
<!--
function submitbutton() {
	
	var novaluesPGB='';
	var formPGB = document.saveForm;
	var textPGB = tinyMCE.get('pgbcontent').getContent();

	
	if (novaluesPGB!=''){}
	<?php
	if ($this->tmpl['display_title_form']== 2) {?>
	else if ( formPGB.title.value == "" ) {
		alert("<?php echo JText::_( 'COM_PHOCAGUESTBOOK_NO_SUBJECT', true); ?>");return false;} <?php }
	if ($this->tmpl['display_name_form']== 2){?>
	else if( formPGB.pgusername.value == "" ) {
		alert("<?php echo JText::_( 'COM_PHOCAGUESTBOOK_NO_USERNAME', true); ?>");return false;}<?php }
	if ($this->tmpl['display_email_form']== 2){?>
	else if( formPGB.email.value == "" ) {
		alert("<?php echo JText::_( 'COM_PHOCAGUESTBOOK_NO_EMAIL', true); ?>");return false;}<?php }
	if ($this->tmpl['display_website_form']== 2){?>
	else if( formPGB.website.value == "" ) {
		alert("<?php echo JText::_( 'COM_PHOCAGUESTBOOK_NO_WEBSITE', true); ?>");return false;}<?php }
	if ($this->tmpl['display_content_form']== 2){?>
	else if( textPGB == "" ) {
		alert("<?php echo JText::_( 'COM_PHOCAGUESTBOOK_NO_CONTENT', true); ?>");return false;}<?php } ?>
}
--></script><?php

// - - - - - - - - - - -
// Header
// - - - - - - - - - - -
echo '<div id="phocaguestbook" class="guestbook'.$this->params->get( 'pageclass_sfx' ).'">';

if ( $this->params->get( 'show_page_heading' ) ) { 
	echo '<h1>'. $this->escape($this->params->get('page_heading')) . '</h1>';
}

if ( @$this->image || @$this->guestbooks->description ) {
	echo '<div class="guestbook-description">';
	if ( isset($this->tmpl['image']) ) {
		echo $this->tmpl['image'];
	}
	echo $this->guestbooks->description;
	echo '</div>';
}

// - - - - - - - - - - -
// Form2 - Pagination
// - - - - - - - - - - -
$form2 = '';
if ($this->tmpl['display_posts'] == 1) {
	$form2 = '<p>&nbsp;</p><div><form action="'.$this->action.'" method="post" name="adminForm" id="pgbadminForm">';
	if (count($this->items)) {
		$form2 .='<div class="pgcenter"><div class="pagination">';
		if ($this->params->get('show_pagination_limit')) {
			$form2 .= '<div class="pginline">'.JText::_('COM_PHOCAGUESTBOOK_DISPLAY_NUM') .'&nbsp;'.$this->pagination->getLimitBox().'</div>';
		}
		if ($this->params->get('show_pagination')) {
			$form2 .= '<div style="margin:0 10px 0 10px;display:inline;" class="sectiontablefooter'.$this->params->get( 'pageclass_sfx' ).'" id="pg-pagination" >'.$this->pagination->getPagesLinks().'</div><div style="margin:0 10px 0 10px;display:inline;" class="pagecounter">'.$this->pagination->getPagesCounter().'</div>';
		}
		$form2 .='</div></div>';
	}
	$form2 .= '</form></div>';
}
$form2 .= $this->tmpl['ft'];

// - - - - - - - - - - -
// Messages - create and correct Messages (Posts, Items)
// - - - - - - - - - - -
$gbPosts = '';//Messages (Posts, Items)
if ($this->tmpl['display_posts'] == 1) {
	foreach ($this->items as $key => $values) {
		//Maximum of links in the message
		$rand 				= '%phoca' . mt_rand(0,1000) * time() . 'phoca%';
		$ahref_replace 		= "<a ".$rand."=";
		$ahref_search		= "/<a ".$rand."=/";
		$values->content	= preg_replace ("/<a href=/", $ahref_replace, $values->content, $this->tmpl['max_url']);
		
		$values->content	= preg_replace ("/\<a href\=.*?\>(.*?)\<\/a\>/",	"$1", $values->content);
		$values->content	= preg_replace ($ahref_search, "<a href=", $values->content);
		
		
		// Forbidden Word Filter
		// Believe or not - it is more faster to replace items than the whole content :-)
		foreach ($this->tmpl['fwfa'] as $key2 => $values2) {
			if (function_exists('str_ireplace')) {
				$values->username 	= str_ireplace (trim($values2), '***', $values->username);
				$values->title		= str_ireplace (trim($values2), '***', $values->title);
				$values->content	= str_ireplace (trim($values2), '***', $values->content);
				$values->email		= str_ireplace (trim($values2), '***', $values->email);
				$values->homesite	= str_ireplace (trim($values2), '***', $values->homesite);
			} else {		
				$values->username 	= str_replace (trim($values2), '***', $values->username);
				$values->title		= str_replace (trim($values2), '***', $values->title);
				$values->content	= str_replace (trim($values2), '***', $values->content);
				$values->email		= str_replace (trim($values2), '***', $values->email);
				$values->homesite	= str_replace (trim($values2), '***', $values->homesite);
			}
		}
		
		//Forbidden Whole Word Filter
		foreach ($this->tmpl['fwwfa'] as $key3 => $values3) {
			if ($values3 !='') {
				//$values3			= "/([\. ])".$values3."([\. ])/";
				$values3			= "/(^|[^a-zA-Z0-9_]){1}(".preg_quote(($values3),"/").")($|[^a-zA-Z0-9_]){1}/i";
				$values->username 	= preg_replace ($values3, "\\1***\\3", $values->username);// \\2
				$values->title		= preg_replace ($values3, "\\1***\\3", $values->title);
				$values->content	= preg_replace ($values3, "\\1***\\3", $values->content);
				$values->email		= preg_replace ($values3, "\\1***\\3", $values->email);
				$values->homesite	= preg_replace ($values3, "\\1***\\3", $values->homesite);
			}
		}
		
		//Hack, because Joomla add some bad code to src and href
		if (function_exists('str_ireplace')) {
			$values->content	= str_ireplace ('../plugins/editors/tinymce/', 'plugins/editors/tinymce/', $values->content);
		} else {		
			$values->content	= str_replace ('../plugins/editors/tinymce/', 'plugins/editors/tinymce/', $values->content);
		}
			
		$gbPosts .= '<div class="pgbox" style="border:1px solid '.$this->tmpl['border_color'].';color:'.$this->tmpl['font_color'].';">';
		$gbPosts .= '<h4 class="pgtitle" style="background:'.$this->tmpl['background_color'].';color:'.$this->tmpl['font_color'].';">';
		
		//!!! username saved in database can be username or name
		$sep = 0;
		if ($this->tmpl['display_name'] != 0) {
			if ($values->username != '') {
				$gbPosts .= PhocaguestbookHelperFront::wordDelete($values->username, 40, '...');
				$sep = 1;
			}
		}
		
		if ($this->tmpl['display_email'] != 0) {
			if ($values->email != '') {
				if ($sep == 1) {
					$gbPosts .= ' ';
					$gbPosts .= '( '. JHTML::_( 'email.cloak', PhocaguestbookHelperFront::wordDelete($values->email, 50, '...') ).' )';
					$sep = 1;
				} else {
					$gbPosts .= JHTML::_( 'email.cloak', PhocaguestbookHelperFront::wordDelete($values->email, 50, '...') );
					$sep = 1;
				}
			}
		}
		
		if ($values->title != '') {
			if ($sep == 1) {
				$gbPosts .= ': ';
			}
			$gbPosts .= PhocaguestbookHelperFront::wordDelete($values->title, 100, '...');
		}
		
		
		
		if ($this->tmpl['display_website'] != 0) {
			if ($values->homesite != '') {
				
				if ($values->title == '' && $values->email == '' && $values->username == '') {
					$gbPosts .= '';
				} else {
					$gbPosts .= ' <br />';
				}
				
				$gbPosts .= ' <span><a href="'.$values->homesite.'">'.PhocaguestbookHelperFront::wordDelete($values->homesite, 50, '...').'</a></span>';
			}
		}
		
		$gbPosts .= '</h4>';
		
		// SECURITY
		// Open a tag protection
		$a_count 		= substr_count(strtolower($values->content), "<a");
		$a_end_count 	= substr_count(strtolower($values->content), "</a>");
		$quote_count	= substr_count(strtolower($values->content), "\"");
		
		if ($quote_count%2!=0) {
			$end_quote = "\""; // close the " if it is open
		} else {
			$end_quote = "";
		}
		
		if ($a_count > $a_end_count) {
			$end_a = "></a>"; // close the a tag if there is a open a tag
							  // in case <a href> ... <a href ></a>
							  // in case <a ... <a href >></a>
		} else {
			$end_a = "";
		}
		
		$gbPosts .= '<div class="pgcontent" style="overflow: auto;border-left:5px solid '.$this->tmpl['background_color'].';">' . $values->content . $end_quote .$end_a. '</div>';
		$gbPosts .= '<p class="pgcontentbottom"><small style="color:'.$this->tmpl['second_font_color'].'">' . JHTML::_('date',  $values->date, JText::_( $this->tmpl['date_format'] ) ) . '</small>';
		
		if ($this->tmpl['administrator'] != 0) {
			$gbPosts.='<a href="'.JRoute::_('index.php?option=com_phocaguestbook&view=phocaguestbook&id='.$this->id.'&Itemid='.JRequest::getVar('Itemid', 0, '', 'int').'&controller=phocaguestbook&task=delete&mid='.$values->id.'&limitstart='.$this->pagination->limitstart).'" onclick="return confirm(\''.JText::_( 'COM_PHOCAGUESTBOOK_WARNING_DELETE_ITEM' ).'\');">'.JHTML::_('image', 'components/com_phocaguestbook/assets/images/icon-trash.gif', JText::_( 'COM_PHOCAGUESBOOK_DELETE' )).'</a>';
			
			$gbPosts.='<a href="'.JRoute::_('index.php?option=com_phocaguestbook&view=phocaguestbook&id='.$this->id.'&Itemid='.JRequest::getVar('Itemid', 0, '', 'int').'&controller=phocaguestbook&task=unpublish&mid='.$values->id.'&limitstart='.$this->pagination->limitstart).'">'.JHTML::_('image', 'components/com_phocaguestbook/assets/images/icon-unpublish.gif', JText::_( 'COM_PHOCAGUESTBOOK_UNPUBLISH' )).'</a>';
		}
		$gbPosts.='</p></div>';	
	}
}

// - - - - - - - - - - -
// Form Top (Form 1 - Messages)
// - - - - - - - - - - - 
// Display Messages (Posts, Items)
// Forms (If position = 1 --> Form is bottom, Messages top, if position = 0 --> Form is top, Messages bottom
if ($this->tmpl['form_position'] == 1) {
	echo $gbPosts;
}

if ($this->tmpl['show_form'] == 1) {
	if ($this->tmpl['display_form'] == 0 ) {
		
		//echo $pane->startPanel( JText::_('COM_PHOCAGUESTBOOK_POST_MESSAGE'), 'phocaguestbook-jpane-toggler-down' );
		
		echo '<div><span id="pg-open-guestbook" >'.JText::_('COM_PHOCAGUESTBOOK_POST_MESSAGE').'</span></div>';
	}

	echo '<div id="pg-guestbook">'
	.'<form action="'.$this->action.'" method="post" name="saveForm" id="pgbSaveForm" onsubmit="return submitbutton();">'
	.'<table width="'.$this->tmpl['table_width'].'">';
	
	if ($this->tmpl['errmsg_top'] != '') {
		echo '<tr>'
		.'<td>&nbsp;</td>'
		.'<td colspan="3">';
		//-- Server side checking 
		echo $this->tmpl['errmsg_top'];
		//-- Server side checking
		echo '&nbsp;</td>'
		.'</tr>';
	}
	
	if ((int)$this->tmpl['display_title_form'] > 0){	
		echo '<tr>'
		.'<td width="5"><strong>'.JText::_('COM_PHOCAGUESTBOOK_SUBJECT').PhocaguestbookHelperFront::getRequiredSign((int)$this->tmpl['display_title_form']).' </strong></td>'
		.'<td colspan="3">'
		.'<input type="text" name="title" id="pgbtitle" value="'. $this->formdata->title .'" size="32" maxlength="200" class="pgbinput" />'.$this->tmpl['hidden_field_output'][1].'</td>'
		.'</tr>';
	}
		
	if ((int)$this->tmpl['display_name_form'] > 0){
		echo '<tr>'
		.'<td><strong>'.JText::_('COM_PHOCAGUESTBOOK_NAME').PhocaguestbookHelperFront::getRequiredSign((int)$this->tmpl['display_name_form']).' </strong></td>'
		.'<td colspan="3">'
		.'<input type="text" name="pgusername" id="pgbusername" value="'.$this->formdata->username .'" size="32" maxlength="100" class="pgbinput" />'.$this->tmpl['hidden_field_output'][2].'</td>'
		.'</tr>';
	}
		
	if ((int)$this->tmpl['display_email_form'] > 0){
		echo '<tr>'
		.'<td><strong>'.JText::_('COM_PHOCAGUESTBOOK_EMAIL').PhocaguestbookHelperFront::getRequiredSign((int)$this->tmpl['display_email_form']).' </strong></td>'
		.'<td colspan="3">'
		.'<input type="text" name="email" id="pgbemail" value="'.$this->formdata->email .'" size="32" maxlength="100" class="pgbinput" />'.$this->tmpl['hidden_field_output'][3].'</td>'
		.'</tr>';
	}
	
	if ((int)$this->tmpl['display_website_form'] > 0){
		echo '<tr>'
		.'<td><strong>'.JText::_('COM_PHOCAGUESTBOOK_WEBSITE').PhocaguestbookHelperFront::getRequiredSign((int)$this->tmpl['display_website_form']).' </strong></td>'
		.'<td colspan="3">'
		.'<input type="text" name="website" id="pgbwebsite" value="'.$this->formdata->website .'" size="32" maxlength="100" class="pgbinput" />'.$this->tmpl['hidden_field_output'][4].'</td>'
		.'</tr>';
	}
	
	if ((int)$this->tmpl['display_content_form'] > 0){
		echo '<tr>'
		.'<td><strong>'.JText::_('COM_PHOCAGUESTBOOK_CONTENT').PhocaguestbookHelperFront::getRequiredSign((int)$this->tmpl['display_content_form']).' </strong></td>'
		.'<td colspan="3">'.$this->tmpl['editor'].$this->tmpl['hidden_field_output'][5].'</td>'
		.'</tr>';
	}
		
	if ((int)$this->tmpl['enable_captcha'] > 0 && $this->tmpl['captcha_id'] > 0) {
	
		// Server side checking CAPTCHA 
		echo $this->tmpl['errmsg_captcha'];
		//-- Server side checking CAPTCHA
			
		// Set fix height because of pane slider
			$imageHeight = 'style="height:105px"';
		
		echo '<tr>'
		.'<td width="5"><strong>'. JText::_('COM_PHOCAGUESTBOOK_IMG_VERIFICATION').PhocaguestbookHelperFront::getRequiredSign(2).' </strong></td>';

		if ((int)$this->tmpl['captcha_id'] == 4) {
			echo '<td colspan="2" align="center" valign="middle" '.$imageHeight . '>';
			echo PhocaGuestbookHelperReCaptcha::recaptcha_get_html($this->tmpl['recaptcha_publickey']);
			echo '</td>';
		} else {
		
		
			$index = 'index.php';
			$app = JFactory::getApplication();
			
			if ($app->getLanguageFilter()) {
				$lang 		= JFactory::getLanguage();
				$langCode 	= $lang->getTag();
				$langSef 	= PhocaguestbookHelperFront::getLangSef($langCode);
				if ($langSef != '') {
					$index = 'index.php/'.$langSef.'/';
				}
			}
			echo '<td width="5" align="left" valign="middle" '.$imageHeight . '>';
			echo '<img src="'. JURI::base(true).'/'.$index.'?option=com_phocaguestbook&view=guestbooki&id='.$this->id.'&Itemid='.JRequest::getVar('Itemid', 0, '', 'int').'&phocasid='. md5(uniqid(time())).'" alt="'.JText::_('COM_PHOCAGUESTBOOK_CAPTCHA_IMAGE').'" id="phocacaptcha" />';
			echo '</td>';
			
				
			echo '<td width="5" align="left" valign="middle">'
			.'<input type="text" id="pgbcaptcha" name="captcha" size="6" maxlength="6" class="pgbinput" /></td>';
				
			echo '<td align="center" width="50" valign="middle">';
			//Remove because of IE6 - href="javascript:void(0)" onclick="javascript:reloadCaptcha();"
			echo '<a href="javascript:reloadCaptcha();" title="'. JText::_('COM_PHOCAGUESTBOOK_RELOAD_IMAGE').'" >'
			. JHTML::_( 'image', 'components/com_phocaguestbook/assets/images/icon-reload.gif', JText::_('COM_PHOCAGUESTBOOK_RELOAD_IMAGE'))
			.'</a></td>';
		}

		echo '</tr>';
	}
		
	echo '<tr>'
	.'<td>&nbsp;</td>'
	.'<td colspan="3">'
	.'<input type="submit" name="save" value="'. JText::_('COM_PHOCAGUESTBOOK_SUBMIT').'" />'
	.' &nbsp;'
	.'<input type="reset" name="reset" value="'. JText::_('COM_PHOCAGUESTBOOK_RESET').'" /></td>'
	.'</tr>'
	.'</table>';

	echo '<input type="hidden" name="cid" value="'. $this->id .'" />' . "\n"
	.'<input type="hidden" name="language" value="'.$this->guestbooks->language.'" />' . "\n"
	.'<input type="hidden" name="option" value="com_phocaguestbook" />' . "\n"
	.'<input type="hidden" name="view" value="guestbook" />' . "\n"
	.'<input type="hidden" name="controller" value="phocaguestbook" />' . "\n"
	.'<input type="hidden" name="task" value="submit" />' . "\n"
	.'<input type="hidden" name="'. JUtility::getToken().'" value="1" />' . "\n"
	.'</form>'. "\n"
	.'</div><div style="clear:both;">&nbsp;</div>';
	
	// Display Pane or not
//	if ($this->tmpl['display_form'] == 0 ) {
//		echo '</div>';
//	}
	
} else {
	// Display or not to display Form, Registered user only, IP Ban
	// Show messages (Only registered user, IP Ban)
	echo $this->tmpl['ipa_msg'];
	echo $this->tmpl['reguser_msg'];
}

// - - - - - - - - - - -
// Form Bottom (Form 1 - Messages)
// - - - - - - - - - - - 
//Forms (If position = 1 --> Form is bottom, Messages top, if position = 0 --> Form is top, Messages bottom
if ($this->tmpl['form_position'] == 0) {
	echo $gbPosts;
}
//echo $form2;
echo '</div>';