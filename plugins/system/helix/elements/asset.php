<?php
/*---------------------------------------------------------------
# Package - Helix Framework  
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField
{
	protected	$type = 'Asset';
	
	protected function getInput() {
		$plg_path = JURI::root().'/plugins/system/helix/elements/';	
		
		if($this->element['assettype'] == 'js') {
			JHtml::_('script', $plg_path . $this->element['filename'], false, true);
		} else {
			JHtml::_('stylesheet', $plg_path . $this->element['filename'], false, true);
		}	
	}
}