<?php
/**
 * @package     Brainymore.com
 * @subpackage  mod_bm_slide_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-5 clearfix">
	<p>
		<input id="modlgn-username" type="text" name="username" placeholder="<?php echo JText::_('MOD_BM_SLIDE_LOGIN_VALUE_USERNAME') ?>" />
		<input id="modlgn-passwd" type="password" name="password" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" /> 
	</p>
    <button type="submit" name="submit">
		<i class="icon-arrow-left"></i>
		<span><?php echo JText::_('JLOGIN') ?></span>
	</button>  
    <input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>    
</form>