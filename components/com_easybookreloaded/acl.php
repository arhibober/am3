<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// ACL Check
$user = JFactory::getUser();
$params = JComponentHelper::getParams('com_easybookreloaded');
$canAdd = false;
$canEdit = false;

$add_acl_array = explode(",", $params->get('add_acl', 1));
$admin_acl_array = explode(",", $params->get('admin_acl', 8));

$usergroup = JAccess::getGroupsByUser($user->id);

foreach ($usergroup as $value)
{
	foreach ($add_acl_array as $add_acl_value)
	{
		if ($value == $add_acl_value)
		{
			$canAdd = true;
		}
	}
	foreach ($admin_acl_array as $admin_acl_value)
	{
		if ($value == $admin_acl_value)
		{
			$canEdit = true;
		}
	}
}

define('_EASYBOOK_CANADD', $canAdd);
define('_EASYBOOK_CANEDIT', $canEdit);
?>
