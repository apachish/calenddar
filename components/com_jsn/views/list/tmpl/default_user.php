<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


?>

<div class="span<?php echo $this->span; ?> col-lg-<?php echo $this->span; ?> jsn_listprofile">
<?php if($this->config->get('avatar',1)) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->user->id,false); ?>">
		<?php echo $this->user->getField('avatar_mini'); ?>
	</a>
<?php endif; ?>

	<h4>
		<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->user->id,false); ?>"><?php echo $this->user->getField('name'); ?></a>
	</h4>
	<?php if($this->config->get('status',1)) : ?>	
		<div class="status"><?php echo $this->user->getField('status'); ?></div>
	<?php endif; ?>
	<div class="customfields">
		<?php 
		$fields=$this->params->def('list_fields', array());
		if(is_array($fields)) foreach($fields as $field) : ?>
			
				<div class="<?php echo $field ?>"><?php  echo $this->user->getField($field,true); ?></div>
			
		<?php endforeach; ?>
	</div>
</div>
