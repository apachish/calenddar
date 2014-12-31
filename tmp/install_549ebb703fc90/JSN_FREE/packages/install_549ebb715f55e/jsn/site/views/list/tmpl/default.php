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

<div class="listusers">
<?php
$cols=$this->params->def('num_columns', 1);
$this->span=12/$cols;
$countUsers=0;
global $JSNLIST_DISPLAYED_ID;
if(is_array($this->items)) foreach($this->items as $item)
{
	$JSNLIST_DISPLAYED_ID=$item->id;
	$this->user = JsnHelper::getUser($item->id);
	if(($countUsers%$cols)==0) echo('<div class="row-fluid">');
	echo $this->loadTemplate('user');
	if(($countUsers%$cols)==($cols-1)) echo('</div>');
	$countUsers+=1;
}
if(($countUsers%$cols)!=0) echo('</div>');
$JSNLIST_DISPLAYED_ID=false;
?>
</div>

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