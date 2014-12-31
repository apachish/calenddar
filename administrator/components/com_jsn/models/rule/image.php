<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('JPATH_PLATFORM') or die;

/**
 * Form Rule class for the Joomla Platform.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormRuleImage extends JFormRule
{
	/**
	 * Method to test an external url for a valid parts.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   JForm             $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   11.1
	 * @link    http://www.w3.org/Addressing/URL/url-spec.txt
	 * @see	    JString
	 */
	public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['requiredfile'] == 'true' || (string) $element['requiredfile'] == 'required');
		
		$validate=false;
		if(isset($_FILES['jform']['name'][(string) $element['name']]) && strlen($_FILES['jform']['name'][(string) $element['name']])>4)
		{
			$filename=$_FILES['jform']['name'][(string) $element['name']];
			$ext = strtolower($filename[strlen($filename)-4].$filename[strlen($filename)-3].$filename[strlen($filename)-2].$filename[strlen($filename)-1]);
			if ($ext[0] == '.') $ext = substr($ext, 1, 3);
			if (!in_array($ext,  explode('|', 'jpg|png|jpeg|gif|bmp') )) return false;
			$validate=true;
		}
		if(isset($_SESSION['_tmp_img'.$element['id']])){
			$validate=true;
		}
		if(!$required) return true;
		
		$form=JRequest::getVar('jform',false);
		if($form && isset($form['id']))
		{
			$db=JFactory::getDbo();
			$query=$db->getQuery(true);
			$query->select($db->quoteName((string) $element['id']))->from('#__jsn_users')->where('id = '. (int) $form['id']);
			$db->setQuery($query);
			$checkValue=$db->loadResult();
			if(!empty($checkValue)) $validate=true;
		}
		
		if($validate) return true;
		else return false;
	}
}
