<?php
/**
 * @version     1.0.0
 * @package     com_smsing
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar <apachish@gmail.com> - http://www.bmsystem.ir
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Message controller class.
 */
class SmsingControllerMessage extends JControllerForm
{

    function __construct() {
        $this->view_list = 'messages';
        parent::__construct();
    }

}