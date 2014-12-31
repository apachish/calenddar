<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnModelList extends JModelList
{
	protected function populateState($ordering = 'name', $direction = 'ASC')
	{
		$app = JFactory::getApplication();
		$params = $app->getParams();

		// List state information
		$value = $app->input->get('limit', $params->get('display_num',$app->getCfg('list_limit', 0)), 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$orderCol = $app->input->get('filter_order', 'a.name');
		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.name';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		/*$user = JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_content')) && (!$user->authorise('core.edit', 'com_content')))
		{
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language', JLanguageMultilang::isEnabled());

		// process show_noauth parameter
		if (!$params->get('show_noauth'))
		{
			$this->setState('filter.access', true);
		}
		else
		{
			$this->setState('filter.access', false);
		}*/

		$this->setState('layout', $app->input->get('layout'));
	}
	
	protected function getListQuery()
	{
		$user=JFactory::getUser();
		$app = JFactory::getApplication();
		$params = $app->getParams();
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($this->getState('list.select','a.id'))->group('a.id')->from('#__users AS a')->join('left','#__jsn_users as b ON a.id=b.id')->join('left','#__user_usergroup_map as c ON a.id=c.user_id')->where('a.block=0');//->order($db->escape('a.name') . ' ASC');
		
		// Add Custom Where
		if($params->get('where','')!='') $query->where($params->get('where',''));
		
		$query->order($db->quoteName('a.name').' ASC');
		
		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		return $items;
	}
	
}


?>