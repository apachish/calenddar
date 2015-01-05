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

jQuery('#filter_search1').datepicker({
                onSelect: function(dateText, inst) {
                    jQuery('#filter_search2').datepicker('option', 'minDate', new JalaliDate(inst['selectedYear'], inst['selectedMonth'], inst['selectedDay']));
                var dat=jQuery('#filter_search1').val();
                jQuery('#filter_search2').val(dat);}
            });
            jQuery('#filter_search2').datepicker({
                onSelect: function(dateText, inst) {
                    var dat=jQuery('#filter_search2').val();
                jQuery('#filter_search1').val(dat);}
                
            });
            jQuery('.icon-clear').click(function(){
                jQuery('#filter_search2').val('');
                jQuery('#filter_search1').val('');
                jQuery('#filter_search').val('');
                jQuery('#filter_search3').val('');
            })
})
</script>
<form action="<?php echo JRoute::_('index.php?option=com_trn&view=projects'); ?>" method="post" name="adminForm" id="adminForm">

<?php 
echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
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
                <?php if (isset($this->items[0]->id)): ?>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php if ($canDelete): ?>
                            <button  class="btn btn-mini delete-button" type="button" title="حذف"><i class="icon-trash" ></i></button>
                            <button  class="btn btn-mini archives-button" type="button" title="بایگانی"><i ><img src="images/archives.png"</i></button>

                        <?php endif; ?>
                    </th>
                <?php endif; ?>
                    <th class='center'>
                    <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_NAME_PROJECT', 'a.name_project', $listDirn, $listOrder); ?>
                    </th>
    				<th class='cetner'>
				        <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_CREATE_PROJECT', 'a.create_project', $listDirn, $listOrder); ?>
				    </th>
				    <th class='center'>
				    <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_EXPIRATION_PROJECT', 'a.expiration_project', $listDirn, $listOrder); ?>
				    </th>
				    <th class='center'>
				    <?php echo JHtml::_('grid.sort',  'COM_TRN_PROJECTS_TYPE_PROJECT', 'a.type_project', $listDirn, $listOrder); ?>
				    </th>
                     <th class='center'>
                    <?php echo JHtml::_('grid.sort',  'COM_TRN_FORM_LBL_PROJECT_NAME_CUSTOMER', 'a.user_id', $listDirn, $listOrder); ?>
                    </th>
                    <th class='center'>
                    <?php echo JHtml::_('grid.sort',  'COM_TRN_FORM_LBL_RIMAINDER', 'a.reminde', $listDirn, $listOrder); ?>
                    </th>
    				<?php //if ($canEdit || $canDelete): ?>
					   <!-- <th aligen="center"> -->
				        <?php //echo JText::_('COM_TRN_PROJECTS_ACTIONS'); ?>
				        <!-- </th> -->
				    <?php //endif; ?>
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
                <?php if (isset($this->items[0]->id)): ?>
                <td class="center hidden-phone">
                    
                    <input type="checkbox" name="id_list[]" value="<?php echo (int)$item->id; ?>">
                </td>
            <?php endif; ?>
                <td class="center">
                    <a href="<?php echo JRoute::_('index.php?option=com_trn&view=project&id='.(int) $item->id); ?>">
                    <?php echo $item->name_project; ?>
                    </a>
                </td>
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
                <td class="center">
                    <?php  $usered   = &JFactory::getUser($item->user_id); echo $usered->name; ?>
                </td>
                <td class="center">
                    <?php  if($item->reminde){echo $item->reminde.' '.JText::_('COM_TRN_FORM_PROJECT_RIMAIINDER');} ?>
                </td>
            

            			<!-- 	<?php if ($canEdit || $canDelete): ?>
					<td class="center">
						<?php //if ($canEdit): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_trn&task=projectform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button"><i class="icon-edit" ></i></a>
						<?php //endif; ?>
						<?php if ($canEdit)://$canDelete): ?>
							<button data-item-id="<?php echo $item->id; ?>" class="btn btn-mini delete-button" type="button"><i class="icon-edit" ></i></button>
						<?php endif; ?>
					</td>
				<?php endif; ?> -->
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

    // jQuery(document).ready(function () {
    //     jQuery('.delete-button').live('click',function(){
    //                 var item_id = jQuery(this).attr('data-item-id');alert(item_id);
    //     jQuery.ajax(
    //             {
    //                 url : "index.php?option=com_trn&view=project&id="+item_id,
    //                 type: "POST",
    //                 success:function(data, textStatus, jqXHR)
    //                 {
    //                     console.log(data);
    //                 }
    //             })
    //             deleteItem; 
    //     })           
    //     jQuery('.delete-button').live('click',deleteItem);

    // });

    // function deleteItem() {
    //     var item_id = jQuery(this).attr('data-item-id');//alert(item_id);
    //     //if (confirm("<?php echo JText::_('COM_TRN_EDIT_MESSAGE'); ?>")) {
    //                    // alert('<?php echo JRoute::_('index.php?option=com_trn&task=projectform.remove&id=',true, 2) ?>' + item_id);

    //         window.location.href = '<?php echo JRoute::_('index.php?option=com_trn&task=projectform.remove&id=',true, 2) ?>' + item_id;
    //     //}
    // }
    jQuery(document).ready(function () {
        
        jQuery('.js-stools-btn-clear').click(function(){
            jQuery('#filter_search').val('');
            jQuery('#filter_search1').val('');
            jQuery('#filter_search2').val('');
            jQuery('#filter_search3').val('0');

        });
        jQuery('.archives-button').click(function(){
            // var postData = $('form').serialize();alert(postData);
             var checked = []
             jQuery("input[name='id_list[]']:checked").each(function ()
             {
               checked.push(parseInt($(this).val()));
              });
             if (confirm("<?php echo JText::_('COM_TRN_ARCHIVE_MESSAGE'); ?>")) {
                jQuery.ajax({
                    url : "index.php?option=com_trn&task=archiveporoject",
                    type: "POST",
                    data : {formData:checked},
                    success:function(data, textStatus, jqXHR)
                    {
                        console.log(data);
                        window.location.href = '<?php echo JRoute::_('index.php?option=com_trn&view=projects') ?>';
                        // if(data){
                        //     alert('good');
                        // }else{
                        //      alert('bad');
                        // }
                    }
                })
            }    
        })  
        jQuery('.delete-button').click(function(){
            // var postData = $('form').serialize();alert(postData);
             var checked = []
             jQuery("input[name='id_list[]']:checked").each(function ()
             {
               checked.push(parseInt($(this).val()));
              });
             if (confirm("<?php echo JText::_('COM_TRN_DELETE_MESSAGE'); ?>")) {
                jQuery.ajax({
                    url : "index.php?option=com_trn&task=deleteporoject",
                    type: "POST",
                    data : {formData:checked},
                    success:function(data, textStatus, jqXHR)
                    {
                        console.log(data);
                         window.location.href = '<?php echo JRoute::_('index.php?option=com_trn&view=projects') ?>';

                        // if(data){
                        //     alert('good');
                        // }else{
                        //      alert('bad');
                        // }
                    }
                })
            }    
        })  
        
    })

</script>


