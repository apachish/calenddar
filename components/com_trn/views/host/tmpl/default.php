<?php
/**
 * @version     1.0.0
 * @package     com_trn
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar pahlevansadegh <apachish@gmail.com> - http://bmsystem.ir
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_trn', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_trn.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_trn' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">
        <table class="table">
            <tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_HOST_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_HOST_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_HOST_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_HOST_CREATE_HOST'); ?></th>
			<td><?php echo $this->item->create_host; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_HOST_EXPIRATION_HOST'); ?></th>
			<td><?php echo $this->item->expiration_host; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_HOST_NAME_HOST'); ?></th>
			<td><?php echo $this->item->name_host; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_HOST_EXTERA_FILDE'); ?></th>
			<td><?php echo $this->item->extera_filde; ?></td>
</tr>

        </table>
    </div>
    <?php if($canEdit && $this->item->checked_out == 0): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_trn&task=host.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_TRN_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_trn.host.'.$this->item->id)):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_trn&task=host.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_TRN_DELETE_ITEM"); ?></a>
								<?php endif; ?>
    <?php
else:
    echo JText::_('COM_TRN_ITEM_NOT_LOADED');
endif;
?>
