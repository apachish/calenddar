/*---------------------------------------------------------------
# SP Tab - Next generation tab module for joomla
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/
jQuery(document).ready(function(){
	showhide();
	jQuery('#jform_params_tab_style,#jform_params_body_height').change(function() {showhide()});
	jQuery('#jform_params_tab_style,#jform_params_body_height').blur(function() {showhide()});
	
	function showhide(){
		if (jQuery("#jform_params_tab_style").val()=="raw" || jQuery("#jform_params_tab_style").val()=="custom") {
			jQuery("#jform_params_color").parent().parent().css("display", "none");
		} else {
			jQuery("#jform_params_color").parent().parent().css("display", "block");		
		}	
		if (jQuery("#jform_params_tab_style").val()=="raw" || jQuery("#jform_params_tab_style").val()!="custom") {
			jQuery('.sp-input').parent().parent().css("display", "none");
		} else {
			jQuery('.sp-input').parent().parent().css("display", "block");		
		}
		if (jQuery("#jform_params_body_height").val()=="1") {
			jQuery("#jform_params_fixed_height").parent().parent().css("display", "none");
		} else {
			jQuery("#jform_params_fixed_height").parent().parent().css("display", "block");		
		}
	}
});