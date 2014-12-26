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
/*
 * Define constants for all pages
 */
define( 'COM_ADDUSERFRONTEND_DIR', 'images'.DIRECTORY_SEPARATOR.'adduserfrontend'.DS );
define( 'COM_ADDUSERFRONTEND_BASE', JPATH_ROOT.DIRECTORY_SEPARATOR.COM_ADDUSERFRONTEND_DIR );
define( 'COM_ADDUSERFRONTEND_BASEURL', JURI::root().str_replace( DS, '/', COM_ADDUSERFRONTEND_DIR ));
// Require the base controller
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'controller.php';
// Require the base controller
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';
// Initialize the controller
$controller = new AdduserfrontendController( );
// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
$controller->redirect();
?>