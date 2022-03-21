<?php
/**
* @file
* @brief    sigplus Image Gallery Plus installer script
* @author   Levente Hunyadi
* @version  1.4.2
* @remarks  Copyright (C) 2009-2010 Levente Hunyadi
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://hunyadi.info.hu/projects/sigplus
*/

/*
* sigplus Image Gallery Plus plug-in for Joomla
* Copyright 2009-2010 Levente Hunyadi
*
* sigplus is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* sigplus is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentSIGPlusInstallerScript {
	/**
	* Localize an error message with fallback to a default message.
	*/
	private static function translate($id, $message) {
		$text = JText::_($id);
		if ($text != $id) {  // message not translated
			return $text;
		} else {
			return $message;
		}
	}

	function __construct($parent) {

	}

	function install($parent) {

	}

	function uninstall($parent) {

	}

	function update($parent) {

	}

	function preflight($type, $parent) {

	}

	function postflight($type, $parent) {
		switch ($type) {
			case 'install':  // runs after installation is complete
			case 'update':
				$targetpath = JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'sigplus'.DS.'js'.DS.'jquery.js';
				$sourcepaths = array(
					'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js',
					'http://ajax.microsoft.com/ajax/jquery/jquery-1.4.4.min.js',
					'http://code.jquery.com/jquery-1.4.4.min.js'
				);
				foreach ($sourcepaths as $sourcepath) {
					if (copy($sourcepath, $targetpath)) {
						return;  // jQuery library successfully copied from a CDN source
					}
				}

				// jQuery library not copied from any CDN source
				$warnmsg = self::translate('SIGPLUS_INSTALLER_JQUERY', 'Unable to get the jQuery library from the following content delivery network (CDN) sources:<br/> %1$s Local copy of <kbd>jquery.js</kbd> in the folder %2$s will not available.');
				$app =& JFactory::getApplication();
				$app->enqueueMessage(sprintf($warnmsg, '<pre>'.implode("\n", $sourcepaths).'</pre>', '<kbd>'.dirname($targetpath).'</kbd>'), 'warning');
		}
	}
}
