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
require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
require_once('./../includes/systems/shoutbox.esys.php');


// -- Check user permission ---------------------------------------------------
$user->check_auth('a_shoutbox_delete');


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  message_die($user->lang['sb_plugin_not_installed']);
}


// -- delete? -----------------------------------------------------------------
$delete_ids = $in->getArray('selected_ids', 'int');
if (is_array($delete_ids) && count($delete_ids) > 0)
{
  foreach ($delete_ids as $delete_id)
  {
    $shoutbox->deleteShoutboxEntry($delete_id);
  }

  $eqdkp->message($user->lang['sb_delete_success'], $user->lang['shoutbox'], 'green');
}


// -- pagination --------------------------------------------------------------
// get total and start
$start = $in->get('start', 0);
$total_entries = $pdh->get('shoutbox', 'count', array());
$limit = 50;
$end = min($start + $limit, $total_entries);
// pagination
$pagination = generate_pagination('manage.php'.$SID, $total_entries, $limit, $start);


// -- display entries ---------------------------------------------------------
$hptt_sort       = $in->get('sort');
$hptt_url_suffix = ($start > 0 ? '&amp;start='.$start : '');
$shoutbox_ids    = $pdh->get('shoutbox', 'id_list', array());
$hptt = new html_pdh_tag_table($systems_shoutbox['pages']['manage'], $shoutbox_ids, $shoutbox_ids);


// -- Template ----------------------------------------------------------------
$tpl->add_js('$(document).ready(function() { Init_RowClick(); });');
$tpl->assign_vars(array (
  // Form
  'F_MANAGE'          => 'manage.php'.$SID,
  'SB_TABLE'          => $hptt->get_html_table($hptt_sort, $hptt_url_suffix, $start, $end),

  // pagination
  'START'             => $start,
  'PAGINATION'        => $pagination,

  // language
  'L_DELETE'          => $user->lang['delete'],
  'L_RESET'           => $user->lang['reset'],

  // credits
  'JS_ABOUT'          => $jquery->Dialog_URL('About', $user->lang['sb_about_header'], '../about.php', '400', '250'),
  'SB_INFO_IMG'       => '../images/credits/info.png',
  'L_CREDITS'         => $user->lang['sb_credits_part1'].$pm->get_data('shoutbox', 'version').$user->lang['sb_credits_part2'],
));


// -- EQDKP -------------------------------------------------------------------
$eqdkp->set_vars(array (
  'page_title'    => $user->lang['shoutbox'].' '.$user->lang['manage'],
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'template_file' => 'admin/manage.html',
  'display'       => true
  )
);

?>
