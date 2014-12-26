<?php
/*------------------------------------------------------------------------
# mod_sp_simple_youtube - Youtube Module by JoomShaper.com
# ------------------------------------------------------------------------
# author    JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomshaper.com
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php if ($youtube_id) { ?>
<div class="sp_simple_youtube">
	<iframe title="Simple youtube module by JoomShaper.com" id="sp-simple-youtube<?php echo $uniqid ?>" src="http://www.youtube.com/embed/<?php echo $youtube_id ?>?wmode=transparent" frameborder="0"></iframe>
</div>
<script type="text/javascript">
	window.addEvent("domready", function() {
		spsyt('sp-simple-youtube<?php echo $uniqid ?>', <?php echo $width ?>, <?php echo $height ?>);
	});
	window.addEvent("resize", function() {
		spsyt('sp-simple-youtube<?php echo $uniqid ?>', <?php echo $width ?>, <?php echo $height ?>);
	});
</script>
<?php } else { ?>
	<p>Please enter youtube id.</p>
<?php } ?>