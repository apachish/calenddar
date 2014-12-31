<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnCoreFieldHelper
{
	public static function create($alias)
	{
		
	}
	
	public static function delete($alias)
	{
		
	}
	
	public static function getXml($item = null)
	{
		$xml='';
		if(JFactory::getApplication()->isAdmin())
		{
			$xml='
				<field name="name" type="hidden"
					class="inputbox"
					default="formatName"
				/>
				<field name="spconnect" label="&lt;h4&gt;SocialConnect&lt;/h4&gt;" type="spacer" />
				<field name="facebook_id" type="text"
					class="inputbox"
					default=""
					label="Facebook ID"
				/>
				<field name="twitter_id" type="text"
					class="inputbox"
					default=""
					label="Twitter ID"
				/>
				<field name="google_id" type="text"
					class="inputbox"
					default=""
					label="Google Plus ID"
				/>
				<field name="linkedin_id" type="text"
					class="inputbox"
					default=""
					label="LinkedIn ID"
				/>
			';
		}
		elseif(JRequest::getVar('view')=='registration')
		{
			$xml='
				<field name="name" type="hidden"
					class="inputbox"
					default="formatName"
				/>
			';
		}
		else
		{
			$xml='
				<field name="name" type="hidden"
					class="inputbox"
				/>
				<field name="id" type="hidden"
					filter="integer"
				/>
				<field name="twofactor" type="hidden" default="none" />
			';
		}
		return $xml;
	}
	

}
