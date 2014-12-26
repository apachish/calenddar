<?php
// Login One! user plugin for Joomla! 3.x
// $Id: script.php 01-10-2014 00:00Z robj $
// (C)2011-2014 INNATO BV - www.innato.nl
// **************************************************************************
// A plugin that prevents multiple log-ins of the same user
// License: see file LICENSE
// @authorcode	gAk9geprut4uCaprEfrazemasPEWec3e
// @filecode	
// **************************************************************************

// no direct access
defined('_JEXEC') or die('Restricted access');
 
//the name of the class must be "plgFolder" plus the name of your plug-in + InstallerScript
//for example: plgSystemjhackerwatchInstallerScript
class plgUserloginone_userInstallerScript {
 	/*
	 * The release value to be displayed and checked against throughout this file.
	 */
	private $release = '1.0';
 
	/*
	 * Find mimimum required joomla version for this extension. It will be read from the version attribute (install tag) in the manifest file
	 * Also set the maximum joomla version. only the specified version positions will be checked
	 */
        private $minimum_joomla_release = '3.0';    
        private $maximum_joomla_release = '3.3.6';	// check only specified digits (positions)
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		// this component does not work with Joomla releases prior to 1.6
		// abort if the current Joomla release is older
		$jversion = new JVersion();
 
		// Extract the version number from the manifest. This will overwrite the 1.0 value set above 
		$this->release=$parent->get("manifest")->version;

		// Find mimimum required joomla version
		$this->minimum_joomla_release=$parent->get("manifest")->attributes()->version;    
 
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install Login One! plug-in in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
 
		// added by innato
		if( ($type == "install" || $type == "update") && version_compare( substr($jversion->getShortVersion(), 0, strlen($this->maximum_joomla_release)), $this->maximum_joomla_release, 'gt' ) ) {
			Jerror::raiseWarning(null, 'Login One! plug-in '.$type.' aborted, because Joomla! version is later than '.$this->maximum_joomla_release.' and the plug-in version may be incompatible. Please use a more recent version of the software package.');
			return false;
		}
 
		// abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'lt' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot downgrade ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }
 
//		echo JText::_('Login One! pre-flight ' . $type . ' ' . $rel).'<br />';
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
		echo '<p>' . JText::_('Login One! user plug-in install ' . $this->release) . '</p>';
		// You can have the backend jump directly to the newly installed component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		echo JText::_('Login One! user plug-in update ' . $this->release).'<br />';
	}		
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		// always create or modify these parameters
		$params['my_param0'] = 'Plugin version ' . $this->release;
		$params['my_param1'] = 'Another value';
 
		// define the following parameters only if it is an original install
		if ( $type == 'install' ) {
			$params['my_param2'] = '4';
			$params['my_param3'] = 'Star';
		}
 
		$this->setParams( $params );
 
//		echo '<p>' . JText::_('PLG_LOGINONE_POSTFLIGHT ' . $type . ' to ' . $this->release) . '</p>';
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
//		echo '<p>' . JText::_('PLG_LOGINONE_UNINSTALL ' . $this->release) . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE type = "plugin" AND element = "loginone_user" AND folder = "user"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE type = "plugin" AND element = "loginone_user" AND folder = "user"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE type = "plugin" AND element = "loginone_user" AND folder = "user"' );
				$db->query();
		}
	}
}