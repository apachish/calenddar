<?php 
/*
* Copyright Copyright (C) 2014 - Kim Pittoors
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html 
*/
defined('_JEXEC') or die('Restricted access'); 
// Access check.
if (!JFactory::getUser()->authorise('adduserfrontend.createuser', 'com_adduserfrontend')) 
{
	return JError::raiseWarning(404, JText::_('')); // Display nothing because controller already does show that message also
}
$doc = JFactory::getDocument();
$style='
    @import "http://fonts.googleapis.com/css?family=Montserrat:300,400,700";
    .rwd-table {
        margin: 1em 0;
        min-width: 100px;
    }
    .rwd-table tr {
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }
    .rwd-table th {
        display: none;

    }
    .rwd-table td {
        display: block;
    }
    .rwd-table td:first-child {
        padding-top: .5em;
    }
    .rwd-table td:last-child {
        padding-bottom: .5em;
    }
    .rwd-table td:before {
        content: attr(data-th) ": ";
        font-weight: bold;
        width: 6.5em;
        display: inline-block;
    }
    @media (min-width: 480px) {
        .rwd-table td:before {
            display: none;
        }
    }
    .rwd-table th, .rwd-table td {
        text-align: center;
    }
    @media (min-width: 480px) {
        .rwd-table th, .rwd-table td {
            display: table-cell;
            padding: .25em .5em;
        }
        .rwd-table th:first-child, .rwd-table td:first-child {
            padding-left: 0;
        }
        .rwd-table th:last-child, .rwd-table td:last-child {
            padding-right: 0;
        }
    }

    body {
        padding: 0 2em;
        font-family: Montserrat, sans-serif;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        color: rgb(252, 252, 251);
        background:#F4F4F4;
    }

    h1 {
        font-weight: normal;
        letter-spacing: -1px;
        color: #34495E;
    }

    .rwd-table {
        background: #F4F4F4;
        color: #949292;
        border-radius: .4em;
        overflow: hidden;
    }
    .rwd-table tr {
        border-color: #46627f;
    }
    .rwd-table th, .rwd-table td {
        margin: .5em 1em;
    }
    @media (min-width: 480px) {
        .rwd-table th, .rwd-table td {
            padding: 1em !important;
        }
    }
    .rwd-table th, .rwd-table td:before {
        color: #90A28B;
    }
    .searchBox {
        position: relative;
        padding: 10px 80px;
    }
    input {
        box-sizing: border-box;
        width: 40%;
        border: 1px solid #999;
        background: #fff;
        padding: 10px;

    }
    button {
        height: 20px;
        background-color: #555;
        padding: 0 10px;
        border: none;
        color: #fff;
        position: absolute;
        top: 10px;
        right: 9px;
    }
    button:before {
        position: absolute;
        display: block;
        height: 40px;
        top: 0px;
        left: -10px;
        width: 10px;
        background: #fff;
        border-left: 1px solid #999;
    }';
$doc->addStyleDeclaration($style);

?>
<div class='searchBox'>
    <button id='button_search'>Search</button>

    <input type='text' class="form-control" id="keyword"  placeholder='search...'/>
</div>
<!--<h1>RWD List to Table</h1>-->
<div class="resultsearch">

<table class="rwd-table">
    <tr>
        <th><?php echo JText::_('LIST_ROW')?></th>
        <th><?php echo JText::_('LIST_NAME')?></th>
        <th><?php echo JText::_('LIST_TELEPHON')?></th>
        <th><?php echo JText::_('LIST_EDIT')?></th>
    </tr>
    <?php $i=1; foreach( $this->customer as $customer){
        $user = JFactory::getUser($customer->id);
        if(!in_array(3,$user->groups) && in_array(2,$user->groups)){
        ?>
    <tr>
        <td data-th="<?php echo JText::_('LIST_ROW')?>"><span><?php echo $i;?></span></td>
        <td data-th="<?php echo JText::_('LIST_NAME')?>"><span><?php echo $customer->name;?></span></td>
        <td data-th="<?php echo JText::_('LIST_TELEPHON')?>">
            <a href="index.php?option=com_adduserfrontend&view=adduserfrontend&userid=<?php echo  $customer->id;?>&type=edit&telephon=<?php echo str_replace('"','',$this->gettelephon($customer->id));?>">
            <span><?php echo str_replace('"','',$this->gettelephon($customer->id));?></span>
        </a>
        </td>
        
        <td data-th="<?php echo JText::_('LIST_EDIT')?>"><span>

            </span></td>
    </tr>
    <?php $i++;} }?>
</table>
</div>

<!--<p>&larr; Drag window (in editor or full page view) to see the effect. &rarr;</p>-->

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#button_search').live('click', function() {
            var searchKeyword = jQuery('#keyword').val();
            if (searchKeyword.length >= 3) {
                jQuery.ajax(
                    {
                        url : "index.php?option=com_conversation&task=searched",
                        type: "POST",
                        data :{search_tel:searchKeyword},
                        success:function(data, textStatus, jqXHR)
                        {
                                jQuery('.resultsearch').html(data);

                        }
                    });
            }
        });
    });

    // var ret = jQuery.parseJSON(data);
    //                        var opt;
    //                        //console.log(ret[0].Name);// alert(ret.state); alert(data);
    //                       Jquery.each(ret ,function(_index, _val){
    //                            //alert( _val.Name);
    //console.log(_val.name);
    ////                            opt=opt+"<option value='"+_val.City+"'>"+_val.Name+"</option> ";
    //                        });
    //                        jQuery('.city').html(opt);
</script>
