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
        <table class="table" style="text-align: right;">
            <tr>
				<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_ID'); ?></th>
				<td><?php echo $this->item->id; ?></td>
			</tr>
<!-- <tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr> -->
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_CREATE_PROJECT'); ?></th>
			<td><?php echo $this->item->create_project; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_EXPIRATION_PROJECT'); ?></th>
			<td><?php echo $this->item->expiration_project; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_NAME_PROJECT'); ?></th>
			<td><?php echo $this->item->name_project; ?></td>
</tr>
<!-- <tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_EXTERA_FILDE'); ?></th>
			<td><?php echo $this->item->extera_filde; ?></td>
</tr> -->
<tr>
			<th><?php echo JText::_('COM_TRN_FORM_LBL_PROJECT_TYPE_PROJECT'); ?></th>
			<td><?php echo $this->get_name_project($this->item->type_project); ?></td>
</tr>

        </table>
    </div>
    <?php if($canEdit && $this->item->checked_out == 0): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_trn&task=project.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_TRN_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_trn.project.'.$this->item->id)):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_trn&task=project.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_TRN_DELETE_ITEM"); ?></a>
								<?php endif; ?>
    <?php
else:
    echo JText::_('COM_TRN_ITEM_NOT_LOADED');
endif;
?>
