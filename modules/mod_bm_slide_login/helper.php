<?php
/**
 * @package     Brainymore.com
 * @subpackage  mod_bm_slide_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_bm_slide_login
 *
 * @package     Joomla.Site
 * @subpackage  mod_bm_slide_login
 * @since       1.5
 */
class ModBMSlideLoginHelper
{
	public static function getReturnURL($params, $type)
	{
		$app	= JFactory::getApplication();
		$router = $app->getRouter();
		$url = null;
		if ($itemid = $params->get($type))
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true)
				->select($db->quoteName('link'))
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('published') . '=1')
				->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);
			if ($link = $db->loadResult())
			{
				if ($router->getMode() == JROUTER_MODE_SEF)
				{
					$url = 'index.php?Itemid='.$itemid;
				}
				else {
					$url = $link.'&Itemid='.$itemid;
				}
			}
		}
		if (!$url)
		{
			// Stay on the same page
			$uri = clone JUri::getInstance();
			$vars = $router->parse($uri);
			unset($vars['lang']);
			if ($router->getMode() == JROUTER_MODE_SEF)
			{
				if (isset($vars['Itemid']))
				{
					$itemid = $vars['Itemid'];
					$menu = $app->getMenu();
					$item = $menu->getItem($itemid);
					unset($vars['Itemid']);
					if (isset($item) && $vars == $item->query)
					{
						$url = 'index.php?Itemid='.$itemid;
					}
					else {
						$url = 'index.php?'.JUri::buildQuery($vars).'&Itemid='.$itemid;
					}
				}
				else
				{
					$url = 'index.php?'.JUri::buildQuery($vars);
				}
			}
			else
			{
				$url = 'index.php?'.JUri::buildQuery($vars);
			}
		}

		return base64_encode($url);
	}

	public static function getType()
	{
		$user = JFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}
	
	public static function loadScript($module, $params)
	{   
		if($params->get('jquery', 0))
		{
			JHtml::script(Juri::base() . 'modules/'.$module->module.'/assets/js/jquery.min.js');
			JHtml::script(Juri::base() . 'modules/'.$module->module.'/assets/js/jquery-noconflict.js');			
		}
		JHtml::script(Juri::base() . 'modules/'.$module->module.'/assets/js/jquery.hoverIntent.minified.js');
		
		JHtml::stylesheet(Juri::base() . 'modules/'.$module->module.'/assets/css/styles.css');        
	}
}
