<?php
/*------------------------------------------------------------------------
# mod_sp_simple_youtube - Youtube Module by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
//Parameters
$doc 					= JFactory::getDocument();
$uniqid 				= $module->id;
$youtube_id				= $params->get ('youtube_id');
$width					= $params->get ('width',300);
$height					= $params->get ('height',225);

if (!defined('_SPSYT')) {
	define ('_SPSYT',1);
	$doc->AddScriptdeclaration("function  spsyt(a, b, c){
		var id = document.id(a);
		var w = id.getParent().getWidth();
		var h = (c*w)/b;
		id.setStyles({
			'width': w,
			'height': h
		});
	}");
}

require(JModuleHelper::getLayoutPath('mod_sp_simple_youtube'));