<?php
defined('_JEXEC') or die('Restricted access');

//Load Footer
if ($this->params->get('show_logo', true) == 1) 
{ ?>
	<p id="easyfooter">
		<a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Joomla! Erweiterung by Kubik-Rubik.de - Viktor Vogel" target="_blank"><img src="<?php echo JURI::base(); ?>components/com_easybookreloaded/images/logo.png" class="png" alt="EasyBook Reloaded - Logo" title="Easybook Reloaded - Joomla Erweiterung by Kubik-Rubik.de - Viktor Vogel" border="0" width="138" height="48" /></a>
	</p>
<?php } 
elseif ($this->params->get('show_logo', true) == 2) 
{ ?>
	<p id="easyfooter">
		<a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Joomla! Erweiterung by Kubik-Rubik.de - Viktor Vogel" target="_blank">Easybook Reloaded by Kubik-Rubik.de</a>
	</p>
<?php }
elseif ($this->params->get('show_logo', true) == 3) 
{ ?>
	<p id="easyfooterinv">
		<a href="http://joomla-extensions.kubik-rubik.de" title="Easybook Reloaded - Joomla! Erweiterung by Kubik-Rubik.de - Viktor Vogel" target="_blank">Easybook Reloaded by Kubik-Rubik.de</a>
	</p>
<?php } ?>