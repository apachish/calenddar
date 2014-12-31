<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;

$this->document->setTitle($this->document->title.' - '.JsnHelper::getFormatName($this->data));
$dispatcher	= JEventDispatcher::getInstance();

global $JSNPROFILE_HIDEHEADFIELDS;
global $JSNPROFILE_HIDEFIELDS;
global $JSNSOCIAL;

?>

<?php 
echo(implode(' ',$dispatcher->trigger('renderBeforeProfile',array($this->data,$this->config))));
?>

<div class="row-fluid jsn_profile">
<?php 
$leftSide=$dispatcher->trigger('renderLeftSide',array($this->data,$this->config)); 
?>
<?php if((!$JSNPROFILE_HIDEHEADFIELDS && $avatar=$this->form->getField('avatar')) || count($leftSide)) : ?>
	<div class="span3 col-md-3 text-center jsn_profile_left">
		<?php
		if(!$JSNPROFILE_HIDEHEADFIELDS && $avatar)
			if (JHtml::isRegistered('jsn.'.$avatar->type)) echo JHtml::_('jsn.'.$avatar->type, $avatar);
		echo(implode(' ',$leftSide));
		?>
	</div>
	<div class="span9 col-md-9 profile jsn_profile_right <?php echo $this->pageclass_sfx?>">
<?php else : ?>
	<div class="span12 col-md-12 profile jsn_profile_full <?php echo $this->pageclass_sfx?>">
<?php endif; ?>
<ul class="btn-toolbar pull-right">
<?php if (JFactory::getUser()->id == $this->data->id || JFactory::getUser()->authorise('core.edit', 'com_users')) : JFactory::getApplication()->setUserState('com_users.edit.profile.redirect',JRoute::_('index.php?option=com_jsn&view=profile&id='.$this->data->id.'&Itemid='.JRequest::getVar('Itemid',''),false)); ?>
		<li class="btn-group">
			<a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_users&view=profile&layout=edit&user_id='.$this->data->id,false);?>">
				<span class="icon-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?></a>
		</li>
<?php endif; ?>
<?php if (JFactory::getUser()->id != $this->data->id) :
	$db=JFactory::getDbo();
	$query=$db->getQuery(true)->select($db->quoteName('id'))->from('#__contact_details as c')->where($db->quoteName('user_id').'='.$this->data->id)->where($db->quoteName('published').'=1');
	$db->setQuery($query);
	if($contact=$db->loadResult()) : ?>
		<li class="btn-group">
			<a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_contact&view=contact&id='.$contact,false);?>">
				<span class="icon-envelope"></span> <?php echo JText::_('JGLOBAL_EMAIL'); ?></a>
		</li>
	<?php endif; ?>
<?php endif; ?>
</ul>
<?php if(!$JSNPROFILE_HIDEHEADFIELDS) : ?>
	<div class="page-header">
		<h1>
			<?php echo JsnHelper::getFormatName($this->data); ?>
		
		</h1>
		<?php if($this->config->get('status',1)) : ?>	
			<?php if(JsnHelper::isOnLine((int) $this->data->id)) : ?>
				<sup class="label label-success">OnLine</sup>
			<?php else : ?>
				<sup class="label label-important">OffLine</sup>
			<?php endif; ?>
		<?php endif; ?>
	
	</div>

	<?php 
	$registerdate=$this->form->getField('registerdate');
	$lastvisitdate=$this->form->getField('lastvisitdate');
	if( $registerdate || $lastvisitdate ) : ?>
	<div class="row-fluid jsn_infodate">
		<?php if($registerdate) : ?>
		<div class="span6 col-md-6 text-muted muted">
			<i class="icon-user icon-link"></i> <?php echo JText::_('REGISTEREDDATE'); ?>: <?php echo JHtml::_('date',$this->data->registerdate,JText::_('DATE_FORMAT_LC4')); ?>
		</div>
		<?php endif; ?>
		<?php if($lastvisitdate) : ?>
		<div class="span6 col-md-6 text-muted muted">
			<i class="icon-eye-open"></i> <?php echo JText::_('LASTVISITDATE'); ?>: <?php echo ($this->data->lastvisitdate!='0000-00-00 00:00:00' ?  JHtml::_('date',$this->data->lastvisitdate,JText::_('DATE_FORMAT_LC4')) : JText::_('COM_JSN_NEVER')); ?>
		</div>
		<?php endif; ?>
	</div>
	<hr class="profilehr" />
	<?php endif; ?>
<?php endif; ?>


<?php 
echo(implode(' ',$dispatcher->trigger('renderBeforeFields',array($this->data,$this->config))));
?>

<?php if(!$JSNPROFILE_HIDEFIELDS) : ?>
	
<?php echo $this->loadTemplate('fields'); ?>

<?php echo $this->loadTemplate('params'); ?>

<?php endif; ?>

<?php 
echo(implode(' ',$dispatcher->trigger('renderAfterFields',array($this->data,$this->config))));
?>

</div>
</div>

<?php
$tabs=$dispatcher->trigger('renderTabs',array($this->data,$this->config));
if(count($tabs)) : 
	$titles=array();
	$contents=array();
	$active=true;
	$index=0;
	foreach($tabs as $tab)
	{
		$titles[]='<li'.($active ? ' class="active"' : '').'><a data-toggle="tab" href="#profiletab'.$index.'">'.$tab[0].'</a></li>';
		$contents[]='<div class="tab-pane'.($active ? ' active' : '').'" id="profiletab'.$index.'">'.$tab[1].'</div>';
		$active=false;
		$index+=1;
	}
	
	if(!(count($tabs)==1 && $JSNSOCIAL))
	{
		echo('<ul class="nav nav-tabs" id="profileTabs">');
		echo(implode(' ',$titles));
		echo('</ul>');
	}
	
	echo('<div class="tab-content">');
	echo(implode(' ',$contents));
	echo('</div>');
endif;
?>

<?php 
echo(implode(' ',$dispatcher->trigger('renderAfterProfile',array($this->data,$this->config))));
?>