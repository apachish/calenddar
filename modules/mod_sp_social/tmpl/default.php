<?php
	/*
		# mod_sp_social - Social module by JoomShaper.com
		# Author    	JoomShaper http://www.joomshaper.com
		# Copyright 	2010 - 2013 JoomShaper.com. All Rights Reserved.
		# License	 	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
		# Website 		http://www.joomshaper.com
	*/
    // no direct access
    defined('_JEXEC') or die('Restricted access');	
?>	
	
	<div id="sp_social<?php echo $ID ?>" class="sp_social icon_size_<?php echo $icon_size ?>">
	<ul>
		<?php if ($rss) : //rss ?>
			<li><a <?php echo $target ?> class="sp_social_rss" title="<?php echo JText::_('SP_SOCIAL_RSS') ?>" href="<?php echo $rss ?>"><span><?php echo JText::_('SP_SOCIAL_RSS') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($facebook) : //facebook ?>
			<li><a <?php echo $target ?> class="sp_social_facebook" title="<?php echo JText::_('SP_SOCIAL_FACEBOOK') ?>" href="http://www.facebook.com/<?php echo $facebook ?>"><span><?php echo JText::_('SP_SOCIAL_FACEBOOK') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>
		
		<?php if ($twitter) : //twitter ?>
			<li><a <?php echo $target ?> class="sp_social_twitter" title="<?php echo JText::_('SP_SOCIAL_TWITTER') ?>" href="http://www.twitter.com/<?php echo $twitter ?>"><span><?php echo JText::_('SP_SOCIAL_TWITTER') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($goggleplus) : //google plus ?>
			<li><a <?php echo $target ?> class="sp_social_googleplus" title="<?php echo JText::_('SP_SOCIAL_GOOGLE_PLUS') ?>" href="https://plus.google.com/<?php echo $goggleplus ?>"><span><?php echo JText::_('SP_SOCIAL_GOOGLE_PLUS') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($youtube) : //youtube ?>
			<li><a <?php echo $target ?> class="sp_social_youtube" title="<?php echo JText::_('SP_SOCIAL_YOUTUBE') ?>" href="http://www.youtube.com/<?php echo $youtube ?>"><span><?php echo JText::_('SP_SOCIAL_YOUTUBE') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($pinterest) : //pinterest ?>
			<li><a <?php echo $target ?> class="sp_social_pinterest" title="<?php echo JText::_('SP_SOCIAL_PINTEREST') ?>" href="http://www.pinterest.com/<?php echo $pinterest ?>"><span><?php echo JText::_('SP_SOCIAL_PINTEREST') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($linkedin) : //linkedin ?>
			<li><a <?php echo $target ?> class="sp_social_linkedin" title="<?php echo JText::_('SP_SOCIAL_LINKEDIN') ?>" href="http://www.linkedin.com/<?php echo $linkedin ?>"><span><?php echo JText::_('SP_SOCIAL_LINKEDIN') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($flickr) : //flickr ?>
			<li><a <?php echo $target ?> class="sp_social_flickr" title="<?php echo JText::_('SP_SOCIAL_FLICKR') ?>" href="http://www.flickr.com/<?php echo $flickr ?>"><span><?php echo JText::_('SP_SOCIAL_FLICKR') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	

		<?php if ($picasa) : //picasa ?>
			<li><a <?php echo $target ?> class="sp_social_picasa" title="<?php echo JText::_('SP_SOCIAL_PICASA') ?>" href="http://www.picasa.com/<?php echo $picasa ?>"><span><?php echo JText::_('SP_SOCIAL_PICASA') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($myspace) : //myspace ?>
			<li><a <?php echo $target ?> class="sp_social_myspace" title="<?php echo JText::_('SP_SOCIAL_MYSPACE') ?>" href="http://www.myspace.com/<?php echo $myspace ?>"><span><?php echo JText::_('SP_SOCIAL_MYSPACE') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($digg) : //digg ?>
			<li><a <?php echo $target ?> class="sp_social_digg" title="<?php echo JText::_('SP_SOCIAL_DIGG') ?>" href="http://www.digg.com/<?php echo $digg ?>"><span><?php echo JText::_('SP_SOCIAL_DIGG') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
		
		<?php if ($skype) : //skype ?>
			<li><a <?php echo $target ?> class="sp_social_skype" title="<?php echo JText::_('SP_SOCIAL_SKYPE') ?>" href="skype:<?php echo $skype ?>?call"><span><?php echo JText::_('SP_SOCIAL_SKYPE') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	

		<?php if ($vimeo) : //vimeo ?>
			<li><a <?php echo $target ?> class="sp_social_vimeo" title="<?php echo JText::_('SP_SOCIAL_VIMEO') ?>" href="http://www.vimeo.com/<?php echo $vimeo ?>"><span><?php echo JText::_('SP_SOCIAL_VIMEO') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	

		<?php if ($delicious) : //delicious ?>
			<li><a <?php echo $target ?> class="sp_social_delicious" title="<?php echo JText::_('SP_SOCIAL_DELICIOUS') ?>" href="http://www.delicious.com/<?php echo $delicious ?>"><span><?php echo JText::_('SP_SOCIAL_DELICIOUS') ?></span><?php echo $custom_text ?></a></li>
		<?php endif; ?>	
	</ul>
	<div style="clear:both"></div>
</div>