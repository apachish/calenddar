<?php
/*---------------------------------------------------------------
# Package - Joomla Template based on Helix Framework   
# ---------------------------------------------------------------
# Author - JoomShaper http://www.joomshaper.com
# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
# license - PHP files are licensed under  GNU/GPL V2
# license - CSS  - JS - IMAGE files  are Copyrighted material 
# Websites: http://www.joomshaper.com
-----------------------------------------------------------------*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');
$user = JFactory::getUser();
?>

<?php /*Login Module*/ if($this->countModules( 'login' )) : ?>
	<a class="login_link" href="#login" role="button" data-toggle="modal"><?php echo ($user->id>0) ? JText::_('MY_ACCOUNT') : JText::_('JLOGIN'); ?></a>

	<div id="login" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
		<h3><?php echo ($user->id>0) ? JText::_('MY_ACCOUNT') : JText::_('JLOGIN'); ?></h3>
	  </div>
	  <div class="modal-body">
		<jdoc:include type="modules" name="login" style="none" />
	  </div>
	  <div class="modal-footer">
		<button class="btn btn-danger" data-dismiss="modal" aria-hidden="true"><i class="icon-white icon-remove"></i> <?php echo JText::_('CLOSE'); ?></button>
	  </div>
	</div>	
<?php endif; ?>