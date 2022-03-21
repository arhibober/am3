<?php
 /**
 * Name:			Easybook Reloaded 3 for Joomla! 1.6
 * Based on: 		Easybook by http://www.easy-joomla.org
 * License:    		GNU/GPL
 * Project Page: 	http://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

 /**
 * Easybook Model
 *
 * @package    Easybook
 */
class EasybookReloadedModelEasybookReloaded extends JModel
{
	 /**
	 * Easybook entry array
	 *
	 * @var array
	 */
	var $_entry;
	var $_total;
	var $_pagination;
	var $_version;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		$config = JFactory::getConfig();

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( 'easybookreloaded.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);		
	}
	
	 /**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
		$query = "SELECT * FROM #__easybook"
		. " ORDER BY gbdate DESC";
		
		return $query;
	}
	
	 /**
	 * Retrieves the GUESTBOOK_ENTRYs
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	function getPagination()
	{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	 /**
	 * Retrieves the count of GUESTBOOK_ENTRYs
	 * @return array Array of objects containing the data from the database
	 */
	function getTotal()
	{
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}
	
	function getVersion()
	{
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'version.php' );
		
		if (empty($this->_version)) {
			$this->_version = new EasybookReloadedHelperVersion();
	    }
		return $this->_version;
	}
}
