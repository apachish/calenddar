<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class com_JsnInstallerScript {
	
	

	/**
	* Method to install the extension
	* $parent is the class calling this method
	*
	* @return void
	*/
	function install($parent) 
	{
		
	}

	/**
	* Method to uninstall the extension
	* $parent is the class calling this method
	*
	* @return void
	*/
	function uninstall($parent) 
	{

	}

	/**
	* Method to update the extension
	* $parent is the class calling this method
	*
	* @return void
	*/
	function update($parent) 
	{
		
	}

	/**
	* Method to run before an install/update/uninstall method
	* $parent is the class calling this method
	* $type is the type of change (install, update or discover_install)
	*
	* @return void
	*/
	function preflight($type, $parent) 
	{

	}

	/**
	* Method to run after an install/update/uninstall method
	* $parent is the class calling this method
	* $type is the type of change (install, update or discover_install)
	*
	* @return void
	*/
	function postflight($type, $parent) 
	{
		$app = JFactory::getApplication();

		$app->redirect('index.php?option=com_jsn&task='.$type);
	}
}
?>
