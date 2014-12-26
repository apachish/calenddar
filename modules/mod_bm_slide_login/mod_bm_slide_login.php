<?php
/**
 * @package     Brainymore.com
 * @subpackage  mod_bm_slide_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the login functions only once
require_once __DIR__ . '/helper.php';

$params->def('greeting', 1);

if(!defined('BM_SLIDE_LOGIN_LOAD_SCRIPT'))
{
    ModBMSlideLoginHelper::loadScript($module, $params); 
    define('BM_SLIDE_LOGIN_LOAD_SCRIPT', TRUE);
}

$type	          = ModBMSlideLoginHelper::getType();
$return	          = ModBMSlideLoginHelper::getReturnURL($params, $type);
$user	          = JFactory::getUser();
$layout           = $params->get('layout', 'default');

// Logged users must load the logout sublayout
if (!$user->guest)
{
	$layout .= '_logout';
}

require JModuleHelper::getLayoutPath('mod_bm_slide_login', $layout);
