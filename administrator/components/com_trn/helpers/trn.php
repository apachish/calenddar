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

/**
 * Trn helper.
 */
class TrnHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        		JHtmlSidebar::addEntry(
			JText::_('COM_TRN_TITLE_PROJECTS'),
			'index.php?option=com_trn&view=projects',
			$vName == 'projects'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_TRN_TITLE_PANELS'),
			'index.php?option=com_trn&view=panels',
			$vName == 'panels'
		);
JHtmlSidebar::addEntry(
			JText::_('COM_TRN_TITLE_TYPE_PROJECTS'),
			'index.php?option=com_trn&view=type_projects',
			$vName == 'type_projects'
		);
        		JHtmlSidebar::addEntry(
			JText::_('COM_TRN_TITLE_HOSTS'),
			'index.php?option=com_trn&view=hosts',
			$vName == 'hosts'
		);
JHtmlSidebar::addEntry(
			JText::_('COM_TRN_TITLE_DOMAINS'),
			'index.php?option=com_trn&view=domains',
			$vName == 'domains'
		);



    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_trn';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }


}
