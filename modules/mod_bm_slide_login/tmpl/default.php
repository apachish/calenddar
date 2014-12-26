<?php
/**
 * @package     Brainymore.com
 * @subpackage  mod_bm_slide_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_users/helpers/route.php';

JHtml::_('behavior.keepalive');

$theme = $params->get('theme','theme1'); 
$type = $params->get('type','slidedown'); 
$effect = $params->get('effect','hover'); 
$login_label = $params->get('login_label','Login'); 
$width_form = $params->get('width_form','340px');

$class="";
if($type=='full'){$class="bm_form_full";}
?>

<?php if ($params->get('pretext')) : ?>
<div class="pretext">
    <p><?php echo $params->get('pretext'); ?></p>
</div>
<?php endif; ?>
<div id="bm_slide_login_<?php echo $module->id;?>" class="bm_slide_login">
	<?php if($type=='slidedown'):?>
        <div class="bm_login_label"><i class="icon-key icon-small"></i> <?php echo JText::_($login_label);?></div>
        <div class="bm_clear"></div>
    <?php endif; ?>

	<div class="bm_login_from <?php echo $class; ?>" style="width:<?php echo $width_form;?>">
		<?php require __DIR__ . '/' .$theme.'.php'; ?>
	</div>

</div>
<?php if ($params->get('posttext')) : ?>
    <div class="posttext">
        <p><?php echo $params->get('posttext'); ?></p>
    </div>
<?php endif; ?>
<?php if($type=='slidedown'):?>
<?php if($effect=='hover'):?>
	<script>
        jQuery('document').ready(function(){
            jQuery('#bm_slide_login_<?php echo $module->id;?>').hoverIntent({
                over: function(){
                    jQuery('#bm_slide_login_<?php echo $module->id;?> .bm_login_from').slideDown( "fast", function() {
                        // Animation complete.
                    });
                },
                out: function(){
                    jQuery('#bm_slide_login_<?php echo $module->id;?> .bm_login_from').slideUp( "fast", function() {
                        // Animation complete.
                    });
                },
                timeout: 500
            });


        })
    </script>
<?php else: ?>
	<script>
        jQuery('document').ready(function(){
            jQuery('#bm_slide_login_<?php echo $module->id;?> .bm_login_label').click(function(){
                    jQuery('#bm_slide_login_<?php echo $module->id;?> .bm_login_from').toggle( "fast", function() {
                        // Animation complete.
                    });
            });

        })
    </script>
<?php endif;?>
<?php endif;?>