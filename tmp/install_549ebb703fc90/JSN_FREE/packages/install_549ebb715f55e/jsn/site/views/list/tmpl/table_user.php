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
<tr>
<?php if($this->params->def('col1_enable', 0)) : 
	$fields=$this->params->def('col1_fields', array());
?>
<td>

	<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->user->id,false); ?>">
		<?php if(is_array($fields)) foreach($fields as $field) : ?>
			
				<div class="<?php echo $field ?>"><?php  echo $this->user->getField($field,true); ?></div>
			
		<?php endforeach; ?>
	</a>

</td>
<?php endif; ?>

<?php if($this->params->def('col2_enable', 0)) : 
	$fields=$this->params->def('col2_fields', array());
?>
<td>

	<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->user->id,false); ?>">
		<?php if(is_array($fields)) foreach($fields as $field) : ?>
			
				<div class="<?php echo $field ?>"><?php  echo $this->user->getField($field,true); ?></div>
			
		<?php endforeach; ?>
	</a>

</td>
<?php endif; ?>

<?php if($this->params->def('col3_enable', 0)) : 
	$fields=$this->params->def('col3_fields', array());
?>
<td>

	<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->user->id,false); ?>">
		<?php if(is_array($fields)) foreach($fields as $field) : ?>
			
				<div class="<?php echo $field ?>"><?php  echo $this->user->getField($field,true); ?></div>
			
		<?php endforeach; ?>
	</a>

</td>
<?php endif; ?>

<?php if($this->params->def('col4_enable', 0)) : 
	$fields=$this->params->def('col4_fields', array());
?>
<td>

	<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->user->id,false); ?>">
		<?php if(is_array($fields)) foreach($fields as $field) : ?>
			
				<div class="<?php echo $field ?>"><?php  echo $this->user->getField($field,true); ?></div>
		
		<?php endforeach; ?>
	</a>

</td>
<?php endif; ?>

<?php if($this->params->def('col5_enable', 0)) : 
	$fields=$this->params->def('col5_fields', array());
?>
<td>

	<a href="<?php echo JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->user->id,false); ?>">
		<?php if(is_array($fields)) foreach($fields as $field) : ?>
			
				<div class="<?php echo $field ?>"><?php  echo $this->user->getField($field,true); ?></div>
		
		<?php endforeach; ?>
	</a>

</td>
<?php endif; ?>
</tr>