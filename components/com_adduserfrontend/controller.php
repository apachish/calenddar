<?php
/**
 * Joomla! 3.0 component Add user Frontend
 *
 * @version $Id: controller.php 2014-08-24 23:00:13 svn $
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
jimport('joomla.application.component.controller');
/**
 * Add user Frontend Component Controller
 */
class AdduserfrontendController extends JControllerLegacy 
   { public function __construct($config = array())
	{
		parent::__construct($config);
		// This conditional lets the ACL check run only on Joomla! 1.6+
		if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
			$user = JFactory::getUser();
			if (!$user->authorise('adduserfrontend.createuser', 'com_adduserfrontend')) {
				return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}
		// Place anything else you need in the constructor below this line
	}
    /**
     * Constructor
     * @access public
     * @subpackage Add user Frontend
     */
   function display() {
        // Make sure we have a default view
        if( !JRequest::getVar( 'view' )) {
		    JRequest::setVar('view', 'adduserfrontend' );
        }
		parent::display();
	}

}
?>