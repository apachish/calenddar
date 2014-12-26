<?php
// Login One! authentication plugin for Joomla! 3.x
// $Id: classes/3.1.6.php 25-09-2014 00:00Z robj $
// (C)2011-2014 INNATO BV - www.innato.nl
// **************************************************************************
// A plugin that prevents multiple log-ins of the same user
// License: see file LICENSE
// @authorcode	gAk9geprut4uCaprEfrazemasPEWec3e
// @filecode	
// **************************************************************************

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// This is the class to be used for J3 version up to and including 3.1.6

class plgAuthenticationLoginOne extends JPlugin
{
	public function onUserAuthenticate( $credentials, $options, &$response )
	{
		// Get the site language in case needed.
		$lang = JFactory::getLanguage();
		$langTag = substr($lang->getTag(),0,2);
		
		// Get the right language if it exists. If not, defaults to english.
		$lang = JFactory::getLanguage();
		$langTag = substr($lang->getTag(),0,2);
		
		// force language - for testing
		// $langTag = 'en';

		// load the language file from admin/language - this is needed for plug-ins when using JTEXT at front-end
		$this->loadLanguage('plg_authentication_loginone', 'administrator');
			
		// get the configured session lifetime in minutes
		$config = JFactory::getConfig();
		$session_lifetime = ( $config->get('lifetime') );
		// Get Plugin info
		$pluginparams = $this->params;
		$strict_mode = $pluginparams->get( 'strict_mode');
				
		// J30 cookie domain and path can be configured. need to retrieve settings
		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path = $config->get('cookie_path', '/');

		$response->type = 'Joomla';
		// Joomla does not like blank passwords
		if (empty($credentials['password'])) {
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
			return false;
		}
		
		// Initialize variables
		$conditions = '';

		// Get a database object
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id, password');
		$query->from('#__users');
		$query->where('username=' . $db->Quote($credentials['username']));

		$db->setQuery($query);
		$result = $db->loadObject();

		if($result)
		{
			$parts	= explode( ':', $result->password );
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($credentials['password'], $salt);

			if ($crypt == $testcrypt) {
				$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
				$response->email = $user->email;
				$response->fullname = $user->name;
				
				if (JFactory::getApplication()->isAdmin()) {
					$response->language = $user->getParam('admin_language');
				}
				else {
					$response->language = $user->getParam('language');
				}

				$response->status = JAuthentication::STATUS_SUCCESS;
				$response->error_message = '';

				// set default referer if not defined yet - this avoids errors when redirecting
				if ( isset($_SERVER['HTTP_REFERER']) ) {
					$redirect_to_referrer_url = $_SERVER['HTTP_REFERER'];
				}
				else {
					$redirect_to_referrer_url = JURI::root();
				}

				// see whether user has already logged in, maybe from another work station
				$query = "SELECT username, time FROM #__session";
				$query .= " WHERE 1=1";
				$query .= " AND userid=" . $user->id;
				$query .= " ORDER BY time ASC";
				$db->setQuery( $query );
				$session_result = $db->loadObject();

				// do login cookie maintenance if no other sessions active
				if ( !$session_result && isset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username)]) ) {
					setcookie(JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username), 0, 1, $cookie_path, $cookie_domain);
					// to make sure the cookie variable is emptied
					unset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username)]);
				}

				// find the user group_id (former gid)
				$user_group_id = $this->find_gid($user->id);
				
				if ($session_result && ($strict_mode || !isset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username)]))) {	// user is already logged in, and has not ended previous session by just closing browser, i.e. without logging off. When cookie is set, below code section is skipped. When strict_mode is set, user is not allowed multiple log-in from same work station.
				
					if ($user_group_id !== "8") {	// exclude super user group_id 8

						// The Joomla! 'remember me' option and some applications store user
						// credentials in a persistent cookie, even if the application parameters
						// have been set not to use 'remember me'.
						// If Login One! forces a log-out, this cookie leads to browser getting
						// into a loop, trying to re-establish earlier session but not allowed
						// by Login One! etc etc.
						// So we need to make sure the 'remember me' cookie is deleted.
						// In J3.0 the cookie is set in
						//        /libraries/legacy/application/application.php  line 679
						if(isset($_COOKIE[JApplication::getHash('JLOGIN_REMEMBER')])) {
							
							// decided not to save this cookie into a temp cookie before
							// deleting it, because it leads to confusing/unpredictable
							// shopping cart contents when switching betweens work stations.

							// delete the 'remember me' cookie
							setcookie(JApplication::getHash('JLOGIN_REMEMBER'), 0, 1, $cookie_path, $cookie_domain);
						}

						JError::raiseNotice('SOME_ERROR_CODE', JText::_('NOTIFICATION_CANNOT_LOGIN'));

						// user is not being logged on. redirect
						$mainframe = JFactory::getApplication();
						$mainframe->redirect($redirect_to_referrer_url);
					}
					else {
						// user is allowed in
						// always set this cookie.
						// if there is no cookie yet, then value this one 1, else 2
						if ( !isset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username)]) ) {
							$cookie_value = 1;
						}
						else {
							$cookie_value = 2;
						}
						setcookie(JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username), $cookie_value, time()+$session_lifetime*60, $cookie_path, $cookie_domain);
					}
				}
				else {	// user is not already logged in or has ended previous session by just closing browser, i.e. without logging off. Therefore user is allowed to log in.
					// set cookie to log start session for this station
					// this cookie facilitiates detection that user is trying multiple logins from the same station. For example if user ends session by browser closure instead of formally logging out, user should be allowed consequent login from the same station.
					// if there is no cookie yet, then value this one 1, else 2
					if ( !isset($_COOKIE[JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username)]) ) {
						$cookie_value = 1;
					}
					else {
						$cookie_value = 2;
					}
					setcookie(JApplication::getHash('PLG_LOGIN_ONE_PLUGIN'.$user->username), $cookie_value, time()+$session_lifetime*60, $cookie_path, $cookie_domain);
					
					// if 'remember me' cookie has been set, we would like to reduce its
					// lifetime because default is one year. This is not possible
					// in this authent procedure as the cookie is set later.
				}

			} else {
				$response->status = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		}
		else
		{
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
		}
	}
	
	function find_gid($uid) {
		// replacement for J15 gid
		// NOTE: in J16 a user can have more than one gid - take the greatest value... however
		// additional customised groups have gid > 8, so if user assigned to group >8 as well as
		// to an admin group (6 <= gid <= 8) always return greatest admin gid
		$db = JFactory::getDBO();
		$query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id=$uid ORDER BY group_id ASC";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if ($result) {
			$store_admin_gid = 0;
			foreach($result as $res){	// run through result list
				if ($res->group_id >= '6' && $res->group_id <= '8') {
					$store_admin_gid = $res->group_id;
				}
			}
			if (!$store_admin_gid) {
				return $res->group_id;
			}
			else {
				return $store_admin_gid;
			}
		}
		else return false;
	}
}