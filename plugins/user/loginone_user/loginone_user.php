<?php
// Login One! user plug-in for Joomla! 3.x
// $Id: loginone_user.php 23-09-2014 00:00Z robj $
// (C)2011-2014 INNATO BV - www.innato.nl
// @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
// See file LICENSE
// **************************************************************************
// A plugin that prevents multiple logins of the same user
// Released under the GNU/GPLv3 License
// **************************************************************************

// This plugin is a modified version of the Joomla! user plugin.

/**
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

// get joomla version
jimport( 'joomla.version' );
$jversion = new JVersion();

// include class
if ( $jversion->getShortVersion() < "3.2.0" ) {
	@include JPATH_SITE.'/plugins/user/loginone_user/classes/3.1.6.php';
}
elseif( $jversion->getShortVersion() == "3.2.0" ) {
	@include JPATH_SITE.'/plugins/user/loginone_user/classes/3.2.0.php';
}
elseif( $jversion->getShortVersion() == "3.2.1" ) {
	@include JPATH_SITE.'/plugins/user/loginone_user/classes/3.2.1.php';
}
elseif( $jversion->getShortVersion() == "3.2.2" ) {
	@include JPATH_SITE.'/plugins/user/loginone_user/classes/3.2.2.php';
}
elseif( $jversion->getShortVersion() >= "3.2.3" && $jversion->getShortVersion() <= "3.3.1" ) {
	//	include this when jversion >= 3.2.3 and <= 3.3.1
	@include JPATH_SITE.'/plugins/user/loginone_user/classes/3.2.3.php';
}
elseif( $jversion->getShortVersion() >= "3.3.2" && $jversion->getShortVersion() <= "3.3.3" ) {
	//	include this when jversion >= 3.3.2 and <= 3.3.3
	@include JPATH_SITE.'/plugins/user/loginone_user/classes/3.3.2.php';
}
else {
	//	include this when jversion >= 3.3.4
	@include JPATH_SITE.'/plugins/user/loginone_user/classes/3.3.4.php';
}
