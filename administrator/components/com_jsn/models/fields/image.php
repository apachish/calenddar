<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;




JFormHelper::loadFieldClass('file');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       3.1
 */
class JFormFieldImage extends JFormFieldFile
{
	public $type = 'Image';
	
	protected function getInput()
	{
		//$upload_dir='images/profiler/';
		$this->element['class']='';
		$attribs=array(
			'style' => 'float:left;width:50px;margin-right:10px;border-radius:2px;'
		);
		$html=array();
		// Check Mobile
		$iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
		$palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
		$berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
		$ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
		$windowsphone = strpos($_SERVER['HTTP_USER_AGENT'],"Windows Phone");
		if($windowsphone || $iphone || $android || $palmpre || $ipod || $berry == true) $mobile=true;
		else $mobile=false;
		
		if(JFactory::getApplication()->isAdmin() || $mobile || !(isset($this->element['cropwebcam']) && $this->element['cropwebcam']=='1'))
		{
			//if($this->value && file_exists(JPATH_SITE.'/'.str_replace('_', 'mini_', $this->value))) $html[]=JHtml::_('image', str_replace('_', 'mini_', $this->value) ,$this->element['alt'],$attribs);
			if($this->element['name']=='avatar') $img_src='components/com_jsn/assets/img/default.jpg';
			else $img_src='components/com_jsn/assets/img/no_image.gif';
			if(!empty($this->value) && !strpos($this->value,'/profiler/')) $img_src=$this->value;
			elseif(!empty($this->value)) {$img_src=str_replace('_', 'mini_', $this->value);}
			if($img_src && file_exists(JPATH_SITE.'/'.$img_src)) $html[]=JHtml::_('image', $img_src ,$this->element['alt'],$attribs);
			$html[]=str_replace('required','',parent::getInput());
		}
		if($this->element['required']!='true' && $this->value && file_exists(JPATH_SITE.'/'.str_replace('_', 'mini_', $this->value))) $html[]='<div class="checkbox" style="clear:both;"><input type="checkbox" name="'.str_replace(']', '_delete]', $this->name).'"/>'.JText::_('COM_JSN_DELETE_IMAGE').'</div>';
		return implode('', $html);
		
	}
	
	public function getImage()
	{
		$attribs=array(
			'class' => 'avatar '.$this->element['imageclass']
		);
		$html=array();
		
		if($this->value) $html[]=JHtml::_('image', $this->value,$this->element['alt'],$attribs);
		return implode('', $html);
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	
}
