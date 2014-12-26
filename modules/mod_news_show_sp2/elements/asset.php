<?php
/*------------------------------------------------------------------------
# News Show SP2 - News display/Slider module by JoomShaper.com
# ------------------------------------------------------------------------
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - GNU/GPL V2 for PHP files. CSS / JS are Copyrighted Commercial
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.form.formfield');
class JFormFieldAsset extends JFormField
{
	protected	$type = 'Asset';
	
	protected function getInput() {
		$doc = JFactory::getDocument();
		if (JVERSION<3) {
			$doc->addScript(JURI::root(true).'/modules/mod_news_show_sp2/elements/js/jquery.js');
			$doc->addScript(JURI::root(true).'/modules/mod_news_show_sp2/elements/js/script.js');
			$doc->addStylesheet(JURI::root(true).'/modules/mod_news_show_sp2/elements/css/style.css');
		} else {
			JHtml::_('jquery.framework');
			$doc->addScript(JURI::root(true).'/modules/mod_news_show_sp2/elements/js/script_j3.js');
			$doc->addStylesheet(JURI::root(true).'/modules/mod_news_show_sp2/elements/css/style_j3.css');			
		}
		return null;
	}
}