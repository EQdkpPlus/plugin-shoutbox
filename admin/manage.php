<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2008 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev$
 *
 * $Id$
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');


// -- Check user permission ---------------------------------------------------
$user->check_auth('a_shoutbox_delete');


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  message_die('The Shoutbox plugin is not installed.');
}


// -- Init WPFC Admin ---------------------------------------------------------
$wpfccore->InitAdmin();


// -- checkall? ---------------------------------------------------------------
$checkall = false;
if ($_GET['checkall'])
{
  $checkall = true;
}


// -- delete array ------------------------------------------------------------
$delete_array = array();
if ($_POST['shoutbox_delete'])
{
  $delete_array = $_POST['shoutbox_id'];
}


// -- delete all shoutbox entries in array ------------------------------------
foreach ($delete_array as $id)
{
  // delete
  $shoutbox->deleteShoutboxEntry($id);
}


// -- pagination --------------------------------------------------------------
// get total and start
$total_entries = $shoutbox->getNumShoutboxEntries();
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;
// pagination
$pagination = generate_pagination('settings.php'.$SID, $total_entries, SHOUTBOX_PAGE_LIMIT, $start);


// -- display entries ---------------------------------------------------------
// get all shoutbox entries
$shoutbox_entries = $shoutbox->getShoutboxEntries($start, SHOUTBOX_PAGE_LIMIT);
// output each entry in one line
foreach ($shoutbox_entries as $entry)
{
  $tpl->assign_block_vars('sb_row', array (
    'class'    => $eqdkp->switch_row_class(),
    'id'       => $entry['id'],
    'date'     => date($user->lang['sb_date_format'], $entry['date']),
    'name'     => get_coloredLinkedName($entry['name']),
    'text'     => $shoutbox->getCleanOutput($entry['text']),
    'selected' => ($checkall == true ? (' checked="checked"') : '')
  ));
}


// -- Template ----------------------------------------------------------------
$tpl->assign_vars(array (
  // form
  'F_CONFIG'          => 'manage.php' . $SID,
  'F_MARK_CLASS'      =>  $eqdkp->switch_row_class(),
  'FOOTCOUNT'         => sprintf($user->lang['sb_footer'], $total_entries, SHOUTBOX_PAGE_LIMIT),

  // pagination
  'START'             => $start,
  'PAGINATION'        => $pagination,

  // language
  'L_DATE'            => $user->lang['sb_adm_date'],
  'L_NAME'            => $user->lang['sb_adm_name'],
  'L_TEXT'            => $user->lang['sb_adm_text'],
  'L_SELECT_ALL'      => $user->lang['sb_adm_select_all'],
  'L_SELECT_NONE'     => $user->lang['sb_adm_select_none'],
  'L_DELETE'          => $user->lang['delete'],

  // javascript
  'JS_MARK'           => '<script type="text/javascript" src="./../includes/javascripts/mark.js"></script>',

  // credits
  'JS_ABOUT'          => $jquery->Dialog_URL('About', $user->lang['sb_about_header'], '../about.php', '400', '200'),
  'SB_INFO_IMG'       => '../images/credits/info.png',
  'L_CREDITS'         => $user->lang['sb_credits_part1'].$pm->get_data('shoutbox', 'version').$user->lang['sb_credits_part2'],
));


// -- EQDKP -------------------------------------------------------------------
$eqdkp->set_vars(array (
  'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['shoutbox'],
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'template_file' => 'admin/manage.html',
  'display'       => true
  )
);

?>
