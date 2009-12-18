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
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../';
include_once('includes/common.php');
require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
require_once('includes/systems/shoutbox.esys.php');


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  message_die($user->lang['sb_plugin_not_installed']);
}


// -- pagination --------------------------------------------------------------
// get total and start
$start = $in->get('start', 0);
$total_entries = $pdh->get('shoutbox', 'count', array());
$limit = 50;
$end = min($start + $limit, $total_entries);
// pagination
$pagination = generate_pagination('archive.php'.$SID, $total_entries, $limit, $start);


// -- display entries ---------------------------------------------------------
$hptt_sort       = $in->get('sort');
$hptt_url_suffix = ($start > 0 ? '&amp;start='.$start : '');
$shoutbox_ids    = $pdh->get('shoutbox', 'id_list', array());
$hptt = new html_pdh_tag_table($systems_shoutbox['pages']['archive'], $shoutbox_ids, $shoutbox_ids);


// -- Template ----------------------------------------------------------------
$tpl->add_js('$(document).ready(function() { Init_RowClick(); });');
$tpl->assign_vars(array (
  // Form
  'F_MANAGE'          => 'admin/manage.php'.$SID,
  'CAN_DELETE'        => $user->check_auth('a_shoutbox_delete', false),
  'SB_TABLE'          => $hptt->get_html_table($hptt_sort, $hptt_url_suffix, $start, $end),
  'COLSPAN'           => $user->check_auth('a_shoutbox_delete', false) ? 5 : 4,

  // pagination
  'START'             => $start,
  'PAGINATION'        => $pagination,

  // language
  'L_DELETE'          => $user->lang['delete'],
));


// -- EQDKP -------------------------------------------------------------------
$eqdkp->set_vars(array (
  'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['sb_shoutbox_archive'],
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'template_file' => 'archive.html',
  'display'       => true
  )
);

?>
