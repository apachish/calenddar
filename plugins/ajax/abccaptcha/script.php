<?php
/**
 * Author     Hadi Pourabbas | info@abccom.biz | http://www.abccom.biz
 * Copyright  Copyright (C) 2013 Alpabet of Communications Co. All Rights Reserved.
 * License    GNU GPL v3 or later
 */
 
 // No direct access to this file 
defined('_JEXEC') or die('Restricted access');

class PlgAjaxABCcaptchaInstallerScript
{

	function install($parent) 
	{
		
	}

	function uninstall($parent) 
	{
		
	}

	function update($parent) 
	{
		
	}

	function preflight($type, $parent) 
	{
	
	}

	function postflight($type, $parent) 
	{
		// activate plugin
		$db = JFactory::getDbo();
		$db->getQuery(true);
		$query = 'UPDATE #__extensions SET enabled = 1 WHERE name = ' . $db->Quote('plg_ajax_abccaptcha');
		$db->setQuery($query);
		$db->query();
	}

}