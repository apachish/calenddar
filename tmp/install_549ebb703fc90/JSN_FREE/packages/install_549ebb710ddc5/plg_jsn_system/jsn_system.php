<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

class plgSystemJsn_System extends JPlugin
{
	public function onAfterRoute()
	{
		$app=JFactory::getApplication();
		
		// Load Config
		$config = JComponentHelper::getParams('com_jsn');
		
		// Load Language for modules
		if($app->isAdmin() && JRequest::getVar('option')=='com_modules')
		{
			$lang = JFactory::getLanguage();
			$lang->load('com_jsn');
		}
		
		if($config->get('logintype', 'USERNAME')=='MAIL')
		{
			// Reset Password with Email config
			if(JRequest::getVar('task')=='reset.confirm')
			{
				$input = JFactory::getApplication()->input;
				$form=$input->post->get('jform', array(), 'array');
				if(isset($form['username'])){
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('a.username')->from('#__users as a')->where('email = '.$db->quote($form['username']));
					$db->setQuery( $query );
					if($username=$db->loadResult()){
						$form['username']=$username;
						$input->post->set('jform', $form);
						JRequest::setVar('jform', $form);
					}
				}
			}
			// Register with Email config (Bug username in email)
			if(JRequest::getVar('task')=='registration.register' || ($app->isAdmin() && JRequest::getVar('option')=='com_users' && JRequest::getVar('layout')=='edit' && JRequest::getVar('id',-1)==0 ))
			{
				$input = JFactory::getApplication()->input;
				$form=$input->post->get('jform', array(), 'array');
				if($app->isSite()) $form['username']=$form['email1'];
				else  $form['username']=$form['email'];
				$input->post->set('jform', $form);
				JRequest::setVar('jform', $form);
			}
			if(JRequest::getVar('task')=='registration.activate')
			{
				$db = JFactory::getDbo();
				$token=JRequest::getVar('token',false);
				if($token){
					// Get the user id based on the token.
					$query = $db->getQuery(true);
					$query->select($db->quoteName('id'))
						->from($db->quoteName('#__users'))
						->where($db->quoteName('activation') . ' = ' . $db->quote($token))
						->where($db->quoteName('block') . ' = ' . 1)
						->where($db->quoteName('lastvisitDate') . ' = ' . $db->quote($db->getNullDate()));
					$db->setQuery($query);
					try
					{
						$userId = (int) $db->loadResult();
						$user = JFactory::getUser($userId);
						$com_users_config = JComponentHelper::getParams('com_users');
						if($com_users_config->get('useractivation',1)!=2 || ($com_users_config->get('useractivation',1)==2 && $user->getParam('activate',0)))
						{
							$user->tmp_username=$user->username;
							$user->username=$user->email;
						}
					}
					catch (RuntimeException $e)
					{
						$this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);
					}
				}
			}
		}
		
		if($app->isSite())
		{
			// ---- Edit Profiles from Frontend
			$user=JFactory::getUser();
			$session = JFactory::getSession();
			$original_id=$session->get('jsn_original_id',0);
			if(empty($original_id)){
				$session->set('jsn_original_id', (int) $user->id );
				$original_id=$user->id;
			}
			if($user->authorise('core.edit', 'com_users') && JRequest::getVar('option','')=='com_users' && JRequest::getVar('layout','')=='edit' && JRequest::getVar('user_id',0) > 0)
			{
				// Check Super User
				$editUser=JFactory::getUser(JRequest::getVar('user_id',0));
				if(in_array(8,$editUser->groups) && !in_array(8,$user->groups))
				{
					$lang = JFactory::getLanguage();
					$lang->load('com_jsn');
					$app->enqueueMessage(JText::_('COM_JSN_NOCHANGEADMIN'),'error');
					$app->redirect(JRoute::_('index.php?option=com_jsn&view=profile&id='.JRequest::getVar('user_id',0),false));
				}
				// Change edit id
				$app->setUserState('com_users.edit.profile.id', (int) JRequest::getVar('user_id') );
			}
			else if($user->authorise('core.edit', 'com_users') && JRequest::getVar('option','')=='com_users' && JRequest::getVar('task','')=='profile.save')
			{
				// Check Super User
				$editUser=JFactory::getUser(JRequest::getVar('user_id',0));
				if(in_array(8,$editUser->groups) && !in_array(8,$user->groups))
				{
					$lang = JFactory::getLanguage();
					$lang->load('com_jsn');
					$app->enqueueMessage(JText::_('COM_JSN_NOCHANGEADMIN'),'error');
					$app->redirect(JRoute::_('index.php?option=com_jsn&view=profile',false));
				}
				// Change user e editid
				$input = JFactory::getApplication()->input;
				$form=$input->post->get('jform', array(), 'array');
				if(isset($form['id']) && $form['id']!=$user->id)
				{
					$user->id=$form['id'];
					JSession::getFormToken();
					$input->post->set(JSession::getFormToken(),1);
					$app->setUserState('com_users.edit.profile.id', (int) $form['id'] );
				}
			}
			else if($user->id!=$original_id)
			{
				// Restore User
				$app->setUserState('com_users.edit.profile.id', (int) $original_id );
				$session->set('user', new JUser($original_id));
				// Redirect
				$menu = $app->getMenu();
				$profileMenu=$menu->getItems('link','index.php?option=com_jsn&view=profile',true);
				if(isset($profileMenu->id))
				{
					$app->redirect(JRoute::_('index.php?option=com_jsn&id='.$user->id.'&view=profile&Itemid='.$profileMenu->id,false));
				}
				else{
					$app->redirect(JRoute::_('index.php?option=com_jsn&id='.$user->id.'&view=profile&Itemid='.$menu->getDefault()->id,false));
				}
			}
			else 
			{
				$app->setUserState('com_users.edit.profile.id', (int) $original_id );
			}
			
			// ---- Load JSN Plugins
			JPluginHelper::importPlugin('jsn');

			// ---- Redirect
			if(JRequest::getVar('option')=='com_users' && JRequest::getVar('view')=='profile' && JRequest::getVar('layout')!='edit' && JRequest::getVar('task','')=='')
			{
				$menu = $app->getMenu();
				$profileMenu=$menu->getItems('link','index.php?option=com_jsn&view=profile',true);
				if(isset($profileMenu->id))
				{
					$app->redirect(JRoute::_('index.php?option=com_jsn&view=profile&Itemid='.$profileMenu->id,false));
				}
				else{
					$app->redirect(JRoute::_('index.php?option=com_jsn&view=profile&Itemid='.$menu->getDefault()->id,false));
				}
			}
			
			// ---- Cookie problem when access to other profiles
			$user->set('cookieLogin','');
			
			// ---- Check if required fields are not empty
			$checkRequired=$config->get('forcerequired',0);
			if($user->id && $checkRequired)
			{
				require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
				$user=JsnHelper::getUser();
				
				$excludeFromCheck=JsnHelper::excludeFromProfile($user);
				$access=$user->getAuthorisedViewLevels();
				$db=JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('a.alias')->from('#__jsn_fields AS a')->where('a.level = 2')->where('a.published = 1')->where('a.required = 1')->where('a.edit = 1')->where('a.access IN ('.implode(',',$access).')');
				$db->setQuery( $query );
				$requiredFields = $db->loadColumn();
				$required=true;
				foreach($requiredFields as $requiredField)
				{
					$formFormatRequiredField='jform['.$requiredField.']';
					if(!in_array($formFormatRequiredField,$excludeFromCheck) && $user->$requiredField=='') $required=false;
				}
				if(!$required)
				{
					if(JRequest::getVar('option')=='com_users' && JRequest::getVar('view')=='profile' && JRequest::getVar('layout')=='edit')
					{
						$lang = JFactory::getLanguage();
						$lang->load('com_jsn');
						if(JRequest::getVar('task')!='profile.save' && JRequest::getVar('task')!='save') $app->enqueueMessage(JText::_('COM_JSN_COMPLETEREGISTRATION'),'warning');
					}
					else{
						if(JRequest::getVar('task')!='profile.save' && JRequest::getVar('task')!='save' && JRequest::getVar('task')!='user.logout') $app->redirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit',false));
					}
				
				}
			}
		}
	}
	
	function onAfterRender()
    {
		$app = JFactory::getApplication();
		if (!$app->isAdmin()){
			$page=JResponse::getBody();
			$page=str_replace('{socialconnect}', '<div class="socialconnect"></div>', $page);
			JResponse::setBody($page);
		}
	}
	
	public function onBeforeCompileHead()
	{
		$app=JFactory::getApplication();

		// Load Config
		$config = JComponentHelper::getParams('com_jsn');

		// Javascript for Tabs
		if(!$app->isAdmin() && JRequest::getVar('option')=='com_users' && $config->get('tabs',1))
		{
			// Tabs
			$doc = JFactory::getDocument();
			$script='
			var jsn_prev_button="'.JText::_('JPREV').'";
			var jsn_next_button="'.JText::_('JNEXT').'";
			';
			$doc->addScriptDeclaration( $script );
			$doc->addScript(JURI::root().'components/com_jsn/assets/js/tabs.js');
		}
		// Javascript for com_users
		if(JRequest::getVar('option')=='com_users')
		{
			JHtml::_('jquery.framework');
			$doc = JFactory::getDocument();
			$doc->addStylesheet(JURI::root().'components/com_jsn/assets/css/style.css');
			$doc->addScript(JURI::root().'components/com_jsn/assets/js/privacy.js');
			$doc->addScript(JURI::root().'components/com_jsn/assets/js/name.js');
		}
		
		// Javascript for Condition (com_users and userlist search form)
		if(JRequest::getVar('option')=='com_users' || (JRequest::getVar('option')=='com_jsn' && JRequest::getVar('view')=='list'))
		{
			JHtml::_('jquery.framework');
			$doc = JFactory::getDocument();
			$db=JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('a.*')->from('#__jsn_fields AS a')->where('a.level = 2')->where('a.published = 1')->order($db->escape('a.lft') . ' ASC');
			$db->setQuery( $query );
			$fields = $db->loadObjectList('alias');
			$script='jQuery(document).ready(function($){
				var hideFieldset=$("#member-profile fieldset.hide,#member-registration fieldset.hide").length;
				function checkFieldset(){
					$("#member-profile fieldset,#member-registration fieldset").each(function(){
						if($(this).children(".control-group.hide").length==$(this).children(".control-group").length){
							$(this).hide().addClass("hide");
							
						}
						else {
							$(this).show().removeClass("hide");
							
						}
						
					});'
				.(!$app->isAdmin() && $config->get('tabs',1) ? '	if($("#member-profile fieldset.hide,#member-registration fieldset.hide").length!=hideFieldset){
						hideFieldset=$("#member-profile fieldset.hide,#member-registration fieldset.hide").length;
						tabs($);
					}' : '')
				.'}
				';
			foreach($fields as $field)
			{
				// Load Options
				$registry = new JRegistry;
				$registry->loadString($field->params);
				$field->params = $registry->toArray();
				$condition_suffix=array('','1','2','3','4','5','6','7','8','9');
				foreach($condition_suffix as $suffix)
				{
					if(empty($field->params['condition_hide'.$suffix])) $field->params['condition_hide'.$suffix]=array();
					if(isset($field->params['condition_operator'.$suffix]) && $field->params['condition_operator'.$suffix]!=0 && count($field->params['condition_hide'.$suffix])>0){
						if(isset($field->params['condition_action'.$suffix]) && $field->params['condition_action'.$suffix]=='show')
						{
							$actionShowFields=true;
						}
						else
						{
							$actionShowFields=false;
						}
						switch($field->params['condition_operator'.$suffix])
						{
							case 1:
								$operator='==';
								$operator_post='';
								if($actionShowFields) $not='!';
								else $not='';
							break;
							case 2:
								$operator='>';
								$operator_post='';
								if($actionShowFields) $not='!';
								else $not='';
							break;
							case 3:
								$operator='<';
								$operator_post='';
								if($actionShowFields) $not='!';
								else $not='';
							break;
							case 4:
								$operator='.indexOf';
								$operator_post='!=-1';
								if($actionShowFields) $not='!';
								else $not='';
							break;
							case 5:
								$operator='!=';
								$operator_post='';
								if($actionShowFields) $not='!';
								else $not='';
							break;
							case 6:
								$operator='.indexOf';
								$operator_post='!=-1';
								if($actionShowFields) $not='';
								else $not='!';
							break;
						}
						// Field to Show/Hide
						$fieldsToHide='';
						foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
						{
							$fieldsToHide.='#jform_'.str_replace('-','_',$fieldToHide).',';
							$fieldsToHide.='#jform_privacy_'.str_replace('-','_',$fieldToHide).',';
						}
						$fieldsToHide=trim($fieldsToHide,',');
						// Field to Check
						if($field->params['condition_field'.$suffix]=='_custom') $valueToCheck='var valToCheck="'.$field->params['condition_custom'.$suffix].'";';
						else $valueToCheck='
							var valToCheck=$("#jform_'.str_replace('-','_',$field->params['condition_field'.$suffix]).'").val();
							$("#jform_'.str_replace('-','_',$field->params['condition_field'.$suffix]).' input:checked").each(function(){
								if(valToCheck=="") valToCheck=$(this).val();
								else valToCheck=valToCheck+","+$(this).val();
							});
						';
						// Field to Bind
						if($field->params['condition_field'.$suffix]=='_custom') $fieldToBind='#jform_'.str_replace('-','_',$field->alias);
						else $fieldToBind='#jform_'.str_replace('-','_',$field->alias).',#jform_'.str_replace('-','_',$field->params['condition_field'.$suffix]);
						// Code
						$scriptval='
							var val=$("#jform_'.str_replace('-','_',$field->alias).'").val();
						';
						if($field->type=='radiolist')
							$scriptval='
								var val=$("#jform_'.str_replace('-','_',$field->alias).' input:checked").val();
							';
						if($field->type=='checkboxlist')
							$scriptval='
								var val="";
								$("#jform_'.str_replace('-','_',$field->alias).' input:checked").each(function(){
									if(val=="") val=$(this).val();
									else val=val+","+$(this).val();
								});
							';
						$script.='
							'.$scriptval.'
							if(val==null || val==undefined) val="";
							'.$valueToCheck.'
							if(valToCheck==null || valToCheck==undefined) valToCheck="";
							if($("#jform_'.str_replace('-','_',$field->alias).'").length) if('.$not.'(val'.$operator.'(valToCheck)'.$operator_post.'))
								$("'.$fieldsToHide.'").each(function(){
									if($(this).is(".required") || $(this).is("[aria-required=\'true\']") || $(this).is("[required=\'required\']")){
										$(this).addClass("norequired").removeClass("required").removeAttr("required").attr("aria-required","false");
										$(this).find("input[type!=\'checkbox\']").addClass("norequired").removeClass("required").removeAttr("required").attr("aria-required","false");
									}
									if($(this).parent().is(".input-prepend")) $(this).parent().parent().parent().hide().addClass("hide");
									else $(this).parent().parent().hide().addClass("hide");
								});
							else
								$("'.$fieldsToHide.'").each(function(){
									if($(this).is(".norequired")){
										$(this).addClass("required").removeClass("norequired").attr("required","required").attr("aria-required","true");
										$(this).find("input[type!=\'checkbox\']").addClass("required").removeClass("norequired").attr("required","required").attr("aria-required","true");
									}
									if($(this).parent().is(".input-prepend")) $(this).parent().parent().parent().show().removeClass("hide");
									else $(this).parent().parent().show().removeClass("hide");
								});
							checkFieldset();
							$("'.$fieldToBind.'").bind("change keyup",function(){
								'.$scriptval.'
								if(val==null || val==undefined) val="";
								'.$valueToCheck.'
								if(valToCheck==null || valToCheck==undefined) valToCheck="";
								if('.$not.'(val'.$operator.'(valToCheck)'.$operator_post.'))
									$("'.$fieldsToHide.'").each(function(){
										if($(this).is(".required") || $(this).is("[aria-required=\'true\']") || $(this).is("[required=\'required\']")){
											$(this).addClass("norequired").removeClass("required").removeAttr("required").attr("aria-required","false");
											$(this).find("input[type!=\'checkbox\']").addClass("norequired").removeClass("required").removeAttr("required").attr("aria-required","false");
										}
										if($(this).parent().is(".input-prepend")) $(this).parent().parent().parent().slideUp().addClass("hide");
										else $(this).parent().parent().slideUp().addClass("hide");
									});
								else
									$("'.$fieldsToHide.'").each(function(){
										if($(this).is(".norequired")){
											$(this).addClass("required").removeClass("norequired").attr("required","required").attr("aria-required","true");
											$(this).find("input[type!=\'checkbox\']").addClass("required").removeClass("norequired").attr("required","required").attr("aria-required","true");
										}
										if($(this).parent().is(".input-prepend")) $(this).parent().parent().parent().slideDown().removeClass("hide");
										else $(this).parent().parent().slideDown().removeClass("hide");
									});
								checkFieldset();
							});';
					}
				}
			}
			$script.='});';
			$doc->addScriptDeclaration( $script );
		}
		
		// Add Bootstrap CSS
		if(!$app->isAdmin() && (JRequest::getVar('option')=='com_users' || JRequest::getVar('option')=='com_jsn') && $config->get('bootstrap',0))
		{
			$doc = JFactory::getDocument();
			$doc->addStylesheet(JURI::root().'media/jui/css/bootstrap.min.css');
		}
		
		if(JRequest::getVar('option')=='com_jsn')
		{
			$doc = JFactory::getDocument();
			$doc->addStylesheet(JURI::root().'components/com_jsn/assets/css/style.css');
		}
		
	}
}
