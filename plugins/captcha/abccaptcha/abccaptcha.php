<?php
/**
 * Author     Hadi Pourabbas | info@abccom.biz | http://www.abccom.biz
 * Copyright  Copyright (C) 2013 Alpabet of Communications Co. All Rights Reserved.
 * License    GNU GPL v3 or later
 */
 
// No direct access to this file
defined('_JEXEC') or die;

jimport('joomla.filesystem.file'); 

class PlgCaptchaABCcaptcha extends JPlugin
{
	
	protected $autoloadLanguage = true;
	protected $_plugin_url;
	protected $_temp_url;
	protected $_temp_path;
	
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();		
	}
	 
	public function onInit($id)
	{
		// initialize variables		
		$document = JFactory::getDocument();
		$this->_plugin_url = JURI::root() . 'plugins/captcha/abccaptcha/';
		$this->_temp_url = JURI::root() . 'tmp/abccaptcha/';
			
		// add stylesheet and script
		JHtml::stylesheet($this->_plugin_url . 'stylesheet.css', array(), true);
		
		require_once JPATH_ROOT . '/plugins/ajax/abccaptcha/createimage.php';
		createImage();
			
		JHtml::_('script', $this->_plugin_url . 'script.js');
		
		// add inline script
		$document->addScriptDeclaration(
			'window.addEvent(\'domready\', function()
			{
				$(\'abccaptcha_newcode\').addEvent(\'click\', function(e) {
					e.stop(); 
					ABCCaptchaGetImage(\'' . $this->_temp_url . '\');
				});				
				
			});'
		);			
		
		return true;
	}

	public function onDisplay($name, $id, $class)
	{
		// initialize variables
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		
		if (!$code = $session->get('plg_abccaptcha.captchacode'))
		{	
			$code = rand(10000, 99999);
			$session->set('plg_abccaptcha.captchacode', $code);
		}
		
		if (!$filename = $session->get('plg_abccaptcha.imagefilename'))
		{
			$filename = $this->_plugin_url . 'noimage.png';
		}
		else
		{
			$filename = $this->_temp_url . $filename;
		}
		
		$ajax = '<span id="abccaptcha_newcode" class="refresh">' . 
				JText::_('PLG_CAPTCHA_ABCCAPTCHA_NEW_CODE') . 
				'</span>';
		
		return 
			'<div id="abccaptcha">' . 
				'<a title="' . JText::_('PLG_CAPTCHA_ABCCAPTCHA_ABCCOM') . '" class="credits" href="http://www.abccom.biz" target="_blank">' . 
					JText::_('PLG_CAPTCHA_ABCCAPTCHA_CREDITS') . '</a>' . 
				'<img id="abccaptcha_image" width="121" height="60" src="' . $filename . '" />' . 
				$ajax . 
				'<input type="text" name="abccaptcha_answer" id="abccaptcha_answer" />' .
				'<div style="display:none;" id="abccaptcha_message">' . 
					JText::_('PLG_CAPTCHA_ABCCAPTCHA_ERROR_PLUGIN') . 
				'</div>' . 
			'</div>';
	}

	public function onCheckAnswer($code = null)
	{
		// initialize variables
		$session = JFactory::getSession();
		
		if (!$code)
		{
			$useranswer = JRequest::getString('abccaptcha_answer');		
		}
		else
		{
			$useranswer = $code;
		}
		
		$answer = $session->get('plg_abccaptcha.captchacode');
		
		if (!$useranswer)
		{
			$this->_subject->setError(JText::_('PLG_ABCCAPTCHA_ERROR_EMPTY_CODE'));
			return false;
		}
		
		if ($useranswer != $answer) 
		{
			$this->_subject->setError(JText::_('PLG_ABCCAPTCHA_ERROR_CODE'));
			return false;
		}

		return true;		
	}
	
}
	