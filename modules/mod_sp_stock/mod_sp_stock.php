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
    defined('_JEXEC') or die('Restricted access');
    //Parameters
    $ID 				= $module->id;
    // Include helper.php
    require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');
    $document 			= JFactory::getDocument();
    $helper 			= new Mod_SP_Stock($params);
    $data 				= array();
    $data['chart']		= $helper->getChart();
    $d 					= $helper->getQuote();
    $data['quote']		= $d['query']['results']['quote'];
    $req 				=  JRequest::get('post');
$list = Mod_SP_Stock::getgroup();
$permitiongroup = Mod_SP_Stock::getpermissiongroup();
$permitionkargozari = Mod_SP_Stock::getpermissionkargozari();

    if( isset($req['sp_stock_request']) and $req['sp_stock_request']=='true' and  $ID==$req['ID']) {
        require(JModuleHelper::getLayoutPath(basename(dirname(__FILE__)))); die;
    }

    if (is_array($data)) {
        $document->addStylesheet(JURI::base(true) . '/modules/'.basename(dirname(__FILE__)).'/assets/css/sp_stock.css');
        echo '<div class="' .$params->get('moduleclass_sfx').'" id="sp-stock-'.$ID.'">';
        require(JModuleHelper::getLayoutPath(basename(dirname(__FILE__))));
        echo '</div>';
    }

    if($params->get('tooltip_display')==='true' ):
    ?>

    <script type="text/javascript">
        window.addEvent('domready', function(){
                var tooltip = new Element('div',{'id': 'sp-stock-tooltip-holder-<?php echo $ID?>', 'class':'sp-stock-tooltip-holder'}).inject(document.body,'top');
                tooltip.setStyles({
                        'opacity':0,
                        'position':'absolute',
                        'z-index':1000
                });

                $$('.sp-stock-symbol').addEvents({
                        'mousemove': function(e){
                            pagey = e.page.y;
                            tooltip.setStyles({'top':pagey+10});
                            tooltip.setStyles({'left':(e.page.x) + 10});
                        },
                        'mouseenter': function(e){
                            boxhtml =  this.getParent('li').getChildren('.sp-stock-tooltip-data').get('html');
                            tooltip.set('html',boxhtml);
                            tooltip.fade('in');
                        },

                        'mouseleave': function(){
                            tooltip.fade('out');
                        }
                });
        });
    </script>
    <?php
        endif;