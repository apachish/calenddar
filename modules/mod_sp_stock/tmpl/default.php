<?php
    /*------------------------------------------------------------------------
	# mod_sp_stock - Yahoo stock module by JoomShaper.com
	# ------------------------------------------------------------------------
	# author    JoomShaper http://www.joomshaper.com
	# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
	# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Websites: http://www.joomshaper.com
    -------------------------------------------------------------------------*/
    // no direct access
    defined( '_JEXEC' ) or die( 'Restricted access' );

	// symbol,LastTradePriceOnly, Name, StockExchange, LastTradeDate, PriceSales, Change
	// ChangeRealtime,LastTradeDate, PercentChange, LastTradeTime, LastTradeDate, Change_PercentChange,  ChangePercentRealtime
?>
<link rel="stylesheet" href="modules/mod_sp_stock/assets/css/jquery.treeview.css" />
<link rel="stylesheet" href="modules/mod_sp_stock/assets/css/screen.css" />

<script src="modules/mod_sp_stock/assets/js/jquery.cookie.js" type="text/javascript"></script>
<script src="modules/mod_sp_stock/assets/js/jquery.treeview.js" type="text/javascript"></script>

<script type="text/javascript" src="modules/mod_sp_stock/assets/js/demo.js"></script>

    <ul id="browser" class="filetree" style="direction: ltr">
        <?php foreach($list["karguzari"] as $key=>$lis){?>
            <?php if(in_array($lis['id'],$permitionkargozari)){?>
            <li class="closed"><span class="folder"><?php echo $lis['title'];?></span>
                <ul>
                    <?php foreach($list['group'][$key] as $ke=>$gr){ ?>
                        <?php if(in_array($gr['id'],$permitiongroup)){?>
                        <li><span class="folder"><?php echo $gr['title'];?></span>
<!--                            --><?php //if($list['group'][$key][$ke]){?>
                                <ul>
                                    <?php foreach($list['user'][$key][$ke] as $name){?>
                                        <li><span class="file"><?php echo $name['first'].' '.$name['last'];?></span></li>
                                    <?php }?>
                                </ul>
<!--                            --><?php //}?>
                        </li>
                    <?php }?>
                    <?php }?>
                </ul>
            </li>

        <?php
            }
        }?>
<!--            <ul>-->
<!--                <li><span class="file">Item 1.1</span></li>-->
<!--            </ul>-->
<!--        </li>-->
<!--        <li class="closed"><span class="folder">Folder 2</span>-->
<!--            <ul>-->
<!--                <li><span class="folder">Subfolder 2.1</span>-->
<!--                    <ul id="folder21">-->
<!--                        <li><span class="file">File 2.1.1</span></li>-->
<!--                        <li><span class="file">File 2.1.2</span></li>-->
<!--                    </ul>-->
<!--                </li>-->
<!--                <li><span class="file">File 2.2</span></li>-->
<!--            </ul>-->
<!--        </li>-->
<!--        <li class="closed"><span class="folder">Folder 3 (closed at start)</span>-->
<!--            <ul>-->
<!--                <li><span class="file">File 3.1</span></li>-->
<!--            </ul>-->
<!--        </li>-->
<!--        <li><span class="file">File 4</span></li>-->
    </ul>
