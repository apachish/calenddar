<?php

/**
 * @version     1.0.0
 * @package     com_trn
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar pahlevansadegh <apachish@gmail.com> - http://bmsystem.ir
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
/**
 * Methods supporting a list of Trn records.
 */
class TrnModelProjects extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'created_by', 'a.created_by',
                'create_project', 'a.create_project',
                'expiration_project', 'a.expiration_project',
                'name_project', 'a.name_project',
                'extera_filde', 'a.extera_filde',
                'type_project', 'a.type_project',
                'user_id', 'a.user_id',
                'reminde', 'a.reminde',
            );
        }
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {


        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);

        if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
        {
            foreach ($list as $name => $value)
            {
                // Extra validations
                switch ($name)
                {
                    case 'fullordering':
                        $orderingParts = explode(' ', $value);

                        if (count($orderingParts) >= 2)
                        {
                            // Latest part will be considered the direction
                            $fullDirection = end($orderingParts);

                            if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
                            {
                                $this->setState('list.direction', $fullDirection);
                            }

                            unset($orderingParts[count($orderingParts) - 1]);

                            // The rest will be the ordering
                            $fullOrdering = implode(' ', $orderingParts);

                            if (in_array($fullOrdering, $this->filter_fields))
                            {
                                $this->setState('list.ordering', $fullOrdering);
                            }
                        }
                        else
                        {
                            $this->setState('list.ordering', $ordering);
                            $this->setState('list.direction', $direction);
                        }
                        break;

                    case 'ordering':
                        if (!in_array($value, $this->filter_fields))
                        {
                            $value = $ordering;
                        }
                        break;

                    case 'direction':
                        if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
                        {
                            $value = $direction;
                        }
                        break;

                    case 'limit':
                        $limit = $value;
                        break;

                    // Just to keep the default case
                    default:
                        $value = $value;
                        break;
                }

                $this->setState('list.' . $name, $value);
            }
        }
        // Receive & set filters
        if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
        {
            foreach ($filters as $name => $value)
            {
                $this->setState('filter.' . $name, $value);
            }
        }

        $this->setState('list.ordering', $app->input->get('filter_order'));
        $this->setState('list.direction', $app->input->get('filter_order_Dir'));
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    JDatabaseQuery
     * @since    1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
                ->select(
                        $this->getState(
                                'list.select', 'DISTINCT a.*'
                        )
        );

        $query->from('`#__trn_project` AS a');

        
    // Join over the users for the checked out user.
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
    
		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

	    
$query->where('a.state = 1');

        // Filter by search in title
        $name_project = $this->getState('filter.search');
        $create_project = $this->getState('filter.search1');
        $expiration_project = $this->getState('filter.search2');
        $type_project = $this->getState('filter.search3');
        if(!empty($type_project) && ( !empty($create_project) && !empty($expiration_project)) && !empty($name_project) ){
                $type_project = $db->Quote($db->escape($type_project, true));
                $create_project = $db->Quote( $db->escape($create_project, true));
                $expiration_project = $db->Quote( $db->escape($expiration_project, true) );
                $name_project = $db->Quote('%' . $db->escape($name_project, true) . '%');
                $query->where('( a.create_project >='.$create_project.' ) AND ( a.expiration_project <='.$expiration_project.' )
                    AND ( a.name_project LIKE '.$name_project.' ) AND ( a.type_project='. $type_project .' )');
        }elseif(!empty($name_project) && ( !empty($create_project) && !empty($expiration_project)) ){
                $create_project = $db->Quote( $db->escape($create_project, true));
                $expiration_project = $db->Quote( $db->escape($expiration_project, true) );
                $name_project = $db->Quote('%' . $db->escape($name_project, true) . '%');
                $query->where('( a.create_project >='.$create_project.' ) AND ( a.expiration_project <='.$expiration_project.' )
                    AND ( a.name_project LIKE '.$name_project.' )');
        }elseif(!empty($type_project) && ( !empty($create_project) && !empty($expiration_project)) ){
                $type_project = $db->Quote($db->escape($type_project, true));
                $create_project = $db->Quote( $db->escape($create_project, true));
                $expiration_project = $db->Quote( $db->escape($expiration_project, true) );
                $query->where('( a.create_project >='.$create_project.' ) AND ( a.expiration_project <='.$expiration_project.' ) 
                    AND ( a.type_project='. $type_project .' )');
        }elseif(!empty($type_project) && !empty($name_project)){
            $type_project = $db->Quote($db->escape($type_project, true));
            $name_project = $db->Quote('%' . $db->escape($name_project, true) . '%');
                $query->where('( a.type_project='. $type_project .' ) AND ( a.name_project LIKE '.$name_project.' )');
        }
        elseif (!empty($name_project))
        {
            if (stripos($name_project, 'id:') === 0)
            {
                $query->where('a.id = ' . (int) substr($name_project, 3));
            }
            else
            {
                $name_project = $db->Quote('%' . $db->escape($name_project, true) . '%');
                $query->where('( a.name_project LIKE '.$name_project.' )');
            }
        }elseif (!empty($create_project) && !empty($expiration_project))
        {
            if (stripos($search1, 'id:') === 0)
            {
                $query->where('a.id = ' . (int) substr($create_project, 3));
            }
            else
            {
                $create_project = $db->Quote( $db->escape($create_project, true));
                $expiration_project = $db->Quote( $db->escape($expiration_project, true) );
                $query->where('( a.create_project >='.$create_project.' ) AND ( a.expiration_project <='.$expiration_project.' )');
            }
        }elseif (!empty($type_project))
        {
            if (stripos($type_project, 'id:') === 0)
            {
                $query->where('a.id = ' . (int) substr($type_project, 3));
            }
            else
            {
                 $type_project = $db->Quote($db->escape($type_project, true));
                $query->where('( a.type_project='. $type_project .' )');
            }
        }
        // Add the list ordering clause.
         $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn)
        {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }else{
            $orderCol='a.reminde';
            $orderDirn='asc';
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }
        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        
        return $items;
    }

    /**
     * Overrides the default function to check Date fields format, identified by
     * "_dateformat" suffix, and erases the field if it's not correct.
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        $filters = $app->getUserState($this->context . '.filter', array());
        $error_dateformat = false;
        foreach ($filters as $key => $value)
        {
            if (strpos($key, '_dateformat') && !empty($value) && !$this->isValidDate($value))
            {
                $filters[$key] = '';
                $error_dateformat = true;
            }
        }
        if ($error_dateformat)
        {
            $app->enqueueMessage(JText::_("COM_PRUEBA_SEARCH_FILTER_DATE_FORMAT"), "warning");
            $app->setUserState($this->context . '.filter', $filters);
        }

        return parent::loadFormData();
    }

    /**
     * Checks if a given date is valid and in an specified format (YYYY-MM-DD) 
     * 
     * @param string Contains the date to be checked
     * 
     */
    private function isValidDate($date)
    {
        return preg_match("/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/", $date) && date_create($date);
    }
    function typeproject(){
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
    function searched(){
        $search = JRequest::getVar('search_tel');
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $query_group="SELECT * FROM #__users where name like '%".$search."%' ";
        $db->setQuery($query_group);
        $db->query();
        $rows = $db->getNumRows();
        if($rows)
            $list=$db->loadObjectlist();
        else
            $list=false;
        return $list;
    }
    function archiveproject(){
        $db = JFactory::getDBO();
        $variable = $_POST['formData'];
        foreach ($variable as $key => $value) {
                   $query_group="UPDATE #__trn_project SET state=2 WHERE id=".$value;
                  $db->setQuery($query_group);
                  $db->query();
                  $rows = $db->getNumRows();
        }
        if($rows){
            return true;
        }else{
            return false;
        }
    }
    function deleteproject(){
        $db = JFactory::getDBO();
        $variable = $_POST['formData'];
        foreach ($variable as $key => $value) {
                   $query_group="UPDATE #__trn_project SET state=-2 WHERE id=".$value;
                  $db->setQuery($query_group);
                  $db->query();
                  $rows = $db->getNumRows();
        }
        if($rows){
            return true;
        }else{
            return false;
        }
    }
}
