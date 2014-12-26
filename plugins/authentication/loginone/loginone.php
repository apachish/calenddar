<?php
// Login One! authentication plugin for Joomla! 3.x
// $Id: loginone.php 25-11-2013 00:00Z robj $
// (C)2011-2014 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// See file LICENSE
// **************************************************************************
// A plugin that prevents multiple log-ins of the same user
// Released under the GNU/GPLv3 License
// **************************************************************************


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport( 'joomla.plugin.plugin' );

// get joomla version
jimport( 'joomla.version' );
$jversion = new JVersion();

// include class
if ( version_compare($jversion->getShortVersion(), "3.2.0") < 0 ) {
	@include JPATH_SITE.'/plugins/authentication/loginone/classes/3.1.6.php';
}
elseif( version_compare($jversion->getShortVersion(), "3.2.0") == 0 ) {
	@include JPATH_SITE.'/plugins/authentication/loginone/classes/3.2.0.php';
}
else {
	//	include this when jversion > 3.2.0
	@include JPATH_SITE.'/plugins/authentication/loginone/classes/3.2.1.php';
}
