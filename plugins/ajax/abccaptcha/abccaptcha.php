<?php 
/**
 * Author     Hadi Pourabbas | info@abccom.biz | http://www.abccom.biz
 * Copyright  Copyright (C) 2013 Alpabet of Communications Co. All Rights Reserved.
 * License    GNU GPL v3 or later
 */
 
// No direct access to this file
defined('_JEXEC') or die;

class plgAjaxABCCaptcha extends JPlugin 
{
	
	function onAjaxABCCaptcha() 
	{
		require_once 'createimage.php';
		
		return createImage();
	}
	
}
