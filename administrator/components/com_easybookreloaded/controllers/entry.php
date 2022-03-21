<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

 /**
 * Easybook Reloaded Component Controller
 *
 * @package    Easybook Reloaded
 */
class EasybookReloadedControllerEntry extends JController
{
	var $_access = null;

	 /**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
    	parent::__construct();

    	// Register Extra tasks
    	$this->registerTask( 'add' , 'edit' );
	}

	function edit()
	{
	    JRequest::setVar( 'view', 'entry' );
	    JRequest::setVar( 'layout', 'form'  );
	    JRequest::setVar('hidemainmenu', 1);
	
	    parent::display();
	}

	 /**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel( 'entry' );
	
		if ($model->store()) {
        	$msg = JText::_( 'ENTRY_SAVED' );
  			$type = 'message';
	    } else {
	        $msg = JText::_( 'ERROR_SAVING_ENTRY' );
	        $type = 'error';
	    }
			
		$this->setRedirect( 'index.php?option=com_easybookreloaded', $msg, $type );
	}

	 /**
	 * remove record
	 * @return void
	 */
	function remove()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		// Load model and DELETE_ENTRY - redirect afterwards
		$model = $this->getModel( 'entry' );
		if (!$model->delete()) {
			$msg = JText::_( 'ERROR_ENTRY_COULD_NOT_BE_DELETED' );
			$type = 'error';
		} else {
			$msg = JText::_( 'ENTRY_DELETED' );
			$type = 'message';
		}
		$this->setRedirect( JRoute::_( 'index.php?option=com_easybookreloaded', false ), $msg, $type );
	}
	
	
	function cancel()
	{
	    $msg = JText::_( 'OPERATION_CANCELLED' );
	    $this->setRedirect( 'index.php?option=com_easybookreloaded', $msg, 'notice' );
	}
	
	function publish() 
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel( 'entry' );
		if ($model->publish(1)) {
			$msg = JText::_( 'ENTRY_PUBLISHED' );
			$type = 'message';
		} else {
			$msg = JText::_( 'ERROR_COULD_NOT_CHANGE_PUBLISH_STATUS' )." - " .$model->getError();
			$type = 'error';
		}
		$this->setRedirect( JRoute::_( 'index.php?option=com_easybookreloaded', false ), $msg, $type );
	}
 	
 	function unpublish() 
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel( 'entry' );
		if ($model->publish(0)) {
			$msg = JText::_( 'ENTRY_UNPUBLISHED' );
			$type = 'message';
		} else {
			$msg = JText::_( 'ERROR_COULD_NOT_CHANGE_PUBLISH_STATUS' )." - " .$model->getError();
			$type = 'error';
		}
		$this->setRedirect( JRoute::_( 'index.php?option=com_easybookreloaded', false ), $msg, $type );
	}

}
?>
