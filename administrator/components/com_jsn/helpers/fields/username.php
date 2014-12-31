<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnUsernameFieldHelper
{
	public static function create($alias)
	{
		
	}
	
	public static function delete($alias)
	{
		
	}
	
	public static function getXml($item)
	{
		$xml='';
		if(JFactory::getApplication()->isSite())
		{
			require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
			$hideTitleInProfile=$item->params->get('hidetitle',0);
			$placeholder=($item->params->get('username_placeholder','')!='' ? 'hint="'.JsnHelper::xmlentities($item->params->get('username_placeholder','')).'"' : '');
			$xml='
				<field name="username" type="text"
					class="validate-username"
					description="COM_USERS_DESIRED_USERNAME"
					filter="username"
					label="'.($hideTitleInProfile ? '' : JsnHelper::xmlentities($item->title)).'"
					message="COM_USERS_PROFILE_USERNAME_MESSAGE"
					required="true"
					'.$placeholder.'
					size="30"
					validate="username"
				/>
			';
		}
		return $xml;
	}
	
	public static function loadData($field, $user, &$data)
	{
		
	}
	
	public static function storeData($field, $data, &$storeData)
	{
		
	}
	
	public static function getSearchInput($field)
	{
		$return='<input id="jform_'.str_replace('-','_',$field->alias).'" type="text" placeholder="'.JText::_('COM_JSN_SEARCHFOR').' '.JText::_($field->title).'..." name="'.$field->alias.'" value="'.JRequest::getVar($field->alias,'').'"/>';
		return $return;
	}
	
	public static function getSearchQuery($field, &$query)
	{
		$db=JFactory::getDbo();
		$query->where('a.'.$db->quoteName('username').' LIKE '.$db->quote('%'.JRequest::getVar('username',null).'%'));
	}

}
