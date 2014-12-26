<?php
defined('_JEXEC') or die();

/*------------------------------------------------------------------------
# News Show SP2 - News display/Slider module by JoomShaper.com
# ------------------------------------------------------------------------
# Author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - GNU/GPL V2 for PHP files. CSS / JS are Copyrighted Commercial
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/
if (JFile::exists(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php')) {
if (!class_exists('VmConfig'))
    require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');

if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . '/helpers/shopfunctions.php');
if (!class_exists('TableCategories'))
    require(JPATH_VM_ADMINISTRATOR . '/tables/categories.php');

if (!class_exists('VmElements'))
    require(JPATH_VM_ADMINISTRATOR . '/elements/vmelements.php');
/*
 * This element is used by the menu manager
 * Should be that way
 */
class VmElementVmCategories extends VmElements {

    var $type = 'vmcategories';

    // This line is required to keep Joomla! 1.6/1.7 from complaining
    function getInput() {
        $key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
        $val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
        JPlugin::loadLanguage('com_virtuemart', JPATH_ADMINISTRATOR);
        $categorylist = ShopFunctions::categoryListTree(array($this->value));
        $class = '';
        $html = '<select class="inputbox" name="' . $this->name . '" >';
        $html .= '<option value="0">' . JText::_('COM_VIRTUEMART_CATEGORY_FORM_TOP_LEVEL') . '</option>';
        $html .= $categorylist;
        $html .="</select>";
        return $html;
    }

    function fetchElement($name, $value, &$node, $control_name) {
        JPlugin::loadLanguage('com_virtuemart', JPATH_ADMINISTRATOR);
        $categorylist = ShopFunctions::categoryListTree(array($value));

        $class = '';
        $html = '<select class="inputbox" name="' . $control_name . '[' . $name . ']' . '" >';
        $html .= '<option value="0">' . JText::_('COM_VIRTUEMART_CATEGORY_FORM_TOP_LEVEL') . '</option>';
        $html .= $categorylist;
        $html .="</select>";
        return $html;
    }

}


if (JVM_VERSION === 2 ) {

    class JFormFieldVmCategories extends VmElementVmCategories {

    }

} else {

    class JElementVmCategories extends VmElementVmCategories {

    }

}
}