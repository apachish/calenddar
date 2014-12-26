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

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-4">
	<h1>Login or Register</h1>
	<p>
		<label for="login"><?php echo JText::_('MOD_BM_SLIDE_LOGIN_VALUE_USERNAME') ?></label>
		<input id="modlgn-username" type="text" name="username" placeholder="<?php echo JText::_('MOD_BM_SLIDE_LOGIN_VALUE_USERNAME') ?>" />
	</p>
	<p>
		<label for="password"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
		<input id="modlgn-passwd" type="password" name="password" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" /> 
	</p>
	<p>
    	<input type="submit" name="submit" value="<?php echo JText::_('JLOGIN') ?>">
    	<?php $usersConfig = JComponentHelper::getParams('com_users'); ?> 
        <?php if ($usersConfig->get('allowUserRegistration')) : ?>
            <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid='.UsersHelperRoute::getRegistrationRoute()); ?>" class="bm-register">
            <?php echo JText::_('MOD_BM_SLIDE_LOGIN_REGISTER'); ?> </a>
		<?php endif; ?> 
		
	</p>    
    <input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?> 
</form>