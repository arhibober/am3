<?php
/*
This file is part of "Fox Contact Form", a free Joomla! 1.6 Contact Form
You can redistribute it and/or modify it under the terms of the GNU General Public License
GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
Author: Demis Palma
Documentation at http://www.fox.ra.it/joomla-extensions/fox-contact-form.html
Copyright: 2011 Demis Palma
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document =& JFactory::getDocument();
// Add a stylesheet
$css = JURI::base() . 'components/com_foxcontact/css/neon.css';
$document->addStyleSheet($css);

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by FoxContact
$controller = JController::getInstance('FoxContact');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
?>

<?php /*
Rather then removing this credits, you may find more useful to gain a permanent backlink
submitting your site at http://www.fox.ra.it/fox-contact-form-showcase.html ;)
*/?>
<div class="foxpowered">powered by <a href="http://www.fox.ra.it/" title="Joomla 1.6 contact form">fox contact</a></div>
<?php /*
Invece di rimuovere questi crediti, potresti trovare piu' utile guadagnare un backlink permanente
segnalando il tuo sito su http://www.fox.ra.it/fox-contact-form-showcase.html ;)
*/?>


