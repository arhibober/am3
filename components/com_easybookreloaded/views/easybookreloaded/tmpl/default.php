<?php 
  /**
 * Easybook Reloaded
 * Based on: Easybook by http://www.easy-joomla.org
 * @license    GNU/GPL
 * Project Page: http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

defined('_JEXEC') or die('Restricted access');

if ($this->params->get('offline'))
{
	echo $this->loadTemplate('header');
	echo JText::_('GUESTBOOK_OFFLINE_FRONTEND');
	echo $this->loadTemplate('footer');
	echo '</div></div>';
}
else
{
	echo $this->loadTemplate('header');
	echo $this->loadTemplate('entrys'); 

	if ($this->params->get('show_count_entries'))
	{ ?>
		<div>
			<br /><strong class='easy_pagination'><?php echo $this->count ?><br />
			<?php if ($this->count == 1) 
			{
				echo JText::_('ENTRY_IN_THE_GUESTBOOK');
			} 
			else 
			{
				echo JText::_('ENTRIES_IN_THE_GUESTBOOK');
			} ?>
			</strong>
		</div>
	<?php }
	
	if ($this->pagination->total > $this->pagination->limit) 
	{
		echo '<div class="easy_pagination">';
		echo $this->pagination->getPagesLinks();
		echo '</div>';
	}
	
	echo $this->loadTemplate('footer');
	?>
	</div>
	</div>
<?php } ?>
