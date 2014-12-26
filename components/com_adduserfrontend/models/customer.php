<?php
/**
 * Joomla! 3.0 component Add user Frontend
 *
 * @version $Id: adduserfrontend.php 2014-08-24 23:00:13 svn $
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
jimport('joomla.application.component.model');
/**
 * Add user Frontend Component Add user Frontend Model
 *
 * @author      notwebdesign
 * @package		Joomla
 * @subpackage	Add user Frontend
 * @since 3.0
 */
class AdduserfrontendModelCustomer extends JModelLegacy {
    /**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
    }
    function getcustomer(){
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
       //$per=$this->permistionlevel();
       // $group=implode(',',$per);
        $query_group="SELECT * FROM #__users ";
        $db->setQuery($query_group);
        $list=$db->loadObjectlist();
        return $list;
    }
    function permistionlevel(){
        $db =& JFactory::getDBO();
        $user = JFactory::getUser();

        $query_group="SELECT * FROM #__usergroups where parent_id = 2 ";//and id IN (select group_id from #__user_usergroup_map where user_id=".$user->id.")
        $db->setQuery($query_group);
        $group=$db->loadObjectlist();
        // if($group){
        //     $query_group="SELECT id FROM #__usergroups ";
        //     $query_group.=" where ";
        //     $size=sizeof($group);
        //     $i=1;
        //     foreach($group as $gr){
        //         if($i<$size)
        //             $query_group.= "parent_id =".$gr->id." OR ";
        //         else
        //             $query_group.= "parent_id =".$gr->id."  ";
        //         $i++;
        //     }
        //     $query_group.="and id IN (select group_id from #__user_usergroup_map where user_id=".$user->id.")";
        // }
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
