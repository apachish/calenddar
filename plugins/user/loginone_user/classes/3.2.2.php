<?php
// Login One! user plugin for Joomla! 3.x
// $Id: classes/3.2.2.php 25-09-2014 00:00Z robj $
// (C)2011-2014 INNATO BV - www.innato.nl
// **************************************************************************
// A plugin that prevents multiple logins of the same user
// License: see file LICENSE
// @authorcode	gAk9geprut4uCaprEfrazemasPEWec3e
// @filecode	
// **************************************************************************

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Joomla User plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 * @since       1.5
 */
class plgUserLoginOne_User extends JPlugin
{
	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  3.2
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  3.2
	 */
	protected $db;

	/**
	 * Remove all sessions for the user name
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->delete($this->db->quoteName('#__session'))
			->where($this->db->quoteName('userid') . ' = ' . (int) $user['id']);

		$this->db->setQuery($query)->execute();

		return true;
	}

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method sends a registration email to new users created in the backend.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		$mail_to_user = $this->params->get('mail_to_user', 1);

		if ($isnew)
		{
			// TODO: Suck in the frontend registration emails here as well. Job for a rainy day.
			if ($this->app->isAdmin())
			{
				if ($mail_to_user)
				{
					$lang = JFactory::getLanguage();
					$defaultLocale = $lang->getTag();

					/**
					 * Look for user language. Priority:
					 * 	1. User frontend language
					 * 	2. User backend language
					 */
					$userParams = new JRegistry($user['params']);
					$userLocale = $userParams->get('language', $userParams->get('admin_language', $defaultLocale));

					if ($userLocale != $defaultLocale)
					{
						$lang->setLanguage($userLocale);
					}

					$lang->load('plg_user_joomla', JPATH_ADMINISTRATOR);

					// Compute the mail subject.
					$emailSubject = JText::sprintf(
						'PLG_USER_JOOMLA_NEW_USER_EMAIL_SUBJECT',
						$user['name'],
						$config = $this->app->get('sitename')
					);

					// Compute the mail body.
					$emailBody = JText::sprintf(
						'PLG_USER_JOOMLA_NEW_USER_EMAIL_BODY',
						$user['name'],
						$this->app->get('sitename'),
						JUri::root(),
						$user['username'],
						$user['password_clear']
					);

					// Assemble the email data...the sexy way!
					$mail = JFactory::getMailer()
						->setSender(
							array(
								$this->app->get('mailfrom'),
								$this->app->get('fromname')
							)
						)
						->addRecipient($user['email'])
						->setSubject($emailSubject)
						->setBody($emailBody);

					// Set application language back to default if we changed it
					if ($userLocale != $defaultLocale)
					{
						$lang->setLanguage($defaultLocale);
					}

					if (!$mail->Send())
					{
						$this->app->enqueueMessage(JText::_('JERROR_SENDING_EMAIL'), 'warning');
					}
				}
			}
		}
		else
		{
			// Existing user - nothing to do...yet.
		}
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data
	 * @param   array  $options  Array holding options (remember, autoregister, group)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.5
	 */
	public function onUserLogin($user, $options = array())
	{
		$instance = $this->_getUser($user, $options);

		// If _getUser returned an error, then pass it back.
		if ($instance instanceof Exception)
		{
			return false;
		}

		// If the user is blocked, redirect with an error
		if ($instance->get('block') == 1)
		{
			$this->app->enqueueMessage(JText::_('JERROR_NOLOGIN_BLOCKED'), 'warning');

			return false;
		}

		// Authorise the user based on the group information
		if (!isset($options['group']))
		{
			$options['group'] = 'USERS';
		}

		// Check the user can login.
		$result = $instance->authorise($options['action']);

		if (!$result)
		{
			$this->app->enqueueMessage(JText::_('JERROR_LOGIN_DENIED'), 'warning');

			return false;
		}

		// Mark the user as logged in
		$instance->set('guest', 0);

		// Register the needed session variables
		$session = JFactory::getSession();
		$session->set('user', $instance);

		// Check to see the the session already exists.
		$this->app->checkSession();

		// Update the user related fields for the Joomla sessions table.
		$query = $this->db->getQuery(true)
			->update($this->db->quoteName('#__session'))
			->set($this->db->quoteName('guest') . ' = ' . $this->db->quote($instance->guest))
			->set($this->db->quoteName('username') . ' = ' . $this->db->quote($instance->username))
			->set($this->db->quoteName('userid') . ' = ' . (int) $instance->id)
			->where($this->db->quoteName('session_id') . ' = ' . $this->db->quote($session->getId()));
		$this->db->setQuery($query)->execute();

		// Hit the user last visit field
		$instance->setLastVisit();

		return true;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (client, ...).
	 *
	 * @return  object  True on success
	 *
	 * @since   1.5
	 */
	public function onUserLogout($user, $options = array()) {
		$db = JFactory::getDBO();
		
		// get strict mode parameter of login one authentication plug-in
		$strict_mode = $this->get_plugin_param('authentication', 'loginone', 'strict_mode');
		if ( $strict_mode === false ) {
			$strict_mode = 0;
		}

		// J30 cookie domain and path can be configured. need to retrieve settings
		$config = JFactory::getConfig();
		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path = $config->get('cookie_path', '/');

		// delete login one cookie - but only if following applies:
		// -- strict mode = YES and the Remember Me cookie has not been set
		// Otherwise the strict mode = NO will not work i.e. next login will be denied.
		// must come first, in case other apps have already logged out user
		if (isset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username'])])) {
			if ( $strict_mode && !isset($_COOKIE[JApplication::getHash('JLOGIN_REMEMBER')]) ) {
				// delete login one cookie
				setcookie(JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username']), 0, 1, $cookie_path, $cookie_domain);
				// to make sure the variable is emptied
				unset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username'])]);
			}
		}

		$my 		= JFactory::getUser();
		$session 	= JFactory::getSession();

		// Make sure we're a valid user first
		if ($user['id'] == 0 && !$my->get('tmp_user')) {
			return true;
		}
		
		// check whether the loginone authentication plugin has been enabled
		$query= "SELECT * FROM #__extensions WHERE folder='authentication' AND element='loginone'";
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		foreach($results as $res){
			if($res->enabled) {	// authentication plugin loginone has been enabled
				$loginone_auth_enabled = true;
			}
		}
		
		// Check to see if we're deleting the current session
		if ($my->get('id') == $user['id'] && $options['clientid'] == $this->app->getClientId())
		{
			// Hit the user last visit field
			$my->setLastVisit();

			if($loginone_auth_enabled) {	// authentication plugin loginone has been enabled
				// find the user's group_id
				$query= "SELECT * FROM #__user_usergroup_map WHERE user_id='".$my->get('id')."'";
				$db->setQuery( $query );
				$user_group_result = $db->loadObject();
				
				// force logout all users with this userid
				$db->setQuery(
					'DELETE FROM `#__session`' .
					' WHERE `userid` = '.(int) $user['id'] .
					' AND `client_id` = '.(int) $options['clientid']
				);
				$db->query();
			}

			$this_session_id = $session->getId();
			
			// delete the login one cookie if the cookie has value 1
			// if it has value 2, sessions have been aborted without proper logout and
			// cookie must not be removed
			if ( isset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username'])]) ) {
				$cookie_value = $_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username'])];
				if ( $cookie_value == 1 ) {	// this is the only session on this device
					if ( !isset($_COOKIE[JApplication::getHash('JLOGIN_REMEMBER')]) ) {
						// delete login one cookie
						setcookie(JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username']), 0, 1, $cookie_path, $cookie_domain);
						// to make sure the variable is emptied
						unset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username'])]);
					}
				}
			}

			// Destroy the php session for this user
			$session->destroy();

			// Once more logout this session only - in case above destroy not working well
			$db->setQuery(
				"DELETE FROM #__session" .
				" WHERE userid = ".(int) $user['id'].
				" AND client_id = ".(int) $options['clientid'] .
				" AND session_id = '".$this_session_id."'"
			);
			$db->query();
		}
		else {
			if ( isset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username'])]) ) {
				// shamelessly delete login one cookie regardless its value
				setcookie(JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username']), 0, 1, $cookie_path, $cookie_domain);
				// to make sure the variable is emptied
				unset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user['username'])]);
			}
			// Force logout all users with that userid
			$query = $this->db->getQuery(true)
				->delete($this->db->quoteName('#__session'))
				->where($this->db->quoteName('userid') . ' = ' . (int) $user['id'])
				->where($this->db->quoteName('client_id') . ' = ' . (int) $options['clientid']);
			$this->db->setQuery($query)->execute();
		}
		return true;
	}

	/**
	 * This method will return a user object
	 *
	 * If options['autoregister'] is true, if the user doesn't exist yet he will be created
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (remember, autoregister, group).
	 *
	 * @return  object  A JUser object
	 *
	 * @since   1.5
	 */
	protected function _getUser($user, $options = array())
	{
		$instance = JUser::getInstance();
		$id = (int) JUserHelper::getUserId($user['username']);

		if ($id)
		{
			$instance->load($id);

			return $instance;
		}

		// TODO : move this out of the plugin
		$config = JComponentHelper::getParams('com_users');

		// Hard coded default to match the default value from com_users.
		$defaultUserGroup = $config->get('new_usertype', 2);

		$instance->set('id', 0);
		$instance->set('name', $user['fullname']);
		$instance->set('username', $user['username']);
		$instance->set('password_clear', $user['password_clear']);

		// Result should contain an email (check).
		$instance->set('email', $user['email']);
		$instance->set('groups', array($defaultUserGroup));

		// If autoregister is set let's register the user
		$autoregister = isset($options['autoregister']) ? $options['autoregister'] : $this->params->get('autoregister', 1);

		if ($autoregister)
		{
			if (!$instance->save())
			{
				JLog::add('Error in autoregistration for user ' .  $user['username'] . '.', JLog::WARNING, 'error');
			}
		}
		else
		{
			// No existing user and autoregister off, this is a temporary user.
			$instance->set('tmp_user', true);
		}

		return $instance;
	}

	/**
	 * We set the authentication cookie only after login is successfullly finished.
	 * We set a new cookie either for a user with no cookies or one
	 * where the user used a cookie to authenticate.
	 *
	 * @param   array  options  Array holding options
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.2
	 */
	public function onUserAfterLogin($options)
	{
		// Currently this portion of the method only applies to Cookie based login.
		if (!isset($options['responseType']) || ($options['responseType'] != 'Cookie' && empty($options['remember'])))
		{
			return true;
		}

		// We get the parameter values differently for cookie and non-cookie logins.
		$cookieLifetime	= empty($options['lifetime']) ? $this->app->rememberCookieLifetime : $options['lifetime'];
		$length			= empty($options['length']) ? $this->app->rememberCookieLength : $options['length'];
		$secure			= empty($options['secure']) ? $this->app->rememberCookieSecure : $options['secure'];

		// We need the old data to match against the current database
		$rememberArray = JUserHelper::getRememberCookieData();

		$privateKey = JUserHelper::genRandomPassword($length);

		// We are going to concatenate with . so we need to remove it from the strings.
		$privateKey = str_replace('.', '', $privateKey);

		$cryptedKey = JUserHelper::getCryptedPassword($privateKey, '', 'bcrypt', false);

		$cookieName = JUserHelper::getShortHashedUserAgent();

		// Create an identifier and make sure that it is unique.
		$unique = false;

		do
		{
			// Unique identifier for the device-user
			$series = JUserHelper::genRandomPassword(20);

			// We are going to concatenate with . so we need to remove it from the strings.
			$series = str_replace('.', '', $series);

			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('series'))
				->from($this->db->quoteName('#__user_keys'))
				->where($this->db->quoteName('series') . ' = ' . $this->db->quote(base64_encode($series)));

			$results = $this->db->setQuery($query)->loadResult();

			if (is_null($results))
			{
				$unique = true;
			}
		}
		while ($unique === false);

		// If a user logs in with non cookie login and remember me checked we will
		// delete any invalid entries so that he or she can use remember once again.
		if ($options['responseType'] !== 'Cookie')
		{
			$query = $this->db->getQuery(true)
				->delete('#__user_keys')
				->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName))
				->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['user']->username));

			$this->db->setQuery($query)->execute();
		}

		$cookieValue = $cryptedKey . '.' . $series . '.' . $cookieName;

		// Destroy the old cookie.
		$this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path'), $this->app->get('cookie_domain'));

		// And make a new one.
		$this->app->input->cookie->set(
			$cookieName, $cookieValue, $cookieLifetime, $this->app->get('cookie_path'), $this->app->get('cookie_domain'), $secure
		);

		$query = $this->db->getQuery(true);

		if (empty($options['user']->cookieLogin) || $options['responseType'] != 'Cookie')
		{
			// For users doing login from Joomla or other systems
			$query->insert($this->db->quoteName('#__user_keys'));
		}
		else
		{
			$query
				->update($this->db->quoteName('#__user_keys'))
				->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['user']->username))
				->where($this->db->quoteName('series') . ' = ' . $this->db->quote(base64_encode($rememberArray[1])))
				->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName));
		}

		$query
			->set($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['user']->username))
			->set($this->db->quoteName('time') . ' = ' . $cookieLifetime)
			->set($this->db->quoteName('token') . ' = ' . $this->db->quote($cryptedKey))
			->set($this->db->quoteName('series') . ' = ' . $this->db->quote(base64_encode($series)))
			->set($this->db->quoteName('invalid') . ' = 0')
			->set($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName));

		$this->db->setQuery($query)->execute();

		return true;
	}

	/**
	 * This is where we delete any authentication cookie when a user logs out
	 *
	 * @param   array  $options  Array holding options (length, timeToExpiration)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.2
	 */
	public function onUserAfterLogout($options)
	{
		$rememberArray = JUserHelper::getRememberCookieData();

		// There are no cookies to delete.
		if ($rememberArray === false)
		{
			return true;
		}

		list($privateKey, $series, $cookieName) = $rememberArray;

		// Remove the record from the database
		$query = $this->db->getQuery(true);

		$query
			->delete('#__user_keys')
			->where($this->db->quoteName('uastring') . ' = ' . $this->db->quote($cookieName))
			->where($this->db->quoteName('user_id') . ' = ' . $this->db->quote($options['username']));

		$this->db->setQuery($query)->execute();

		// Destroy the cookie
		$this->app->input->cookie->set($cookieName, false, time() - 42000, $this->app->get('cookie_path'), $this->app->get('cookie_domain'));

		return true;
	}

	// function to retrieve parameters from another plug-in - by www.innato.nl
	function get_plugin_param($folder, $element, $param) {
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$query = "SELECT params FROM #__extensions";
		$query .= " WHERE type='plugin'";
		$query .= " AND folder='$folder'";
		$query .= " AND element='$element'";
		$query .= " AND params LIKE '%\"$param\":%'";
		
		$db->setQuery( $query );
		$result = $db->loadResult();
		if ( $result ) {
			$begin_param_string = stripos($result, "\"".$param."\":");
			$begin_pos_param = $begin_param_string + strlen($param);
			$param_sub_string = substr($result, $begin_pos_param + 4);
			$param_result = substr($param_sub_string, 0, stripos($param_sub_string, "\""));
			return $param_result;
		}
		else {
			return false;
		}
	}
}