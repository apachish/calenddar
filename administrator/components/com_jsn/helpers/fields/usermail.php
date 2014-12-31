<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnUsermailFieldHelper
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
			$hideTitleInProfile=$item->params->get('hidetitle',0);//(isset($item->params['hidetitle']) && $item->params['hidetitle'] && JRequest::getVar('option')=='com_jsn' ? true : false);
			$placeholder=($item->params->get('usermail_placeholder','')!='' ? 'hint="'.JsnHelper::xmlentities($item->params->get('usermail_placeholder','')).'"' : '');
			$placeholder2=($item->params->get('usermail_placeholder2','')!='' ? 'hint="'.JsnHelper::xmlentities($item->params->get('usermail_placeholder2','')).'"' : '');
			$xml.='
				<field name="email1" type="email"
					description="COM_USERS_PROFILE_EMAIL1_DESC"
					filter="string"
					label="'.($hideTitleInProfile ? '' : JsnHelper::xmlentities($item->title)).'"
					message="COM_USERS_PROFILE_EMAIL1_MESSAGE"
					required="true"
					size="30"
					unique="true"
					validate="email"
					'.$placeholder.'
				/>

				
			';
			$config = JComponentHelper::getParams('com_jsn');
			if($config->get('confirmusermail',0))
			{
				$xml.='
					<field name="email2" type="email"
						description="COM_USERS_PROFILE_EMAIL2_DESC"
						field="email1"
						filter="string"
						label="COM_USERS_PROFILE_EMAIL2_LABEL"
						message="COM_USERS_PROFILE_EMAIL2_MESSAGE"
						required="true"
						size="30"
						validate="equals"
						'.$placeholder2.'
					/>
				';
			}
		}
		else{
			$placeholder=($item->params->get('usermail_placeholder','')!='' ? 'hint="'.JsnHelper::xmlentities($item->params->get('usermail_placeholder','')).'"' : '');
			$xml.='
				<field name="email" type="email"
					class="inputbox"
					description="COM_USERS_USER_FIELD_EMAIL_DESC"
					label="JGLOBAL_EMAIL"
					required="true"
					size="30"
					validate="email"
					'.$placeholder.'
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
		$query->where('a.'.$db->quoteName('email').' LIKE '.$db->quote('%'.JRequest::getVar('email',null).'%'));
	}
	
	public static function usermail($field)
	{
		$value=$field->__get('value');
		if (empty($value))
		{
			return JHtml::_('users.value', $value);
		}
		else
		{
			JPluginHelper::importPlugin('content');
			return JHtml::_('content.prepare', '<a href="mailto:'.$value.'">'.$value.'</a>', '', 'jsn_content.content');
		}
	}

}
