<?php

$lang = &$GLOBALS['TL_LANG']['tl_filter_config'];

/**
 * Fields
 */
$lang['title']            = ['Title', 'Please enter a title.'];
$lang['name']             = ['Name', 'Please enter a form name.'];
$lang['dataContainer']    = ['Data container', 'Choose the desired data container.'];
$lang['method']           = ['HTTP-Method', 'Select the form method (GET or POST).'];
$lang['filterFormAction'] = ['Action', 'Select the url, where the form data should be submitted to.'];
$lang['renderEmpty']      = ['Render empty', 'Enable, if all form fields should be rendered without submitted/session data.'];
$lang['template']         = ['Template', 'Select the form template.'];
$lang['cssClass']         = ['CSS class', 'Here you can enter one or more classes.'];
$lang['published']        = ['Publish Filter', 'Make the Filter publicly visible on the website.'];
$lang['start']            = ['Show from', 'Do not publish the Filter on the website before this date.'];
$lang['stop']             = ['Show until', 'Unpublish the Filter on the website after this date.'];
$lang['tstamp']           = ['Revision date', ''];

$lang['resetFilterInitial']     = ['Reset filter on page call', 'Select this option to always reset the filter when the page is loaded.'];


/**
 * Legends
 */
$lang['general_legend']  = 'General settings';
$lang['config_legend']   = 'Configuration';
$lang['template_legend'] = 'Template settings';
$lang['expert_legend']   = 'Expert settings';
$lang['publish_legend']  = 'Publish settings';

/**
 * Buttons
 */
$lang['new']    = ['New Filter', 'Filter create'];
$lang['edit']   = ['Edit Filter', 'Edit Filter ID %s'];
$lang['copy']   = ['Duplicate Filter', 'Duplicate Filter ID %s'];
$lang['delete'] = ['Delete Filter', 'Delete Filter ID %s'];
$lang['toggle'] = ['Publish/unpublish Filter', 'Publish/unpublish Filter ID %s'];
$lang['show']   = ['Filter details', 'Show the details of Filter ID %s'];
