<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


JLoader::register('JHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');
JHtml::register('users.spacer', array('JHtmlUsers', 'spacer'));


$fieldsets = $this->form->getFieldsets();
if (isset($fieldsets['core']))   unset($fieldsets['core']);
if (isset($fieldsets['params'])) unset($fieldsets['params']);

foreach ($fieldsets as $group => $fieldset): // Iterate through the form fieldsets
	$fields = $this->form->getFieldset($group);
	$count=0;
	$empty=array();
	foreach ($fields as $field)
	{
		if(!$field->hidden && !in_array($field->name,$this->excludeFromProfile))
		{
			if($field->value=='' && $this->config->get('hideempty',0)) $empty[]=$field->name; 
			else $count+=1;
		}
	}
	if ($count) : 
?>
<fieldset id="<?php echo $group;?>">
	<?php if (isset($fieldset->label)): // If the fieldset has a label set, display it as the legend.?>
	<legend><?php echo JText::_($fieldset->label); ?></legend>
	<?php endif;?>
	<dl class="dl-horizontal">
	<?php foreach ($fields as $field):
		if (!$field->hidden && !in_array($field->name,$empty) && !in_array($field->name,$this->excludeFromProfile) && $field->fieldname!='avatar' && $field->fieldname!='registerdate' && $field->fieldname!='lastvisitdate') :?>
		<dt class="<?php echo substr($field->name,6,-1); ?>Label"><?php echo $field->title; ?></dt>
		<dd class="<?php echo substr($field->name,6,-1); ?>Value"><?php 
			if (JHtml::isRegistered('users.'.$field->id)) echo JHtml::_('users.'.$field->id, $field->value);
			elseif (JHtml::isRegistered('users.'.$field->fieldname)) echo JHtml::_('users.'.$field->fieldname, $field->value);
			elseif (JHtml::isRegistered('users.'.$field->type)) echo JHtml::_('users.'.$field->type, $field->value);
			elseif (JHtml::isRegistered('jsn.'.$field->type)) echo JHtml::_('jsn.'.$field->type, $field);
			else echo JHtml::_('users.value', $field->value);
		?></dd>
		<?php endif;?>
	<?php endforeach;?>
	</dl>
</fieldset>
	<?php endif;?>
<?php endforeach;?>
