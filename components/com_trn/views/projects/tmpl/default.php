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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
//JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canCreate = $user->authorise('core.create', 'com_trn');
$canEdit = $user->authorise('core.edit', 'com_trn');
$canCheckin = $user->authorise('core.manage', 'com_trn');
$canChange = $user->authorise('core.edit.state', 'com_trn');
$canDelete = $user->authorise('core.delete', 'com_trn');
?>
    <link type="text/css" href="<?php echo JURI::root()?>components/com_adduserfrontend/assest/styles/jquery-ui-1.8.14.css" rel="stylesheet" />

    <script type="text/javascript" src="<?php echo JURI::root()?>components/com_adduserfrontend/assest/scripts/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="<?php echo JURI::root()?>components/com_adduserfrontend/assest/scripts/jquery.ui.core.js"></script>
    <script type="text/javascript" src="<?php echo JURI::root()?>components/com_adduserfrontend/assest/scripts/jquery.ui.datepicker-cc.js"></script>
    <script type="text/javascript" src="<?php echo JURI::root()?>components/com_adduserfrontend/assest/scripts/calendar.js"></script>
    <script type="text/javascript" src="<?php echo JURI::root()?>components/com_adduserfrontend/assest/scripts/jquery.ui.datepicker-cc-ar.js"></script>
    <script type="text/javascript" src="<?php echo JURI::root()?>components/com_adduserfrontend/assest/scripts/jquery.ui.datepicker-cc-fa.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery('#filter_search1').datepicker();
})
</script>
<form action="<?php echo JRoute::_('index.php?option=com_trn&view=projects'); ?>" method="post" name="adminForm" id="adminForm">
<table  width='100%'><tr>
<td><input name="filter[search1]" id="filter_search1" value=""  readonly='readonly' class="js-stools-search-string" placeholder="Search" type="text">
</td><td><select name="filter[search2]" id="filter_search2"  >
<option value='0'> انتخاب کنید</option>
<?php 
$variable=$this->get_list_type_project();
foreach ($variable as $key => $value) {
    echo '<option value="'.$value->id.'">'.$value->type_project.'</option>';
}
?>
</select></td><td>
<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?></td></tr></table>
    <div class="js-stools-container-filters hidden-phone clearfix" style="">
        <?php // Load the form filters ?>
        <?php if ($filters) : ?>
            <?php foreach ($filters as $fieldName => $field) : ?>
                <?php if ($fieldName != 'filter_search') : ?>
                    <div class="js-stools-field-filter">
                        <?php echo $field->input; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
        <?php endif; ?>
    </div>
    <table class="table table-striped" id = "projectList" >
        <thead >
            <tr >
                <?php if (isset($this->items[0]->state)): ?>
                    <th class='center'>
                    <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_NAME_PROJECT', 'a.name_project', $listDirn, $listOrder); ?>
                    </th>
                <!--<th width="1%" class="nowrap center">
                    <?php //echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                </th> -->

    				<th class='cetner'>
				        <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_CREATE_PROJECT', 'a.create_project', $listDirn, $listOrder); ?>
				    </th>
				    <th class='center'>
				    <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_EXPIRATION_PROJECT', 'a.expiration_project', $listDirn, $listOrder); ?>
				    </th>

				    <th class='center'>
				    <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_TYPE_PROJECT', 'a.type_project', $listDirn, $listOrder); ?>
				    </th>


<!--     <?php //if (isset($this->items[0]->id)): ?>
        <th width="1%" class="nowrap center hidden-phone">
            <?php //echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
        </th>
    <?php //endif; ?> -->

    				<?php if ($canEdit || $canDelete): ?>
					   <th class="center">
				        <?php echo JText::_('COM_TRN_PROJECTS_ACTIONS'); ?>
				        </th>
				    <?php endif; ?>
                <?php endif; ?>    

    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
            <?php echo $this->pagination->getListFooter(); ?>
        </td>
    </tr>
    </tfoot>
    <tbody>
    <?php foreach ($this->items as $i => $item) : ?>
        <?php $canEdit = $user->authorise('core.edit', 'com_trn'); ?>

        				<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_trn')): ?>
					<?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
				<?php endif; ?>

        <tr class="row<?php echo $i % 2; ?>">

            <?php if (isset($this->items[0]->state)): ?>
                <?php $class = ($canEdit || $canChange) ? 'active' : 'disabled'; ?>
                <td class="center">
                    <a href="<?php echo JRoute::_('index.php?option=com_trn&view=project&id='.(int) $item->id); ?>">
                    <?php echo $item->name_project; ?>
                    </a>
                </td>
<!--                 <td class="center">
                    <a class="btn btn-micro <?php echo $class; ?>"
                       href="<?php echo ($canEdit || $canChange) ? JRoute::_('index.php?option=com_trn&task=projectform.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
                        <?php if ($item->state == 1): ?>
                            <i class="icon-publish"></i>
                        <?php else: ?>
                            <i class="icon-unpublish"></i>
                        <?php endif; ?>
                    </a>
                </td> -->

            	<td class="center">
                    <!--<?php //if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php //echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'projects.', $canCheckin); ?>
				    <?php //endif; ?> -->
				    <?php echo $this->escape($item->create_project); ?>
				</td>
				<td class="center">
					<?php echo $item->expiration_project; ?>
				</td>
				<td class="center">
					<?php echo $this->get_name_project($item->type_project); ?>
				</td>
<!-- 

            <?php if (isset($this->items[0]->id)): ?>
                <td class="center hidden-phone">
                    <?php echo (int)$item->id; ?>
                </td>
            <?php endif; ?> -->

            				<?php if ($canEdit || $canDelete): ?>
					<td class="center">
						<?php if ($canEdit): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_trn&task=projectform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button"><i class="icon-edit" ></i></a>
						<?php endif; ?>
						<?php if ($canDelete): ?>
							<button data-item-id="<?php echo $item->id; ?>" class="btn btn-mini delete-button" type="button"><i class="icon-trash" ></i></button>
						<?php endif; ?>
					</td>
				<?php endif; ?>
            <?php endif; ?>

        </tr>
    <?php endforeach; ?>
    </tbody>
    </table>

    <?php if ($canCreate): ?>
        <a href="<?php echo JRoute::_('index.php?option=com_trn&task=projectform.edit&id=0', false, 2); ?>"
           class="btn btn-success btn-small"><i
                class="icon-plus"></i> <?php echo JText::_('COM_TRN_ADD_ITEM'); ?></a>
    <?php endif; ?>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo JHtml::_('form.token'); ?>
</form>

<script type="text/javascript">

    jQuery(document).ready(function () {
        jQuery('.delete-button').click(deleteItem);
    });

    function deleteItem() {
        var item_id = jQuery(this).attr('data-item-id');
        if (confirm("<?php echo JText::_('COM_TRN_DELETE_MESSAGE'); ?>")) {
            window.location.href = '<?php echo JRoute::_('index.php?option=com_trn&task=projectform.remove&id=', false, 2) ?>' + item_id;
        }
    }
</script>


