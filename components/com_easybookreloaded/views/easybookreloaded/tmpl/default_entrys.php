<?php 
 /**
 * Easybook Reloaded
 * Based on: Easybook by http://www.easy-joomla.org
 * @license    GNU/GPL
 * Project Page: http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */
 
defined('_JEXEC') or die('Restricted access');

foreach ($this->entrys as $entry) 
{ ?>
	<div class="easy_frame" <?php if (!$entry->published) { ?> style="background-color: #fffefd; border: #ffb39b solid 1px;" <?php } ?>>
	<div class="easy_top" <?php if (!$entry->published) { ?> style="background-color: #FFE7D7;" <?php } ?>>
	<div class="easy_top_left">
		<strong class="easy_big" id="gbentry_<?php echo $entry->id; ?>"><?php echo $entry->gbname ?></strong>
		<strong class="easy_small">
		<?php if ($entry->published) 
		{ // Datumsformat international
			if ($this->params->get('date_format') == 0)
			{
				echo JHTML::_('date', $entry->gbdate, JText::_('DATE_FORMAT_LC2'));
			}
			else
			{
				echo JHTML::_('date', $entry->gbdate, JText::_('DATE_FORMAT_LC1'));
			}
			
			if ($entry->gbloca) 
			{
				echo ' | '.$entry->gbloca;
			}
		}
		
		if (!$entry->published) 
		{
			echo " | </strong><strong class='easy_small_red'>". JText::_( 'ENTRY_OFFLINE'); 
		} ?>
		</strong>
	</div>
	<div class="easy_top_right">
		<?php
		//Voting
		if ($this->params->get('show_rating', true) AND $entry->gbvote !== "0") 
		{
			for ($start=1; $start<=$this->params->get('rating_max', 5); $start++) 
			{
				if ($this->params->get('show_rating_type') == 0) 
				{
					$ratimg = $entry->gbvote >= $start ? 'sun_full.png' : 'sun_empty.png';
				}
				elseif ($this->params->get('show_rating_type') == 1) 
				{
					$ratimg = $entry->gbvote >= $start ? 'star_full.png' : 'star_empty.png';
				}
				elseif ($this->params->get('show_rating_type') == 2) 
				{
					$ratimg = $entry->gbvote >= $start ? 'star_boxed_full.png' : 'star_boxed_empty.png';
				}
				echo JHTML::_('image', 'components/com_easybookreloaded/images/'.$ratimg, JText::_('Rating'), 'border="0" class="easy_align_middle"');
			}
		}
		
		// Adminfunktionen
		
		if (_EASYBOOK_CANEDIT) 
		{
			echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
			echo "<a href='".JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=edit&cid='.(int)$entry->id)."' title='".JText::_('EDIT_ENTRY')."'>".JHTML::_('image', 'components/com_easybookreloaded/images/edit.png', JText::_('EDIT_ENTRY'), 'class="easy_align_middle" border="0"')."</a>&nbsp;&nbsp;\n";
			echo "<a href='".JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=remove&cid='.(int)$entry->id)."' title='".JText::_('DELETE_ENTRY')."'>".JHTML::_('image', 'components/com_easybookreloaded/images/delete.png', JText::_('DELETE_ENTRY'), 'class="easy_align_middle" border="0"')."</a>&nbsp;&nbsp;\n";
			if ($entry->gbcomment != "")
			{
				echo "<a href='".JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=comment&cid='.(int)$entry->id)."' title='".JText::_('EDIT_COMMENT')."'>".JHTML::_('image', 'components/com_easybookreloaded/images/comment_edit.png', JText::_('EDIT_COMMENT'), 'class="easy_align_middle" border="0"')."</a>&nbsp;&nbsp;\n";
			} 
			else 
			{
				echo "<a href='".JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=comment&cid='.(int)$entry->id)."' title='".JText::_('EDIT_COMMENT')."'>".JHTML::_('image', 'components/com_easybookreloaded/images/comment.png', JText::_('EDIT_COMMENT'), 'class="easy_align_middle" border="0"')."</a>&nbsp;&nbsp;\n";
			}
			if ($entry->published == 0) 
			{
				echo "<a href='".JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=publish&cid='.(int)$entry->id)."' title='".JText::_('PUBLISH_ENTRY')."'>".JHTML::_('image', 'components/com_easybookreloaded/images/offline.png', JText::_('PUBLISH_ENTRY'), 'class="easy_align_middle" border="0"')."</a>\n";
			} 
			else 
			{
				echo "<a href='".JRoute::_('index.php?option=com_easybookreloaded&controller=entry&task=publish&cid='.(int)$entry->id)."' title='".JText::_('UnPUBLISH_ENTRY')."'>".JHTML::_('image', 'components/com_easybookreloaded/images/online.png', JText::_('UnPUBLISH_ENTRY'), 'class="easy_align_middle" border="0"')."</a>\n";
			}
		} 
		
		
		?>
	</div>
	<div style="clear: both;"></div>
	</div>
	<?php if (($entry->gbmail != "" AND $this->params->get('show_mail', true) AND $entry->gbmailshow) OR ($entry->gbpage != "" AND $this->params->get('show_home', true)) OR ($entry->gbicq != "" AND $this->params->get('show_icq', true)) OR ($entry->gbaim != "" AND $this->params->get('show_aim', true)) OR ($entry->gbmsn != "" AND $this->params->get('show_msn', true)) OR ($entry->gbyah != "" AND $this->params->get('show_yah', true)) OR ($entry->gbskype != "" AND $this->params->get('show_skype', true))) 
	{ ?>
		<div class='easy_contact'>		
			<?php
			//Display CONTACT_DETAILS if available
			//E-Mail
			if ($entry->gbmail != "" AND $this->params->get('show_mail', true) AND $entry->gbmailshow) 
			{
				$image = JHTML::_('image', 'components/com_easybookreloaded/images/email.png', '', 'height="16" width="16" class="png" hspace="3" border="0"');
				echo JHTML::_('email.cloak', $entry->gbmail, true, $image, false);
			}
			
			//Homepage
			if ($entry->gbpage != "" AND $this->params->get('show_home', true)) 
			{
				if (substr($entry->gbpage,0,7)!="http://") 
				{
					$entry->gbpage="http://$entry->gbpage";
				}
				echo "<a href=\"$entry->gbpage\" title=\"".JTEXT::_('Homepage')." - $entry->gbpage\" ";
				
				if ($this->params->get('nofollow_home', true)) 
				{
					echo "rel=\"nofollow\" ";
				}
				
				echo "target=\"_blank\">".JHTML::_('image', 'components/com_easybookreloaded/images/world.png', $entry->gbpage, 'height="16" width="16" class="png" hspace="3" border="0"')."</a>";
			}
			
			//ICQ
			if ($entry->gbicq != "" AND $this->params->get('show_icq', true)) 
			{
				echo "<a href=\"mailto:$entry->gbicq@pager.icq.com\">".JHTML::_('image', 'components/com_easybookreloaded/images/im-icq.png', $entry->gbicq, 'title="'.JTEXT::_('ICQ_NUMBER').' - '.$entry->gbicq.'" border="0" height="16" width="16" class="png" hspace="3"')."</a>";
			}
			
			//AIM
			if ($entry->gbaim != "" AND $this->params->get('show_aim', true)) 
			{
				echo "<a href=\"aim:goim?screenname=$entry->gbaim\">".JHTML::_('image', 'components/com_easybookreloaded/images/im-aim.png', $entry->gbaim, 'title="'.JTEXT::_('AIM_NICKNAME').' - '.$entry->gbaim.'" border="0" height="16" width="16" class="png" hspace="3"')."</a>";
			}
			
			//MSN
			if ($entry->gbmsn != "" AND $this->params->get('show_msn', true)) 
			{
				echo JHTML::_('image', 'components/com_easybookreloaded/images/im-msn.png', $entry->gbmsn, 'title="'.JTEXT::_('MSN_MESSENGER').' - '.$entry->gbmsn.'" border="0" height="16" width="16" class="png" hspace="3"');
			}
			
			//Yahoo
			if ($entry->gbyah != "" AND $this->params->get('show_yah', true)) 
			{
				echo "<a href='ymsgr:sendIM?$entry->gbyah'>".JHTML::_('image', 'components/com_easybookreloaded/images/im-yahoo.png', $entry->gbyah, 'title="'.JTEXT::_('YAHOO_MESSENGER').' - '.$entry->gbyah.'" border="0" height="16" width="16" class="png" hspace="3"')."</a>";
			}
			
			//Skype
			if ($entry->gbskype != "" AND $this->params->get('show_skype', true)) 
			{
				echo "<a href='skype:" . $entry->gbskype . "?call'>".JHTML::_('image', 'components/com_easybookreloaded/images/im-skype.png', $entry->gbskype, 'title="'.JTEXT::_('SKYPE_NICKNAME').' - '.$entry->gbskype.'" border="0" height="16" width="16" class="png" hspace="3"')."</a>";
			} ?>
		</div>
	<?php }
	if ($entry->gbtitle != "" AND $this->params->get('show_title', true)) 
	{ ?>
		<div class="easy_title">
			<?php echo $entry->gbtitle; ?>
		</div>
	<?php } ?>
	<div class="easy_content">
		<?php echo EasybookReloadedHelperContent::parse($entry->gbtext) ?>
	</div>
	<?php if ($entry->gbcomment) { ?>
		<div class="easy_admincomment">
		<?php echo JHTML::_('image', 'components/com_easybookreloaded/images/admin.png', JText::_('ADMIN_COMMENT:'), 'class="easy_align_middle" style="padding-bottom: 2px;"'); ?>
		<strong><?php echo JText::_( 'ADMIN_COMMENT'); ?>:</strong><br />
		<?php echo EasybookReloadedHelperContent::parse($entry->gbcomment) ?>
		</div>
	<?php } ?>
	</div>
	<p class="clr"></p>
<?php } ?>
