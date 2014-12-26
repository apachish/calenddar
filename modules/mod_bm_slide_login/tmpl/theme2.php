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

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-2">
	<h1><span class="log-in">Log in</span> or <span class="sign-up">sign up</span></h1>
	<p class="float">
		<label for="login"><i class="icon-user"></i><?php echo JText::_('MOD_BM_SLIDE_LOGIN_VALUE_USERNAME') ?></label>
		<input id="modlgn-username" type="text" name="username" placeholder="<?php echo JText::_('MOD_BM_SLIDE_LOGIN_VALUE_USERNAME') ?>" />
	</p>
	<p class="float">
		<label for="password"><i class="icon-lock"></i><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
		<input id="modlgn-passwd" type="password" name="password" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
	</p>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<div id="form-login-remember" class="control-group checkbox">
		<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		<label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_BM_SLIDE_LOGIN_REMEMBER_ME') ?></label>
	</div>
	<?php endif; ?>
    
	<p class="clearfix"> 
		<?php $usersConfig = JComponentHelper::getParams('com_users'); ?> 
        <?php if ($usersConfig->get('allowUserRegistration')) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid='.UsersHelperRoute::getRegistrationRoute()); ?>" class="bm-register">
            <?php echo JText::_('MOD_BM_SLIDE_LOGIN_REGISTER'); ?> </a>
		<?php endif; ?> 
		<input type="submit" name="submit" value="<?php echo JText::_('JLOGIN') ?>">
	</p>
    <input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
