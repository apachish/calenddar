<?php
/**
 * Joomla! 3.0 component Add user Frontend
 *
 * @version $Id: view.html.php 2014-08-24 23:00:13 svn $
 * @author Kim Pittoors
 * @package Joomla
 * @subpackage Add user Frontend
 * @license GNU/GPL
 *
 * Add users to Community builder on the frontend
 *
 * @Copyright Copyright (C) 2009 - 2014 - Kim Pittoors - www.joomlacy.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the Add user Frontend component
 */
class AdduserfrontendViewAdduserfrontend extends JViewLegacy {
    function display($tpl = null) {
        $this->telephon = JRequest::getVar('telephon');
        $this->type = JRequest::getVar('type');
        $this->userids = JRequest::getVar('userid');
        $this->us = JFactory::getUser($this->userids);
        parent::display($tpl);
    }
    function type_project(){
        $db = JFactory::getDBO();
        $query_project="SELECT * FROM #__trn_type_project";
        $db->setQuery($query_project);
        $db->query();
        $rows = $db->getNumRows();
        if($rows)
        $list=$db->loadObjectlist();
        else
            $list=false;
        return $list;
    }
    function type_host(){
        $db = JFactory::getDBO();
        $query_project="SELECT * FROM #__trn_panel";
        $db->setQuery($query_project);
        $db->query();
        $rows = $db->getNumRows();
        if($rows)
        $list=$db->loadObjectlist();
        else
            $list=false;
        return $list;
    }
}
?>