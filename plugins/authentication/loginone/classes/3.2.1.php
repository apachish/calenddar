<?php
// Login One! authentication plugin for Joomla! 3.x
// $Id: classes/3.2.1.php 25-09-2014 00:00Z robj $
// (C)2011-2014 INNATO BV - www.innato.nl
// **************************************************************************
// A plugin that prevents multiple log-ins of the same user
// License: see file LICENSE
// @authorcode	gAk9geprut4uCaprEfrazemasPEWec3e
// @filecode	
// **************************************************************************

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// This is the class to be used for J3 version 3.2.1 and higher

class plgAuthenticationLoginOne extends JPlugin
{
	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array   $credentials  Array holding the user credentials
	 * @param   array   $options      Array of extra options
	 * @param   object  &$response    Authentication response object
	 *
	 * @return  boolean
	 *
	 * @since   1.5
	 */
	public function onUserAuthenticate($credentials, $options, &$response) {
		// set the Joomla! application object
		$mainframe = JFactory::getApplication();
				
		jimport('joomla.user.helper');

		// Get the site language in case needed.
		$lang = JFactory::getLanguage();
		$langTag = substr($lang->getTag(),0,2);
		
		// force language - for testing
		// $langTag = 'en';

		// load the language file from admin/language - this is needed for plug-ins when using JTEXT at front-end
		$this->loadLanguage('plg_authentication_loginone', 'administrator');
			
		// Get Plugin params
		$pluginparams = $this->params;
		$strict_mode = $pluginparams->get( 'strict_mode', 0);

		// Get a database object
		$db		= JFactory::getDbo();

		// get the configured session lifetime in minutes
		$config = JFactory::getConfig();
		$session_lifetime = ( $config->get('lifetime') );
				
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

		// Set up a query
		$query	= $db->getQuery(true);
		$query->select('id, password');
		$query->from('#__users');
		$query->where('username = ' . $db->Quote($credentials['username']));

		$db->setQuery($query);
		$result = $db->loadObject();

		if($result)
		{
			$user = JUser::getInstance($result->id); // Bring this in line with the rest of the system
			// find the user group_id (former gid) - just one user group
			$user_group_id = $this->find_gid($user->id);
			
			// standard Joomla! 3.2.1 password check
			$match = JUserHelper::verifyPassword($credentials['password'], $result->password, $result->id);				

			if (isset($match) && $match === true)
			{
				// Again - Bring this in line with the rest of the system
				$response->username = $user->username;
				$response->email = $user->email;
				$response->fullname = $user->name;

				if (JFactory::getApplication()->isAdmin())
				{
					$response->language = $user->getParam('admin_language');
				}
				else
				{
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
				// take the oldest session as reference
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
							// shopping cart contents when switching between work stations.

							// delete the 'remember me' cookie
							setcookie(JApplication::getHash('JLOGIN_REMEMBER'),0,time()-3600,$cookie_path, $cookie_domain);
						}

						JError::raiseNotice('SOME_ERROR_CODE', JText::_('NOTIFICATION_CANNOT_LOGIN'));

						// log-in denied. redirect
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
			}
			else {
				// Invalid password
				$response->status = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		}
		else
		{
			// Invalid user
			$response->status = JAuthentication::STATUS_FAILURE;
			$response->error_message = JText::_('JGLOBAL_AUTH_NO_USER');
		}
		
		// as of J3.2 
		// Check the two factor authentication
		if ($response->status == JAuthentication::STATUS_SUCCESS)
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

			$methods = UsersHelper::getTwoFactorMethods();

			if (count($methods) <= 1)
			{
				// No two factor authentication method is enabled
				return;
			}

			require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';

			$model = new UsersModelUser;

			// Load the user's OTP (one time password, a.k.a. two factor auth) configuration
			if (!array_key_exists('otp_config', $options))
			{
				$otpConfig = $model->getOtpConfig($result->id);
				$options['otp_config'] = $otpConfig;
			}
			else
			{
				$otpConfig = $options['otp_config'];
			}

			// Check if the user has enabled two factor authentication
			if (empty($otpConfig->method) || ($otpConfig->method == 'none'))
			{
				// Warn the user if he's using a secret code but he has not
				// enabed two factor auth in his account.
				if (!empty($credentials['secretkey']))
				{
					try
					{
						$app = JFactory::getApplication();

						$this->loadLanguage();

						$app->enqueueMessage(JText::_('PLG_AUTH_JOOMLA_ERR_SECRET_CODE_WITHOUT_TFA'), 'warning');
					}
					catch (Exception $exc)
					{
						// This happens when we are in CLI mode. In this case
						// no warning is issued
						return;
					}
				}

				return;
			}

			// Load the Joomla! RAD layer
			if (!defined('FOF_INCLUDED'))
			{
				include_once JPATH_LIBRARIES . '/fof/include.php';
			}

			// Try to validate the OTP
			FOFPlatform::getInstance()->importPlugin('twofactorauth');

			$otpAuthReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorAuthenticate', array($credentials, $options));

			$check = false;

			/*
			 * This looks like noob code but DO NOT TOUCH IT and do not convert
			 * to in_array(). During testing in_array() inexplicably returned
			 * null when the OTEP begins with a zero! o_O
			 */
			if (!empty($otpAuthReplies))
			{
				foreach ($otpAuthReplies as $authReply)
				{
					$check = $check || $authReply;
				}
			}

			// Fall back to one time emergency passwords
			if (!$check)
			{
				// Did the user use an OTEP instead?
				if (empty($otpConfig->otep))
				{
					if (empty($otpConfig->method) || ($otpConfig->method == 'none'))
					{
						// Two factor authentication is not enabled on this account.
						// Any string is assumed to be a valid OTEP.

						return true;
					}
					else
					{
						/*
						 * Two factor authentication enabled and no OTEPs defined. The
						 * user has used them all up. Therefore anything he enters is
						 * an invalid OTEP.
						 */
						return false;
					}
					}

				// Clean up the OTEP (remove dashes, spaces and other funny stuff
				// our beloved users may have unwittingly stuffed in it)
				$otep = $credentials['secretkey'];
				$otep = filter_var($otep, FILTER_SANITIZE_NUMBER_INT);
				$otep = str_replace('-', '', $otep);

				$check = false;

				// Did we find a valid OTEP?
				if (in_array($otep, $otpConfig->otep))
				{
					// Remove the OTEP from the array
					$otpConfig->otep = array_diff($otpConfig->otep, array($otep));

					$model->setOtpConfig($result->id, $otpConfig);

					// Return true; the OTEP was a valid one
					$check = true;
				}
			}

			if (!$check)
			{
				$response->status = JAuthentication::STATUS_FAILURE;
				$response->error_message = JText::_('JGLOBAL_AUTH_INVALID_SECRETKEY');
			}
		}
	}
	
	function find_gid($uid, $return_all = false) {
		// replacement for J15 gid
		// NOTE: in J30 a user can have more than one gid - take the greatest value... however
		// additional customised groups have gid > 8, so if user assigned to group >8 as well as
		// to an admin group (6 <= gid <= 8) always return greatest admin gid
		// if flag $return_all is set, return an array with all assigned user group ids
		$db = JFactory::getDBO();
		$query = "SELECT group_id FROM #__user_usergroup_map WHERE user_id=$uid ORDER BY group_id ASC";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if ($result) {
			if ( $return_all ) {
				// return array of all assigned group ids
				$gid_array = array();
				foreach($result as $res){	// run through result list
					$gid_array[] = $res->group_id;
				}
				return $gid_array;
			}
			else {
				$store_admin_gid = 0;
				foreach($result as $res){	// run through result list
					if ($res->group_id >= '6' && $res->group_id <= '8') {
						// this is an admin group - return greatest admin group number
						$store_admin_gid = $res->group_id;
					}
				}
				if (!$store_admin_gid) {
					// this is not an admin group - return greatest group number
					return $res->group_id;
				}
				else {
					return $store_admin_gid;
				}
			}
		}
		else {
			return false;
		}
	}
}