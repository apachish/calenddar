<?php
/**
 * @version     1.0.0
 * @package     com_trn
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar pahlevansadegh <apachish@gmail.com> - http://bmsystem.ir
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Projects list controller class.
 */
class TrnControllerProjects extends TrnController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Projects', $prefix = 'TrnModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
}