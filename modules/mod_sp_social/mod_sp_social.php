<?php
	/*
		# mod_sp_social - Social module by JoomShaper.com
		# Author    	JoomShaper http://www.joomshaper.com
		# Copyright 	2010 - 2013 JoomShaper.com. All Rights Reserved.
		# License	 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Website 		http://www.joomshaper.com
	*/

    // no direct access
    defined('_JEXEC') or die('Restricted access');
    //Parameters
	$rss 			= $params->get('rss');
	$facebook 		= $params->get('facebook');
	$twitter 		= $params->get('twitter');
	$goggleplus 	= $params->get('goggleplus');
	$youtube 		= $params->get('youtube');
	$pinterest 		= $params->get('pinterest');
	$linkedin 		= $params->get('linkedin');
	$flickr 		= $params->get('flickr');
	$picasa 		= $params->get('picasa');
	$myspace 		= $params->get('myspace');
	$digg 			= $params->get('digg');
	$skype 			= $params->get('skype');
	$vimeo 			= $params->get('vimeo');
	$delicious 		= $params->get('delicious');
	
	$preset 		= $params->get('preset');
	$icon_size 		= $params->get('icon_size');
	$target 		= $params->get('target');
	$custom_text 	= ($params->get('custom_text')!='') ? '<strong>' . $params->get('custom_text') . '</strong>': '';
	
	
    $ID 				= $module->id;
    $document 			= JFactory::getDocument();
	require(JModuleHelper::getLayoutPath(basename(dirname(__FILE__)))); 
    $document->addStylesheet(JURI::base(true) . '/modules/'.basename(dirname(__FILE__)).'/assets/css/' . $preset . '/sp_social.css');