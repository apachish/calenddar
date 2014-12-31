<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


global $_FIELDTYPES;
$_FIELDTYPES['text']='COM_JSN_FIELDTYPE_TEXT';

class JsnTextFieldHelper
{
	public static function create($alias)
	{
		$db = JFactory::getDbo();
		$query = "ALTER TABLE #__jsn_users ADD ".$db->quoteName($alias)." VARCHAR(255)";
		$db->setQuery($query);
		$db->query();
	}
	
	public static function delete($alias)
	{
		$db = JFactory::getDbo();
		$query = "ALTER TABLE #__jsn_users DROP COLUMN ".$db->quoteName($alias);
		$db->setQuery($query);
		$db->query();
	}
	
	public static function getXml($item)
	{
		require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
		$hideTitleInProfile=$item->params->get('hidetitle',0);
		$defaultvalue=($item->params->get('text_defaultvalue','')!='' ? 'default="'.JsnHelper::xmlentities($item->params->get('text_defaultvalue','')).'"' : '');
		$maxlength=($item->params->get('text_maxlength','')!='' ? 'maxlength="'.$item->params->get('text_maxlength','').'"' : '');
		$placeholder=($item->params->get('text_placeholder','')!='' ? 'hint="'.JsnHelper::xmlentities($item->params->get('text_placeholder','')).'"' : '');
		if($item->params->get('text_regex','')!='custom') $regex=($item->params->get('text_regex','')!='' ? 'validate="regex" regex="'.$item->params->get('text_regex','').'"' : '');
		else $regex='validate="regex" regex="'.$item->params->get('text_customregex','').'"';
		
		if($item->params->get('text_readonly','')==1 && JFactory::getApplication()->isSite()) $readonly='readonly="true"';
		elseif($item->params->get('text_readonly','')==2 && JRequest::getVar('view')!='registration' && JFactory::getApplication()->isSite()) $readonly='readonly="true"';
		else $readonly='';
		
		
		$type='text';
		
		$xml='';
		
		$xml.='
			
			<field
				name="'.$item->alias.'"
				type="'.$type.'"
				id="'.$item->alias.'"
				label="'.($hideTitleInProfile ? '' : JsnHelper::xmlentities($item->title)).'"
				description="'.JsnHelper::xmlentities(($item->description)).'"
				size="30"
				'.$defaultvalue.'
				'.$maxlength.'
				'.$placeholder.'
				'.$regex.'
				'.$readonly.'
				required="'.($item->required ? 'true' : 'false' ).'"
			/>
		';
		return $xml;
	}
	
	public static function loadData($field, $user, &$data)
	{
		$alias=$field->alias;
		if(isset($user->$alias)) $data->$alias=$user->$alias;
	}
	
	public static function storeData($field, $data, &$storeData)
	{
		if($field->params->get('text_readonly','')==1 && JFactory::getApplication()->isSite()) return;
		if($field->params->get('text_readonly','')==2 && JRequest::getVar('view')!='registration.register' && JRequest::getVar('view')!='register' && JFactory::getApplication()->isSite()) return;
		$alias=$field->alias;
		if(isset($data[$alias])) $storeData[$alias]=$data[$alias];
	}
	
	public static function getSearchInput($field)
	{
		$return='<input id="jform_'.str_replace('-','_',$field->alias).'" type="text" placeholder="'.JText::_('COM_JSN_SEARCHFOR').' '.JText::_($field->title).'..." name="'.$field->alias.'" value="'.JRequest::getVar($field->alias,'').'"/>';
		return $return;
	}
	
	public static function getSearchQuery($field, &$query)
	{
		$db=JFactory::getDbo();
		$query->where('b.'.$db->quoteName($field->alias).' LIKE '.$db->quote('%'.JRequest::getVar($field->alias,null).'%'));
	}
		

}
