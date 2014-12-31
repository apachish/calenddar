<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

$dispatcher	= JEventDispatcher::getInstance();

?>

<?php 
echo(implode(' ',$dispatcher->trigger('renderBeforeList',array($this->items,$this->config))));
?>

<div class="jsn_list">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h2><?php echo $this->params->get('page_heading'); ?></h2>
	</div>
<?php endif; ?>
<?php

if ($this->params->def('search_enabled', 0))
{
	echo $this->loadTemplate('search');
}

if(count($this->items)>0 && $this->params->def('show_total', 1) && !($this->params->def('search_enabled',0) && !$this->params->def('search_showuser',0) && !JRequest::getVar('search',0))){
	echo('<span class="label label-warning">'.$this->pagination->total.' '.JText::_('COM_JSN_MEMBERS').'</span><br /><br />');
}
if(count($this->items)==0)
{
	?>
	<div class="alert alert-warning">
	<?php echo(JText::_('COM_JSN_NORESULT')); ?>
	</div>
	<?php
}

?>

<style>
 .listusers img{width:40px;}
 .listusers .name,.listusers .formatname{font-weight:bold;}
</style>

<table class="table table-striped listusers">	

<?php
	// Table Header
	if(($this->params->def('col1_enable', 0) && $this->params->def('col1_header', '')) || ($this->params->def('col2_enable', 0) && $this->params->def('col2_header', '')) ||  ($this->params->def('col3_enable', 0) && $this->params->def('col3_header', '')) || ($this->params->def('col4_enable', 0) && $this->params->def('col4_header', '')) || ($this->params->def('col5_enable', 0) && $this->params->def('col5_header', '')))
	{
		echo('<tr>');
		if($this->params->def('col1_enable', 0)) echo('<th>'.JText::_($this->params->def('col1_header', '')).'</th>');
		if($this->params->def('col2_enable', 0)) echo('<th>'.JText::_($this->params->def('col2_header', '')).'</th>');
		if($this->params->def('col3_enable', 0)) echo('<th>'.JText::_($this->params->def('col3_header', '')).'</th>');
		if($this->params->def('col4_enable', 0)) echo('<th>'.JText::_($this->params->def('col4_header', '')).'</th>');
		if($this->params->def('col5_enable', 0)) echo('<th>'.JText::_($this->params->def('col5_header', '')).'</th>');
		echo('</tr>');
	}
?>

<?php
global $JSNLIST_DISPLAYED_ID;
if(is_array($this->items)) foreach($this->items as $item)
{
	$JSNLIST_DISPLAYED_ID=$item->id;
	$this->user = JsnHelper::getUser($item->id);
	echo $this->loadTemplate('user');
}
$JSNLIST_DISPLAYED_ID=false;
?>

</table>

<?php // Add pagination links ?>
<?php //if (!empty($this->items)) : ?>
	<?php if ($this->params->def('show_pagination', 1) && $this->pagination->pagesTotal > 1) : ?>
	<div class="pagination">

		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>

		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
<?php  //endif; ?>
</div>

<?php 
echo(implode(' ',$dispatcher->trigger('renderAfterList',array($this->items,$this->config))));
?>