<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$config = JComponentHelper::getParams('com_jsn');
		
		$vName=JRequest::getVar('view','profile');
		
		if(JRequest::getVar('format','')!='raw') echo('<div id="easyprofile" class="view_'.$vName.'">');
		
		switch($vName){
			case 'profile':
				$user=JFactory::getUser();
				$profileAcl=$config->get('profileACL',2);
				//$doc = JFactory::getDocument();
				//$doc->addStylesheet(JURI::root().'components/com_jsn/assets/css/style.css');
				if(JRequest::getVar('id')==null && $user->guest)
					JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=login',false));
				else
				{
					if(JFactory::getUser(JRequest::getVar('id'))->block)
					{
						$lang = JFactory::getLanguage();
						$lang->load('com_users');
						JError::raiseWarning(403, JText::_('COM_USERS_USER_BLOCKED'));
					}
					else
					switch($profileAcl){
						case 0: // Private
							if(JRequest::getVar('id')==$user->id || JRequest::getVar('id')==null || $user->authorise('core.edit', 'com_users'))
							{
								JsnHelper::getUserProfile(JRequest::getVar('id'));
							}
							else
							{
								JError::raiseWarning(403, JText::_('COM_JSN_NOTVIEWPROFILE'));
							}
						break;
						case 1: // Only Registered
							if(!$user->guest)
							{
								JsnHelper::getUserProfile(JRequest::getVar('id'));
							}
							else
							{
								JError::raiseWarning(403, JText::_('COM_JSN_NOTVIEWPROFILE').' - '.JText::_('COM_JSN_LOGINPLEASE'));
								JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=login',false));
							}
						break;
						case 2: // Public
							JsnHelper::getUserProfile(JRequest::getVar('id'));
						break;
						case 3: // Custom
							$access=$user->getAuthorisedViewLevels();
							$profileAclCustom=$config->get('profileACLcustom','');
							if(JRequest::getVar('id')==$user->id || JRequest::getVar('id')==null || in_array($profileAclCustom,$access) || $user->authorise('core.edit', 'com_users'))
							{
								JsnHelper::getUserProfile(JRequest::getVar('id'));
							}
							else
							{
								JError::raiseWarning(403, JText::_('COM_JSN_NOTVIEWPROFILE'));
							}
						break;
					}
				}
			break;
			case 'search':
			case 'list':
				$app = JFactory::getApplication();
				$menu = $app->getMenu();
				$item = $menu->getActive();
				if(isset($item->link) && (strpos($item->link,'option=com_jsn&view=list')>0 || strpos($item->link,'option=com_jsn&view=search'))>0)
				{
					if(JRequest::getVar('search',0) && !JRequest::checkToken()){die('Not Valid Token');}
					$document	= JFactory::getDocument();
					$vName=($vName=='search' ? 'list' : $vName);
					$lName   = $this->input->getCmd('layout', 'default');
					$vFormat = $document->getType();
					$model = $this->getModel($vName);
				
					$view=$this->getView($vName, $vFormat);
					$view->setModel($model, true);
					$view->setLayout($lName);

					// Push document object into the view.
					$view->document = $document;
					$view->display();
				}
				else echo('<h1>'.JText::_('JLIB_RULES_NOT_ALLOWED').'</h1>');
			break;
			case 'getField':
				$user=JsnHelper::getUser();
				if(!$user->guest && JRequest::getVar('alias','')!=''){
					echo $user->getField(JRequest::getVar('alias',''));
				}
			break;
			case 'setField':
				$user=JsnHelper::getUser();
				if(!$user->guest && JRequest::getVar('alias','')!=''){
					$user->setValue(JRequest::getVar('alias',''),JRequest::getVar('value',''));
					$user->save();
				}
			break;
			case 'opField':
				foreach (glob(JPATH_ADMINISTRATOR . '/components/com_jsn/helpers/fields/*.php') as $filename) {
					require_once $filename;
				}
				//$user=JsnHelper::getUser();
				if(/*!$user->guest && */JRequest::getVar('type','')!=''){
					$class='Jsn'.ucfirst(JRequest::getVar('type','')).'FieldHelper';
					if(class_exists($class))
					{
						$class::operations();
					}
				}
			break;
			default:
				$dispatcher	= JEventDispatcher::getInstance();
				echo(implode(' ',$dispatcher->trigger('renderPlugin',array())));
			break;

		}
		
		if(JRequest::getVar('format','')!='raw') echo('</div>');
		
	}
}
