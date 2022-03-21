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
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.folder' );

function com_install(){

	$document			= JFactory::getDocument();
	$document->addStyleSheet(JURI::base(true).'/components/com_phocaguestbook/assets/phocaguestbook.css');
	
	$lang = JFactory::getLanguage();
	$lang->load('com_phocaguestbook.sys');
	$lang->load('com_phocaguestbook');
	
	$styleInstall = 
	'display: block;
	background-color: #6699cc;
	margin: 10px;
	padding:10px 25px 10px 45px;
	-moz-border-radius: 8px;
	-webkit-border-radius: 8px;
	border-radius: 8px;
	border-bottom: 2px solid #215485;
	border-right: 2px solid #215485;
	border-top: 2px solid #8fbbe6;
	border-left: 2px solid #8fbbe6;
	width: 6em;
	text-align:center;
	color: #fff;
	font-weight: bold;
	font-size: x-large;
	text-decoration: none;
	background: #6699cc url(\''.JURI::base(true).'/components/com_phocaguestbook/assets/images/bg-install.png\') 10px center no-repeat;';
	
	$styleUpgrade = 
	'display: block;
	background-color: #6699cc;
	margin: 10px;
	padding:10px 25px 10px 45px;
	-moz-border-radius: 8px;
	-webkit-border-radius: 8px;
	border-radius: 8px;
	border-bottom: 2px solid #215485;
	border-right: 2px solid #215485;
	border-top: 2px solid #8fbbe6;
	border-left: 2px solid #8fbbe6;
	width: 6em;
	text-align:center;
	color: #fff;
	font-weight: bold;
	font-size: x-large;
	text-decoration: none;
	background: #6699cc url(\''.JURI::base(true).'/components/com_phocaguestbook/assets/images/bg-upgrade.png\') 10px center no-repeat;';	

	$message = '<p>&nbsp;</p><p>Please select if you want to Install or Upgrade Phoca Guestbook component. Click Install for new Phoca Guestbook installation. If you click on Install and some previous Phoca Guestbook version is installed on your system, all Phoca Guestbook data stored in database will be lost. If you click on Upgrade, previous Phoca Guestbook data stored in database will be not removed.</p>';
	
	?><div style="padding:20px;border:1px solid #b36b00;background:#fff">
		<a style="text-decoration:underline" href="http://www.phoca.cz/" target="_blank"><?php
			echo  JHTML::_('image', 'administrator/components/com_phocaguestbook/assets/images/icon-phoca-logo.png', 'Phoca.cz');
		?></a>
		<div style="position:relative;float:right;">
			<?php echo  JHTML::_('image', 'administrator/components/com_phocaguestbook/assets/images/logo-phoca.png', 'Phoca.cz');?>
		</div>
		<p>&nbsp;</p>
		<?php echo $message; ?>
		<div style="clear:both">&nbsp;</div>
		<div style="text-align:center"><center><table border="0" cellpadding="20" cellspacing="20">
			<tr>
				
				<td align="center" valign="middle">
					<div id="pg-install"><a style="<?php echo $styleInstall; ?>" href="index.php?option=com_phocaguestbook&amp;task=phocaguestbookinstall.install"><?php echo JText::_('COM_PHOCAGUESTBOOK_INSTALL'); ?></a></div>
				</td>
				
				<td align="center" valign="middle">
					<div id="pg-upgrade"><a style="<?php echo $styleUpgrade; ?>" href="index.php?option=com_phocaguestbook&amp;task=phocaguestbookinstall.upgrade"><?php echo JText::_('COM_PHOCAGUESTBOOK_UPGRADE'); ?></a></div>
				</td>
			</tr>
		</table></center></div>
		
		<p>&nbsp;</p><p>&nbsp;</p>
		<p>
		<a href="http://www.phoca.cz/phocaguestbook/" target="_blank">Phoca Guestbook Main Site</a><br />
		<a href="http://www.phoca.cz/documentation/" target="_blank">Phoca Guestbook User Manual</a><br />
		<a href="http://www.phoca.cz/forum/" target="_blank">Phoca Guestbook Forum</a><br />
		</p>
		
		<p>&nbsp;</p>
		<p><center><a style="text-decoration:underline" href="http://www.phoca.cz/" target="_blank">www.phoca.cz</a></center></p>		
<?php	
}
?>