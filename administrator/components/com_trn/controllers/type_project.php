<?php
/**
 * @version     1.0.0
 * @package     com_trn
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar pahlevansadegh <apachish@gmail.com> - http://bmsystem.ir
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Type_project controller class.
 */
class TrnControllerType_project extends JControllerForm
{

    function __construct() {
        $this->view_list = 'type_projects';
        parent::__construct();
    }

}