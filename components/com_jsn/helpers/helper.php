<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


class JsnHelper
{

	public static function getUser($id=null)
	{
		static $currentId=null;
		if($id==null) $id=$currentId;
		
		static $cache_users=null;
		if($id>0 && isset($cache_users[$id]))
		{
			return $cache_users[$id];
		}
		$user=new JsnUser($id);
		$cache_users[$user->id]=$user;
		if($id==null) $currentId=$user->id;
		
		return $user;
	}

	public static function getUserProfile($id=null,$alt=false)
	{
		$user=JFactory::getUser($id);
		$app=JFactory::getApplication();
		
		// Override Current Var
		$currentId=$app->getUserState('com_users.edit.profile.id','');
		$app->setUserState('com_users.edit.profile.id',$user->id);

		//require_once(JPATH_SITE . '/components/com_users/controller.php');
		require_once(JPATH_SITE . '/components/com_users/views/profile/view.html.php');
		require_once(JPATH_SITE . '/components/com_users/models/profile.php');
		require_once(JPATH_SITE . '/components/com_users/helpers/html/users.php');

		/*static $controller;
		if(!($controller instanceof UsersController)){
			$controller = new UsersController();//JControllerLegacy::getInstance('Users');
		}*/

		$document	= JFactory::getDocument();

		$vName   = 'profile';
		$vFormat = $document->getType();
		($alt ? $lName   = $alt : $lName   = JRequest::getVar('layout','default'));

		$model=new UsersModelProfile();//$controller->getModel('profile');
		$view=new UsersViewProfile();//$controller->getView('profile');
		//print_r($view)
		$view->setModel($model, true);
		$view->setLayout($lName);
		
		$view->excludeFromProfile=JsnHelper::excludeFromProfile($model->getData()); // Conditions
		$view->config=JComponentHelper::getParams('com_jsn');
		

		// Push document object into the view.
		$view->document = $document;

		$view->display();

		// Replace Original Current Var
		$app->setUserState('com_users.edit.profile.id',$currentId);
		
		return true;
	}
	
	public static function isOnline($id) {
		static $loggedin=array();
		if(isset($loggedin[$id])) return $loggedin[$id];
		else
		{
			$db     = JFactory::getDBO();
			$query      = 'SELECT COUNT(userid) FROM #__session AS s WHERE s.client_id=0 AND s.userid = '.(int)$id;
			$db->setQuery($query);
			$loggedin[$id]   = $db->loadResult();
			return $loggedin[$id];
		}
	}
	
	public static function getFormatName($user){
		$config = JComponentHelper::getParams('com_jsn');
		$formatName=$config->get('formatname', 'NAME');
		switch($formatName){
			case 'NAME':
				return $user->name;
			break;
			case 'USERNAME':
				return $user->username;
			break;
			case 'NAMEUSERNAME':
				return $user->name.' ('.$user->username.')';
			break;
			case 'USERNAMENAME':
				return $user->username.' ('.$user->name.')';
			break;
		}
	}
	
	public static function addUserToGroup($user, $groupId)
	{
		$userId=$user->id;
		// Add the user to the group if necessary.
		if (!in_array($groupId, $user->groups))
		{
			// Get the title of the group.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__usergroups'))
				->where($db->quoteName('id') . ' = ' . (int) $groupId);
			$db->setQuery($query);
			$title = $db->loadResult();

			// If the group does not exist, return an exception.
			if (!$title)
			{
				throw new RuntimeException('Access Usergroup Invalid');
			}

			$columns = array('user_id', 'group_id');
			$values = array($userId,$groupId);
			$query = $db->getQuery(true);
			$query
			    ->insert($db->quoteName('#__user_usergroup_map'))
			    ->columns($db->quoteName($columns))
			    ->values(implode(',', $values));
			$db->setQuery($query);
			$db->query();
		}

		if (session_id())
		{
			// Set the group data for any preloaded user objects.
			$temp = JFactory::getUser((int) $userId);
			$temp->groups = $user->groups;

			// Set the group data for the user object in the session.
			$temp = JFactory::getUser();

			if ($temp->id == $userId)
			{
				$temp->groups = $user->groups;
			}
		}

		
	}
	
	public static function removeUserFromGroup($user, $groupId)
	{
		$userId=$user->id;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$conditions = array(
		    $db->quoteName('user_id') . ' = '. $userId, 
		    $db->quoteName('group_id') . ' = ' . $groupId
		);
		$query->delete($db->quoteName('#__user_usergroup_map'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->query();
		

		// Set the group data for any preloaded user objects.
		$temp = JFactory::getUser((int) $userId);
		$temp->groups = $user->groups;

		// Set the group data for the user object in the session.
		$temp = JFactory::getUser();

		if ($temp->id == $userId)
		{
			$temp->groups = $user->groups;
		}

		
	}
	
	public static function excludeFromProfile($data){
		static $fields=null;
		if(!$fields)
		{
			$db=JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('a.*')->from('#__jsn_fields AS a')->where('a.level = 2')->where('a.published = 1')->order($db->escape('a.lft') . ' ASC');
			$db->setQuery( $query );
			$fields = $db->loadObjectList('alias');
		}
		$userData=$data;
		$excludeFromProfile=array();
		foreach($fields as $field)
		{
			// Load Options
			if(is_string($field->params))
			{
				$registry = new JRegistry;
				$registry->loadString($field->params);
				$field->params = $registry->toArray();
			}
			
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
					if($field->params['condition_field'.$suffix]=='_custom') $value=$field->params['condition_custom'.$suffix];
					else 
					{
						$alias=$field->params['condition_field'.$suffix];
						if(isset($userData->$alias)) $value=$userData->$alias;
						else $value='';
						if(is_array($value)) $value=implode(',',$value);
					}
					$alias=$field->alias;
					if(!isset($userData->$alias)) $userValue='';
					else $userValue=$userData->$alias;
					
					if(is_array($userValue)) $userValue=implode(',',$userValue);
					
					/*if(is_array($userData->$alias))
					{
						foreach($userData->$alias as $userValue)
						{
							switch($field->params['condition_operator'.$suffix])
							{
								case 1:
									if((!$actionShowFields && $userValue==$value) || ($actionShowFields && $userValue!=$value))
									{
										foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
										{
											$excludeFromProfile[]='jform['.$fieldToHide.']';
											$excludeFromProfile[]='jform['.$fieldToHide.'][]';
										}
									}
								break;
								case 2:
									if((!$actionShowFields && $userValue>$value) || ($actionShowFields && $userValue<=$value))
									{
										foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
										{
											$excludeFromProfile[]='jform['.$fieldToHide.']';
											$excludeFromProfile[]='jform['.$fieldToHide.'][]';
										}
									}
								break;
								case 3:
									if((!$actionShowFields && $userValue<$value) || ($actionShowFields && $userValue>=$value))
									{
										foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
										{
											$excludeFromProfile[]='jform['.$fieldToHide.']';
											$excludeFromProfile[]='jform['.$fieldToHide.'][]';
										}
									}
								break;
								case 4:
									if((!$actionShowFields && strpos(' '.$userValue,$value)>0) || ($actionShowFields && !(strpos(' '.$userValue,$value)>0)))
									{
										foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
										{
											$excludeFromProfile[]='jform['.$fieldToHide.']';
											$excludeFromProfile[]='jform['.$fieldToHide.'][]';
										}
									}
								break;
								case 5:
									if((!$actionShowFields && $userValue!=$value) || ($actionShowFields && $userValue==$value))
									{
										foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
										{
											$excludeFromProfile[]='jform['.$fieldToHide.']';
											$excludeFromProfile[]='jform['.$fieldToHide.'][]';
										}
									}
								break;
								case 6:
									if((!$actionShowFields && !(strpos(' '.$userValue,$value)>0)) || ($actionShowFields && strpos(' '.$userValue,$value)>0))
									{
										foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
										{
											$excludeFromProfile[]='jform['.$fieldToHide.']';
											$excludeFromProfile[]='jform['.$fieldToHide.'][]';
										}
									}
								break;
							}
						}
					}
					else
					{*/
						switch($field->params['condition_operator'.$suffix])
						{
							case 1:
								if((!$actionShowFields && $userValue==$value) || ($actionShowFields && $userValue!=$value)   )
								{
									foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
									{
										$excludeFromProfile[]='jform['.$fieldToHide.']';
										if(isset($fields[$fieldToHide]) && $fields[$fieldToHide]->type=='gmap'){
											$excludeFromProfile[]='jform['.$fieldToHide.'_lat]';
											$excludeFromProfile[]='jform['.$fieldToHide.'_lng]';
										}
										$excludeFromProfile[]='jform['.$fieldToHide.'][]';
									}
								}
							break;
							case 2:
								if((!$actionShowFields && $userValue>$value) || ($actionShowFields && $userValue<=$value)   )
								{
									foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
									{
										$excludeFromProfile[]='jform['.$fieldToHide.']';
										if(isset($fields[$fieldToHide]) && $fields[$fieldToHide]->type=='gmap'){
											$excludeFromProfile[]='jform['.$fieldToHide.'_lat]';
											$excludeFromProfile[]='jform['.$fieldToHide.'_lng]';
										}
										$excludeFromProfile[]='jform['.$fieldToHide.'][]';
									}
								}
							break;
							case 3:
								if((!$actionShowFields && $userValue<$value) || ($actionShowFields && $userValue>=$value)   )
								{
									foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
									{
										$excludeFromProfile[]='jform['.$fieldToHide.']';
										if(isset($fields[$fieldToHide]) && $fields[$fieldToHide]->type=='gmap'){
											$excludeFromProfile[]='jform['.$fieldToHide.'_lat]';
											$excludeFromProfile[]='jform['.$fieldToHide.'_lng]';
										}
										$excludeFromProfile[]='jform['.$fieldToHide.'][]';
									}
								}
							break;
							case 4:
								if((!$actionShowFields && strpos(' '.$userValue,$value)>0) || ($actionShowFields && !(strpos(' '.$userValue,$value)>0))   )
								{
									foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
									{
										$excludeFromProfile[]='jform['.$fieldToHide.']';
										if(isset($fields[$fieldToHide]) && $fields[$fieldToHide]->type=='gmap'){
											$excludeFromProfile[]='jform['.$fieldToHide.'_lat]';
											$excludeFromProfile[]='jform['.$fieldToHide.'_lng]';
										}
										$excludeFromProfile[]='jform['.$fieldToHide.'][]';
									}
								}
							break;
							case 5:
								if((!$actionShowFields && $userValue!=$value) || ($actionShowFields && $userValue==$value)   )
								{
									foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
									{
										$excludeFromProfile[]='jform['.$fieldToHide.']';
										if(isset($fields[$fieldToHide]) && $fields[$fieldToHide]->type=='gmap'){
											$excludeFromProfile[]='jform['.$fieldToHide.'_lat]';
											$excludeFromProfile[]='jform['.$fieldToHide.'_lng]';
										}
										$excludeFromProfile[]='jform['.$fieldToHide.'][]';
									}
								}
							break;
							case 6:
								if((!$actionShowFields && !(strpos(' '.$userValue,$value)>0)) || ($actionShowFields && strpos(' '.$userValue,$value)>0)   )
								{
									foreach($field->params['condition_hide'.$suffix] as $fieldToHide)
									{
										$excludeFromProfile[]='jform['.$fieldToHide.']';
										if(isset($fields[$fieldToHide]) && $fields[$fieldToHide]->type=='gmap'){
											$excludeFromProfile[]='jform['.$fieldToHide.'_lat]';
											$excludeFromProfile[]='jform['.$fieldToHide.'_lng]';
										}
										$excludeFromProfile[]='jform['.$fieldToHide.'][]';
									}
								}
							break;
						}
					/*}*/
				}
			}
		}
		return $excludeFromProfile;
	}
	
	public static function xmlentities($s) {
		if(substr_count($s,'<p>')==1 && substr_count($s,' ')==0) $s=strip_tags($s);
	    static $patterns = null;
	    static $reps = null;
	    static $tbl = null;
	    if ($tbl === null) {
	        $tbl = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
	        foreach ($tbl as $k => $v) {
	            $patterns[] = "/$v/";
	            $reps[] = '&#' . ord($k) . ';';
	        }
	   }
	  return preg_replace($patterns, $reps, htmlspecialchars($s, ENT_QUOTES, 'UTF-8'));
	}
	
}

class JsnUser extends JUser
{
	private $excludeFromProfile;
	
	function __construct($id){
		//parent::__construct($id);
		//$user=JFactory::getUser($id);
		$app=JFactory::getApplication();
		
		// Override Current Var
		$currentId=$app->getUserState('com_users.edit.profile.id','');
		$app->setUserState('com_users.edit.profile.id',$id);

		//
		if($app->isAdmin())
		{
			require_once(JPATH_SITE . '/administrator/components/com_users/models/user.php');
			$model = new UsersModelUser();
			$userData=$model->getItem($id);
		}
		else
		{
			require_once(JPATH_SITE . '/components/com_users/models/profile.php');
			$model = new UsersModelProfile();
			$userData=$model->getData();
		}
		$exclude=array('email1','email2');
		foreach($userData as $key => $value)
		{
			if(!isset($this->$key) && !in_array($key, $exclude)) $this->$key=$value;
		}

		// Replace Original Current Var
		$app->setUserState('com_users.edit.profile.id',$currentId);
		
		$this->excludeFromProfile=JsnHelper::excludeFromProfile($this);
		
	}
	
	public function getLink() {
		return JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->id,false);
	}
	
	public function getValue($name) {
		if(isset($this->$name)) return $this->$name;
		else return null;
	}
	
	public function setValue($name,$value) {
		$this->$name=$value;
		return true;
	}
	
	public function getField($name,$privacy=false) {
		if(in_array('jform['.$name.']',$this->excludeFromProfile)) return;
		if($name=='name' || $name=='formatname') return $this->getFormatName();
		if($name=='email') {
			JPluginHelper::importPlugin('content');
			return JHtml::_('content.prepare', '<a href="mailto:'.$this->email.'">'.$this->email.'</a>', '', 'jsn_content.content');
		}
		if($name=='status')
		{
			if($this->isOnline())
				return '<sup class="label label-success">OnLine</sup>';
			else
				return '<sup class="label label-important label-danger">OffLine</sup>';
		}
		if(!isset($this->$name) || (empty($this->$name) && $this->$name!="0")) return '';
		if(strpos($name, '_mini')==0)
			$fieldname=$name;
		else 
			$fieldname=substr($name,0,-5);
		static $field_cache=null;
		if(!isset($field_cache[$fieldname]))
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('a.*')->from('#__jsn_fields AS a')->where('a.level = 2')->where('a.published = 1')->where('a.alias = '.$db->quote($fieldname));
			$db->setQuery( $query );
			$field_cache[$fieldname] = $db->loadObject();
		}
		$field=$field_cache[$fieldname];
		
		if($field)
		{
			require_once(JPATH_SITE . '/components/com_users/helpers/html/users.php');
			if(file_exists(JPATH_ADMINISTRATOR . '/components/com_jsn/helpers/fields/'.$field->type.'.php')) require_once(JPATH_ADMINISTRATOR . '/components/com_jsn/helpers/fields/'.$field->type.'.php');
			
			// Load Options
			if(is_string($field->params))
			{
				$registry = new JRegistry;
				$registry->loadString($field->params);
				$field->params = $registry;
			}
			
			// Access to view this field
			$userVisitor=JFactory::getUser();
			$accessVisitor=$userVisitor->getAuthorisedViewLevels();
			if(!($userVisitor->id==$this->id || in_array($field->accessview, $accessVisitor))) return '';
			
			// Check Privacy
			$privacy_name='privacy_'.$name;
			if($privacy && $field->params->get('privacy',0)){
				$privacy_name='privacy_'.$name;
				if($userVisitor->id==$this->id) $privacy_auth=99;
				elseif($userVisitor->id) $privacy_auth=1;
				else $privacy_auth=0;
				
				// Privacy Skip for Admin
				if($userVisitor->authorise('core.edit', 'com_users')) $privacy_auth=99;
				
				$privacy_value=(isset($this->$privacy_name) && !empty($this->$privacy_name) ? $this->$privacy_name : $field->params->get('privacy_default',0));
				if($privacy_auth<$privacy_value) return '';
			}
			
			// Get Xml
			$class='Jsn'.ucfirst($field->type).'FieldHelper';
			if(class_exists($class)) 
			{
				$fieldXml=$class::getXml($field);
				$form=new JForm(null);
				$form->load('<form>'.$fieldXml.'</form>');
				$form->setValue($fieldname,null,$this->$name);
				$method=$field->type;
				if (JHtml::isRegistered('users.'.$field->id)) return JHtml::_('users.'.$field->id, $this->$name);
				elseif (JHtml::isRegistered('users.'.$field->alias)) return JHtml::_('users.'.$field->alias, $this->$name);
				elseif (JHtml::isRegistered('users.'.$field->type)) return JHtml::_('users.'.$field->type, $this->$name);
				elseif (JHtml::isRegistered('jsn.'.$field->type)) return JHtml::_('jsn.'.$field->type, $form->getField($fieldname));
				else return JHtml::_('users.value', $this->$name);
			}
		}
		return '';
	}
	
	public function isOnline() {
		return JsnHelper::isOnline($this->id);
	}
	
	public function getFormatName() {
		return JsnHelper::getFormatName($this);
	}
	
	public function getUserError() {
		return implode("\n",$this->_errors);
	}

}