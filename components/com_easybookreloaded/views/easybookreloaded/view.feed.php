<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class EasybookReloadedViewEasybookReloaded extends JView
{
	function display($tpl = null)
	{
		global $mainframe;
		$document = &JFactory::getDocument();
		$document->link = JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded');
		JRequest::setVar('limit', $mainframe->getCfg('feed_limit'));

		// Get some data from the model
		$items = &$this->get('Data');

		foreach ($items as $item) 
		{
			// strip html from feed item title
			$title = $this->escape($item->gbname);
			$title = html_entity_decode( $title );

			// url link to article
			$link = JRoute::_('index.php?option=com_easybookreloaded&view=easybookreloaded#gbentry_'.$item->id);

			// strip html from feed item description text
			$description = $item->gbtext;
			$date = ($item->gbdate ? date( 'r', strtotime($item->gbdate) ) : '');

			// load individual item creator class
			$feeditem = new JFeedItem();
			$feeditem->title 		= $title;
			$feeditem->link 		= $link;
			$feeditem->description 	= $description;
			$feeditem->date			= $date;
			$feeditem->category   	= 'Guestbook';

			// loads item info into rss array
			$document->addItem($feeditem);
		}
	}
}
?>
