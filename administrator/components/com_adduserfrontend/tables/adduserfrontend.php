<?php
/**
 * Joomla! 3.0 component Add user Frontend
 *
 * @version $Id: adduserfrontend.php 2014-08-24 23:00:13 svn $
 * @author Kim Pittoors
 * @package Joomla
 * @subpackage Add user Frontend
 * @license GNU/GPL
 *
 * Add users to Community builder on the frontend
 *
* @Copyright Copyright (C) 2009 - 2014 - Kim Pittoors - www.joomlacy.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html 
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
// Include library dependencies
jimport('joomla.filter.input');
/**
* Table class
*
* @package          Joomla
* @subpackage		Add user Frontend
*/
class TableItem extends JTable {
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
    /**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function __construct(& $db) {
		parent::__construct('#__adduserfrontend', 'id', $db);
	}
	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	function check() {
		return true;
	}
}
?>