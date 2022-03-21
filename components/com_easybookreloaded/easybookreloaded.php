<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
define('_EASYBOOK_VERSION', '3.0.5');

// Require the base controller
require_once(JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'content.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'smilie.php');
require_once(JPATH_COMPONENT.DS.'acl.php');

// Require specific controller if requested
if ($controller = JRequest::getWord('controller')) 
{
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) 
	{
        require_once $path;
    } 
	else 
	{
        $controller = '';
    }
}

// Create the controller
$classname    = 'EasybookReloadedController'.$controller;
$controller   = new $classname();

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
?>