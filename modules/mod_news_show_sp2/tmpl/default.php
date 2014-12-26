﻿﻿
<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>In this sample is demonstrated how to create a basic navigation
        with jqxTree and jqxSplitter. </title>
    <link rel="stylesheet" href="modules/mod_news_show_sp2/assets/js/jqwidgets/styles/jqx.base.css" type="text/css" />
<!--    <script type="text/javascript" src="conver/modules/mod_news_show_sp2/assets/js/scripts/jquery-1.11.1.min.js"></script>-->
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/scripts/demos.js"></script>
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/jqwidgets/jqxpanel.js"></script>
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/jqwidgets/jqxtree.js"></script>
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/jqwidgets/jqxexpander.js"></script>
    <script type="text/javascript" src="modules/mod_news_show_sp2/assets/js/jqwidgets/jqxsplitter.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            // Create jqxTree
            jQuery('#jqxTree').jqxTree({  height: '100%', width: '100%' });
            jQuery("#splitter").jqxSplitter({ width: 850, height: 400, panels: [{ size: 250 }] });
            jQuery('#jqxTree').on('select', function (event) {
                jQuery("#ContentPanel").html("<div style='margin: 10px;'>" + event.args.element.id + "</div>");
            });
        });
    </script>
</head>
<body class='default'>
<div id="splitter">
    <div>
        <div style="border: none;" id='jqxTree'>
            <ul>
                <li id="Mail" item-expanded='true'>
                    <img style='float: left; margin-right: 5px;' src='../../images/mailIcon.png' /><span
                        item-title="true">Mail</span>
                    <ul>
                        <li id="Calendar" item-expanded='true'>
                            <img style='float: left; margin-right: 5px;' src='../../images/calendarIcon.png' /><span
                                item-title="true">Calendar</span> </li>
                        <li id="Contacts">
                            <img style='float: left; margin-right: 5px;' src='../../images/contactsIcon.png' /><span
                                item-title="true">Contacts</span> </li>
                        <li id="Inbox">
                            <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                item-title="true"> <span>Inbox</span><span style='color: Blue;'> (3)</span></span>
                            <ul>
                                <li id="jQWidgets">
                                    <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                        item-title="true">jQWidgets</span>
                                    <ul>
                                        <li id="Admin">
                                            <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                                item-title="true">Admin</span> </li>
                                        <li id="Corporate">
                                            <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                                item-title="true">Corporate</span> </li>
                                        <li id="Finance">
                                            <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                                item-title="true">Finance</span> </li>
                                        <li id="Other">
                                            <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                                item-title="true">Other</span> </li>
                                    </ul>
                                </li>
                                <li id="Personal">
                                    <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                        item-title="true">Personal</span> </li>
                            </ul>
                        </li>
                        <li id="Deleted Items" item-expanded='true'>
                            <img style='float: left; margin-right: 5px;' src='../../images/recycle.png' /><span
                                item-title="true"> <span>Deleted Items</span><span style='color: Blue;'> (10)</span></span>
                            <ul>
                                <li id="Today">
                                    <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                        item-title="true">Today</span> </li>
                                <li id="Last Week">
                                    <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                        item-title="true">Last Week</span> </li>
                                <li id="Last Month">
                                    <img style='float: left; margin-right: 5px;' src='../../images/folder.png' /><span
                                        item-title="true">Last Month</span> </li>
                            </ul>
                        <li id="Notes">
                            <img style='float: left; margin-right: 5px;' src='../../images/notesIcon.png' /><span
                                item-title="true">Notes</span> </li>
                        <li id="Settings">
                            <img style='float: left; margin-right: 5px;' src='../../images/settings.png' /><span
                                item-title="true">Settings</span> </li>
                        <li id="Favorites">
                            <img style='float: left; margin-right: 5px;' src='../../images/favorites.png' /><span
                                item-title="true">Favorites</span> </li>
                </li>
            </ul>
            </li>
            </ul>
        </div>
    </div>
    <div id="ContentPanel">
    </div>
</div>
</body>
</html>