<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view' );

class EasybookReloadedViewEntry extends JView
{
	 /**
	 * Hellos view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		$mainframe 	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$menus		= JSite::getMenu();
		$params 	= JComponentHelper::getParams( 'com_easybookreloaded' );
		$task		= JRequest::getVar( 'task' );
		$session 	= JFactory::getSession();
		$user		= JFactory::getUser();
		
		// Set CSS File
		if ($params->get('template_dark') == 0) 
		{
			JHTML::_('stylesheet', 'easybookreloaded.css', 'components/com_easybookreloaded/css/');
		}
		else
		{
			JHTML::_('stylesheet', 'easybookreloadeddark.css', 'components/com_easybookreloaded/css/');
		}
		
		$document->addCustomTag('
		<!--[if IE 6]>
    		<style type="text/css">
    				.easy_align_middle { behavior: url('.JURI::base().'components/com_easybookreloaded/scripts/pngbehavior.htc); }
    				.png { behavior: url('.JURI::base().'components/com_easybookreloaded/scripts/pngbehavior.htc); }
    		</style>
  		<![endif]-->');
		
		if ($params->get('show_rating')) 
		{
			if ($params->get('show_rating_type') == 0) 
			{
				$document->addCustomTag('
				<script type="text/javascript">
					//<![CDATA[ 
					window.addEvent("load", function() {
						MooStarRatingImages.defaultImageFolder = "/components/com_easybookreloaded/images";
						var Rating = new MooStarRating({ form: "gbookForm", radios: "gbvote", imageEmpty: "sun_empty.png", imageFull:  "sun_full.png", imageHover: "sun_hover.png", tip: "<em>[VALUE] / [COUNT]</em>", tipTarget: $("easybookvotetip"), tipTargetType: "html"  });
					});
					//]]> 
				</script>');
			}
			elseif ($params->get('show_rating_type') == 1)
			{
				$document->addCustomTag('
				<script type="text/javascript">
					//<![CDATA[ 
					window.addEvent("load", function() {
						MooStarRatingImages.defaultImageFolder = "/components/com_easybookreloaded/images";
						var Rating = new MooStarRating({ form: "gbookForm", radios: "gbvote", imageEmpty: "star_empty.png", imageFull:  "star_full.png", imageHover: "star_hover.png", tip: "<em>[VALUE] / [COUNT]</em>", tipTarget: $("easybookvotetip"), tipTargetType: "html"  });
					});
					//]]> 
				</script>');
			}
			elseif ($params->get('show_rating_type') == 2) 
			{
				$document->addCustomTag('
				<script type="text/javascript">
					//<![CDATA[ 
					window.addEvent("load", function() {
						MooStarRatingImages.defaultImageFolder = "/components/com_easybookreloaded/images";
						var Rating = new MooStarRating({ form: "gbookForm", radios: "gbvote", imageEmpty: "star_boxed_empty.png", imageFull:  "star_boxed_full.png", imageHover: "star_boxed_hover.png", width: 17, tip: "<em>[VALUE] / [COUNT]</em>", tipTarget: $("easybookvotetip"), tipTargetType: "html" });
					});
					//]]> 
				</script>');
			}
		}
		
		// Get data from the model
		$entry = $this->get('Data');
		
		// Get EasyCalcCheck
		$easycalccheck = $this->get('EasyCalcCheck');
		
		// Set IP
		$entry->ip 	= getenv('REMOTE_ADDR');
		
		// Set the document page title
		switch($task)
		{
			case 'add':
				$heading = $document->getTitle()." - ".JTEXT::_('SIGN_GUESTBOOK');
				break;
			case 'edit' OR 'edit_mail':
				$heading = $document->getTitle()." - ".JTEXT::_('EDIT_ENTRY');
				break;
			case 'comment' OR 'comment_mail':
				$heading = $document->getTitle()." - ".JTEXT::_('EDIT_COMMENT');
				break;
		}
		
		$this->assignRef('heading',	$heading);
		$this->assignRef('entry', $entry);
		$this->assignRef('params', $params);
		$this->assignRef('session', $session);
		$this->assignRef('user', $user);
		
		parent::display($tpl);
	}
} ?>