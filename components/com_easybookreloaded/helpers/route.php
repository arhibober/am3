<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

class EasybookReloadedHelperRoute
{
	function getEasybookReloadedRoute($id, $Itemid)
	{
		$limit = EasybookReloadedHelperRoute::_limitstart($id);
		$link = 'index.php?option=com_easybookreloaded&view=easybookreloaded';
		$link .= '&Itemid='.$Itemid;
		
		if ($limit != 0) 
		{
			$link .= '&limitstart='.$limit;
		}
		
		$link .= '#gbentry_'.$id;
		
		return $link;		
	}
	
	function getEasybookReloadedRouteHashPublish($id, $Itemid)
	{
		//Create the link
		$link = 'index.php?option=com_easybookreloaded&task=publish_mail';
		$link .= '&Itemid='.$Itemid;
		$link .= '&hash=';
		
		return $link;		
	}
	
	function getEasybookReloadedRouteHashDelete($id, $Itemid)
	{
		$link = 'index.php?option=com_easybookreloaded&task=remove_mail';
		$link .= '&Itemid='.$Itemid;
		$link .= '&hash=';
		
		return $link;		
	}
	
	function getEasybookReloadedRouteHashComment($id, $Itemid)
	{
		$link = 'index.php?option=com_easybookreloaded&task=comment_mail';
		$link .= '&Itemid='.$Itemid;
		$link .= '&hash=';
		
		return $link;		
	}

	function getEasybookReloadedRouteHashEdit($id, $Itemid)
	{
		$link = 'index.php?option=com_easybookreloaded&task=edit_mail';
		$link .= '&Itemid='.$Itemid;
		$link .= '&hash=';
		
		return $link;		
	}
	
	// Limitstart ermitteln - Easybook Reloaded 2.0.2
	function _limitstart($id)
	{
		$bookParams = JComponentHelper::getParams('com_easybookreloaded');
		$bookEntrieperPage = $bookParams->get('entries_perpage', 5);
		
		$query = 'SELECT * FROM #__easybook WHERE published = 1 ORDER BY `id` DESC';
		$db	= JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		$result = $db->loadResultArray();
		
		$key = array_search($id, $result);
		$limit = $bookEntrieperPage * intval($key/$bookEntrieperPage);
		
		return $limit;
	}
}
?>
