<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnPasswordFieldHelper
{
	public static function create($alias)
	{
		
	}
	
	public static function delete($alias)
	{
		
	}
	
	public static function getXml($item)
	{
		
		//echo($configUsers->get())
		$version=new JVersion();
		$xml='';
		if(JFactory::getApplication()->isSite())
		{
			$configUsers=JComponentHelper::getParams('com_users');
			$configJsn=JComponentHelper::getParams('com_jsn');
			if(JRequest::getVar('view')=='registration') $required='required="true"';
			else $required='';
			if($configJsn->get('passwordstrengthmeter',0)) $strengthmeter='strengthmeter="true"';
			else $strengthmeter='';
			$placeholder=($item->params->get('password_placeholder','')!='' ? 'hint="'.JsnHelper::xmlentities($item->params->get('password_placeholder','')).'"' : '');
			$placeholder2=($item->params->get('password_placeholder2','')!='' ? 'hint="'.JsnHelper::xmlentities($item->params->get('password_placeholder2','')).'"' : '');
			$xml.='
				<field name="password1" type="password"
					autocomplete="off"
					class="validate-password"
					description="COM_USERS_DESIRED_PASSWORD"
					field="password2"
					filter="raw"
					label="COM_JSN_PROFILE_PASSWORD1_LABEL"
					message="COM_USERS_PROFILE_PASSWORD1_MESSAGE"
					size="30"
					minimum_length="'.$configUsers->get('minimum_length',4).'"
					minimum_integers="'.$configUsers->get('minimum_integers',0).'"
					minimum_symbols="'.$configUsers->get('minimum_symbols',0).'"
					minimum_uppercase="'.$configUsers->get('minimum_uppercase',0).'"
					'.($version->RELEASE=='3.0' ? 'validate="equals"' : 'validate="password"').'
					'.$required.'
					'.$placeholder.'
					'.$strengthmeter.'
				/>

				<field name="password2" type="password"
					autocomplete="off"
					'.($version->RELEASE=='3.0' ? '' : 'validate="equals"').'
					field="password1"
					class="validate-password"
					description="COM_USERS_PROFILE_PASSWORD2_DESC"
					filter="raw"
					label="COM_JSN_PROFILE_PASSWORD2_LABEL"
					size="30"
					'.$required.'
					'.$placeholder2.'
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

}
