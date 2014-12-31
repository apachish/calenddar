<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnLastvisitdateFieldHelper
{
	public static function create($alias)
	{
		
	}
	
	public static function delete($alias)
	{
		
	}
	
	public static function getXml($item)
	{
		require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
		$xml='';
		$hideTitleInProfile=$item->params->get('hidetitle',0);
		if(JFactory::getApplication()->isSite())
			$xml='
				<field
					name="lastvisitdate"
					type="lastvisitdate"
					class="readonly"
					label="'.($hideTitleInProfile ? '' : JsnHelper::xmlentities($item->title)).'"
					description=""
					readonly="true"
					format="%Y-%m-%d %H:%M:%S"
					size="22"
					filter="user_utc"
				/>
			';
		return $xml;
	}
	
	public static function loadData($field, $user, &$data)
	{
		$data->lastvisitdate=$data->lastvisitDate;
	}
	
	public static function storeData($field, $data, &$storeData)
	{
		
	}
	
	public static function lastvisitdate($field)
	{
		$value=$field->__get('value');
		if (empty($value))
		{
			return JHtml::_('users.value', $value);
		}
		else
		{
			if ($value != '0000-00-00 00:00:00')
				return JHtml::_('date', $value);
			else
				return JText::_('COM_JSN_NEVER');
		}
	}
	
	public static function getSearchInput($field)
	{
		$doc=JFactory::getDocument();
		$doc->addScript(JURI::root().'components/com_jsn/assets/js/bootstrap-datepicker.js');
		$doc->addStylesheet(JURI::root().'components/com_jsn/assets/css/datepicker.css'); 
		$return=array();
		$return[]='<input id="jform_'.str_replace('-','_',$field->alias).'" type="hidden" name="'.$field->alias.'" value="1" /><div class="row-fluid"><div class="span4">';
		$return[]='<div data-date-viewmode="days" data-date-format="yyyy-mm-dd" data-date="'.JRequest::getVar($field->alias.'_from','').'" id="'.$field->alias.'_from" class="input-prepend date bsdate"><span class="add-on"><i class="icon-calendar"></i></span><span class="add-on"><i class="icon-remove"></i></span><input placeholder="'.JText::_('COM_JSN_STARTDATE').'" type="text" readonly="readonly" value="'.JRequest::getVar($field->alias.'_from','').'" name="'.$field->alias.'_from" /></div>';
		$return[]='</div><div class="span4">';
		$return[]='<div data-date-viewmode="days" data-date-format="yyyy-mm-dd" data-date="'.JRequest::getVar($field->alias.'_to','').'" id="'.$field->alias.'_to" class="input-prepend date bsdate"><span class="add-on"><i class="icon-calendar"></i></span><span class="add-on"><i class="icon-remove"></i></span><input placeholder="'.JText::_('COM_JSN_ENDDATE').'" type="text" readonly="readonly" value="'.JRequest::getVar($field->alias.'_to','').'" name="'.$field->alias.'_to" /></div>';
		$return[]='</div><div class="span4"><span class="label label-default">'.JText::_('COM_JSN_CHOOSEDATEINTERVAL').'</span></div></div>';
		$script='
		jQuery(document).ready(function($){
			var nowTemp = new Date();
			var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
			$("#'.$field->alias.'_from").datepicker();
			$("#'.$field->alias.'_to").datepicker();
			
			
			$("#'.$field->alias.'_from .icon-remove").click(function(){
				'.$field->alias.'_from.setValue(newDate);
				$("#'.$field->alias.'_from input").val("");
				return false;
			});
			$("#'.$field->alias.'_to .icon-remove").click(function(){
				'.$field->alias.'_to.setValue(newDate);
				$("#'.$field->alias.'_to").attr("data-date","");
				$("#'.$field->alias.'_to input").val("");
				return false;
			});
		});
		';
		$doc->addScriptDeclaration( $script );
		return implode('',$return);
		
		//$return='<input type="hidden" name="'.$field->alias.'" value="1" /><div class="row-fluid"><div class="span4">'.JHtml::_('calendar',JRequest::getVar($field->alias.'_from',''),$field->alias.'_from',$field->alias.'_from','%Y-%m-%d',array('placeholder'=>JText::_('COM_JSN_STARTDATE'))).'</div><div class="span4">'.JHtml::_('calendar',JRequest::getVar($field->alias.'_to',''),$field->alias.'_to',$field->alias.'_to','%Y-%m-%d',array('placeholder'=>JText::_('COM_JSN_ENDDATE'))).'</div><div class="span4"><span class="label label-default">'.JText::_('COM_JSN_CHOOSEDATEINTERVAL').'</span></div></div>';
		//return $return;
	}
	
	public static function getSearchQuery($field, &$query)
	{
		$date_from=new JDate(JRequest::getVar($field->alias.'_from',''));
		$date_to=new JDate(JRequest::getVar($field->alias.'_to',''));
		$db=JFactory::getDbo();
		//$from=JDate::getInstance((JRequest::getVar($field->alias.'_from','')=='' ? '0000-00-00' : $date_from->toSql()));
		//$to=JDate::getInstance((JRequest::getVar($field->alias.'_to','')=='' ? '9999-00-00' : $date_to->toSql()));
		if(JRequest::getVar($field->alias.'_from','')!='') $query->where('a.'.$db->quoteName('lastvisitDate').' > '.$db->quote($date_from->toSql()));
		if(JRequest::getVar($field->alias.'_to','')!='') $query->where('a.'.$db->quoteName('lastvisitDate').' < '.$db->quote($date_to->toSql()));
	}

}
