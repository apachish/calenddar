<?php
/**
 * @version     1.0.0
 * @package     com_smsing
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar <apachish@gmail.com> - http://www.bmsystem.ir
 */
// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_smsing', JPATH_ADMINISTRATOR);
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_smsing.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_smsing' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">
        <table class="table">
            <tr>
			<th><?php echo JText::_('COM_SMSING_FORM_LBL_MESSAGE_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SMSING_FORM_LBL_MESSAGE_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SMSING_FORM_LBL_MESSAGE_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SMSING_FORM_LBL_MESSAGE_MESSAGE'); ?></th>
			<td><?php echo $this->item->message; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_SMSING_FORM_LBL_MESSAGE_STATE_MESSAGE'); ?></th>
			<td><?php echo $this->item->state_message; ?></td>
</tr>

        </table>
    </div>
    <?php if($canEdit && $this->item->checked_out == 0): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_smsing&task=message.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_SMSING_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_smsing.message.'.$this->item->id)):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_smsing&task=message.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_SMSING_DELETE_ITEM"); ?></a>
								<?php endif; ?>
    <?php
else:
    echo JText::_('COM_SMSING_ITEM_NOT_LOADED');
endif;
?>
