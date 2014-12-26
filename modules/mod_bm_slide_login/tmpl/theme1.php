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
<link rel="stylesheet" href="templates/protostar/css/colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="templates/protostar/js/jquery.colorbox.js"></script>
<script type="text/javascript">
   jQuery(document).ready(function(){
        /*jQuery( "form" ).submit(function( event ) {
            event.preventDefault();
            var data=jQuery( this ).serialize();
            jQuery.ajax({
                url:'index.php?option=com_users&viwe=login',
                type:'POST',
                data:data,
               success:function(session){
                   var url = "index.php?option=com_dpcalendar&view=calendar";
                   jQuery(location).attr('href',url);

               }
            });
       });*/
       jQuery(".iframe").colorbox({
           iframe:true, width:"80%", height:"80%"
       });
    })
</script>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>"   method="post" id="login-form"  class="form-1">
	<p class="field">
<!--        <img id="passwd" class="tooltip" title="کیبرد" src="modules/mod_bm_slide_login/assets/images/keyboard.png">
-->		<input class="keyboard" id="modlgn-username" type="text" name="username" placeholder="<?php echo JText::_('MOD_BM_SLIDE_LOGIN_VALUE_USERNAME') ?>" />

   <!-- <div class="code ui-corner-all">

        <script>
            $('.qwerty:eq(1)')
                    .keyboard({
                        openOn : null,
                        stayOpen : true,
                        layout : 'qwerty'
                    })
                    .addTyping();
            $('#passwd').click(function(){
                $('.qwerty:eq(1)').getkeyboard().reveal();
            });
        </script>
    </div>-->

			<i class="icon-user icon-large"></i>
	</p>
		<p class="field">
            <input class="keyboard" id="modlgn-passwd" type="password" name="password" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
   <!-- <div class="code ui-corner-all">

            <script>
                $('.qwerty:eq(1)')
                        .keyboard({
                            openOn : null,
                            stayOpen : true,
                            layout : 'qwerty'
                        })
                        .addTyping();
                $('#passwd').click(function(){
                    $('.qwerty:eq(1)').getkeyboard().reveal();
                });
            </script>
    </div>-->

            <i class="icon-lock icon-large"></i>
	</p>
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<div id="form-login-remember" class="control-group checkbox">
		<input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		<label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_BM_SLIDE_LOGIN_REMEMBER_ME') ?></label>
	</div>
	<?php endif; ?>
	<div id="form-login-submit" class="control-group">
		<div class="controls">
			<button type="submit" tabindex="0" name="Submit" class="btn-submit"><?php echo JText::_('JLOGIN') ?></button>
		</div>
	</div>
	<?php
		$usersConfig = JComponentHelper::getParams('com_users'); ?>
		<ul class="unstyled">
		<?php if ($usersConfig->get('allowUserRegistration')) : ?>
			<li>
                <span class="icon-user"></span><a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid='.UsersHelperRoute::getRegistrationRoute()); ?>">
				<?php echo JText::_('MOD_BM_SLIDE_LOGIN_REGISTER'); ?></a>
			</li>
		<?php endif; ?>
			<!--<li>
                <span class="icon-arrow-left"></span><a href="<?php /*echo JRoute::_('index.php?option=com_users&view=remind&Itemid='.UsersHelperRoute::getRemindRoute()); */?>">
				<?php /*echo JText::_('MOD_BM_SLIDE_LOGIN_FORGOT_YOUR_USERNAME'); */?></a>
			</li>-->
			<li>
                <span class="icon-refresh"></span><a class='iframe' href="<?php echo JRoute::_('index.php?option=com_users&view=reset&Itemid='.UsersHelperRoute::getResetRoute()); ?>">
				<?php echo JText::_('MOD_BM_SLIDE_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
                			</li>
		</ul>
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="user.login" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_('form.token'); ?>
<!--    <iframe id="RSS_target" name="RSS_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>-->
</form>
<div style='display:none'>
    <div id='inline_content' style='padding:10px; background:#fff;'>
        <p><strong>This content comes from a hidden element on this page.</strong></p>
        <p>The inline option preserves bound JavaScript events and changes, and it puts the content back where it came from when it is closed.</p>
        <p><a id="click" href="#" style='padding:5px; background:#ccc;'>Click me, it will be preserved!</a></p>

        <p><strong>If you try to open a new Colorbox while it is already open, it will update itself with the new content.</strong></p>
        <p>Updating Content Example:<br />
            <a class="ajax" href="../content/ajax.html">Click here to load new content</a></p>
    </div>
</div>
