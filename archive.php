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
 * @copyright   2008-2011 Aderyn
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


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check('shoutbox', PLUGIN_INSTALLED))
{
  message_die($user->lang('sb_plugin_not_installed'));
}


// -- delete? -----------------------------------------------------------------
if ($in->get('delete_id'))
{
  $shoutbox->deleteShoutboxEntry($in->get('delete_id', 0));
}


// -- get all shoutbox id's ---------------------------------------------------
$shoutbox_ids = $pdh->get('shoutbox', 'id_list', array());
$shoutbox_out = array();


// -- build 2D array with [year][month] ---------------------------------------
$date_array = array();
foreach ($shoutbox_ids as $shoutbox_id)
{
  $shoutbox_date       = $pdh->get('shoutbox', 'date', array($shoutbox_id));
  $shoutbox_date_year  = $time->date('Y', $shoutbox_date);
  $shoutbox_date_month = $time->date('m', $shoutbox_date);
  $date_array[$shoutbox_date_year][$shoutbox_date_month][] = $shoutbox_id;
}


// -- output date select on left side -----------------------------------------
foreach ($date_array as $year => $months)
{
  $tpl->assign_block_vars('year_row', array(
    'YEAR' => $year
  ));

  foreach ($months as $month => $ids)
  {
    $tpl->assign_block_vars('year_row.month_row', array(
      'MONTH'     => $time->date('F', $time->mktime(0, 0, 0, $month, 1, $year)),
      'COUNT'     => count($ids),
      'LINK_VIEW' => $eqdkp_root_path.'plugins/shoutbox/archive.php'.$SID.'&amp;year='.$year.'&amp;month='.$month
    ));
  }
}


// -- year/month select? ------------------------------------------------------
$page_title = '';
if ($in->get('year') && $in->get('month'))
{
  // add all shoutbox entries within date/month to the output array
  $shoutbox_out = $date_array[$in->get('year')][$in->get('month')];
  $page_title   = $time->date('F', $time->mktime(0, 0, 0, $in->get('month'), 1, $in->get('year'))).' '.$in->get('year');
}
// -- search? -----------------------------------------------------------------
else if ($in->get('search'))
{
  // loop through all the shoutbox entries and try to find in either username or in text
  foreach ($shoutbox_ids as $shoutbox_id)
  {
    $text   = $pdh->get('shoutbox', 'text',           array($shoutbox_id));
    $member = $pdh->get('shoutbox', 'usermembername', array($shoutbox_id));
    $search = $in->get('search');
    if (strpos($text, $search) !== false || strpos($member, $search) !== false)
      $shoutbox_out[] = $shoutbox_id;
    $page_title = $user->lang('search').': '.sanitize($in->get('search'));
  }
}
// -- last month --------------------------------------------------------------
else if (count($shoutbox_ids) > 0)
{
  // show the last month only
  $shoutbox_date       = $pdh->get('shoutbox', 'date', array($shoutbox_ids[0]));
  $shoutbox_date_year  = $time->date('Y', $shoutbox_date);
  $shoutbox_date_month = $time->date('m', $shoutbox_date);
  $shoutbox_out = $date_array[$shoutbox_date_year][$shoutbox_date_month];
  $page_title   = $time->date('F', $time->mktime(0, 0, 0, $shoutbox_date_month, 1, $shoutbox_date_year)).' '.$shoutbox_date_year;
}


// -- output filtered data ----------------------------------------------------
foreach ($shoutbox_out as $shoutbox_id)
{
  // show a new date row if it's not the same as the last one
  $shoutbox_date = $pdh->get('shoutbox', 'date', array($shoutbox_id));

  // output
  $tpl->assign_block_vars('shoutbox_row', array(
    'ID'      => $shoutbox_id,
    'NAME'    => $pdh->geth('shoutbox', 'usermembername', array($shoutbox_id)),
    'DATE'    => $time->date($user->style['date'], $shoutbox_date),
    'TIME'    => $time->date($user->style['time'], $shoutbox_date),
    'MESSAGE' => $pdh->geth('shoutbox', 'text', array($shoutbox_id))
  ));
}


// -- Template ----------------------------------------------------------------
$tpl->assign_vars(array(
  // Form
  'LINK_ARCHIVE'      => $eqdkp_root_path.'plugins/shoutbox/archive.php'.$SID,
  'S_YEAR'            => $in->get('year', ''),
  'S_MONTH'           => $in->get('month', ''),
  'S_SEARCH'          => $in->get('search', ''),
  'S_COUNT'           => count($shoutbox_out),
  'S_PAGE_TITLE'      => ($page_title != '') ? '&raquo; '.$page_title : '',

  // Admin
  'CAN_DELETE'        => $user->check_auth('a_shoutbox_delete', false),
));


// -- EQDKP -------------------------------------------------------------------
$core->set_vars(array (
  'page_title'    => $user->lang('sb_shoutbox_archive').' '.$page_title,
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'template_file' => 'archive.html',
  'display'       => true
  )
);

?>
