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
class AdduserfrontendViewCustomer extends JViewLegacy {
	function display($tpl = null) {
        $this->customer = $this->get('customer');
        parent::display($tpl);
    }
    function gettelephon($id){
        $db = JFactory::getDBO();
        $query_group="SELECT * FROM #__user_profiles where user_id=".$id." and  profile_key='profile.phone' ";
        $db->setQuery($query_group);
        $db->query();
        $rows = $db->getNumRows();
        if($rows)
        $list=$db->loadObject();
        else
            $list=false;
        return $list->profile_value;
    }
    function getUserGroups($gid)
    {
        // Initialise variables.
        $permisson=$this->permistionlevel();
        if(in_array($gid,$permisson)){
            $db     = JFactory::getDBO();
            $query  = $db->getQuery(true)
                ->select('a.title AS text')
                ->from('#__usergroups AS a')
                ->where('a.id='.$gid)
                ->group('a.id')
                ->order('a.lft ASC');

            $db->setQuery($query);
            $options = $db->loadObject();
            return $options;
        }else{
            return null;

        }
    }
    function permistionlevel(){
        $db =& JFactory::getDBO();
        $user = JFactory::getUser();

        $query_group="SELECT * FROM #__usergroups where parent_id = 2 and id IN (select group_id from #__user_usergroup_map where user_id=".$user->id.")";
        $db->setQuery($query_group);
        $group=$db->loadObjectlist();
        if($group){
            $query_group="SELECT id FROM #__usergroups ";
            $query_group.=" where ";
            $size=sizeof($group);
            $i=1;
            foreach($group as $gr){
                if($i<$size)
                    $query_group.= "parent_id =".$gr->id." OR ";
                else
                    $query_group.= "parent_id =".$gr->id."  ";
                $i++;
            }
            $query_group.="and id IN (select group_id from #__user_usergroup_map where user_id=".$user->id.")";
        }
        $db->setQuery($query_group);
        $group_list=$db->loadAssocList();
        $i=0;
        foreach($group_list as $list){
            $gr_list[$i++]=$list['id'];
        }
        return $gr_list;
    }
}
?>