<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnViewUsertype extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');
		$this->canDo = JsnHelper::getActions($this->state->get('jsn.component'));
		$input = JFactory::getApplication()->input;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$input->set('hidemainmenu', true);
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since  3.1
	 */
	protected function addToolbar()
	{
		$input      = JFactory::getApplication()->input;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = JFactory::getLanguage();
			$lang->load('com_jsn', JPATH_BASE, null, false, false)
		||	$lang->load('com_jsn', JPATH_ADMINISTRATOR.'/components/com_jsn', null, false, false)
		||	$lang->load('com_jsn', JPATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load('com_jsn', JPATH_ADMINISTRATOR.'/components/com_jsn', $lang->getDefault(), false, false);

		// Load the tags helper.
		require_once JPATH_COMPONENT.'/helpers/jsn.php';

		// Get the results for each action.
		$canDo = JsnHelper::getActions('com_jsn', $this->item->id);

		$title = JText::_('COM_JSN_BASE_'.($isNew?'ADD':'EDIT').'_TITLE_USERTYPE');

		// Prepare the toolbar.
		JToolbarHelper::title($title, 'usertype-'.($isNew?'add':'edit').($isNew?'add':'edit'));

		// For new records, check the create permission.
		if ($isNew)
		{
			JToolbarHelper::apply('usertype.apply');
			JToolbarHelper::save('usertype.save');
			JToolbarHelper::save2new('usertype.save2new');
		}

		// If not checked out, can save the item.
		elseif (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_user_id == $userId))) {
			JToolbarHelper::apply('usertype.apply');
			JToolbarHelper::save('usertype.save');
			if ($canDo->get('core.create')) {
				JToolbarHelper::save2new('usertype.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolbarHelper::save2copy('usertype.save2copy');
		}

		if (empty($this->item->id))  {
			JToolbarHelper::cancel('usertype.cancel');
		}
		else {
			JToolbarHelper::cancel('usertype.cancel', 'JTOOLBAR_CLOSE');
		}
		//JToolbarHelper::help('JHELP_COMPONENTS_TAGS_MANAGER_EDIT');
		JToolbarHelper::divider();

	}
}
