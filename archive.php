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


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  message_die('The Shoutbox plugin is not installed.');
}


// -- delete? -----------------------------------------------------------------
if ($_GET['deleteid'])
{
  $shoutbox->deleteShoutboxEntry($_GET['deleteid']);
}


// -- pagination --------------------------------------------------------------
// get total and start
$total_entries = $shoutbox->getNumShoutboxEntries();
$start = (isset($_GET['start'])) ? $_GET['start'] : 0;
// pagination
$pagination = generate_pagination('archive.php'.$SID, $total_entries, SHOUTBOX_PAGE_LIMIT, $start);


// -- display entries ---------------------------------------------------------
// get all shoutbox entries
$shoutbox_entries = $shoutbox->getShoutboxEntries($start, SHOUTBOX_PAGE_LIMIT);
// output each entry in one line
foreach ($shoutbox_entries as $entry)
{
  // can delete if owner or admin
  $can_delete = false;
  if (($user->data['user_id'] != ANONYMOUS && $user->data['user_id'] == $shoutbox->getUserIdFromMemberId($entry['member_id'])) ||
      $user->check_auth('a_shoutbox_delete', false))
  {
    $can_delete = true;
  }
  
  $tpl->assign_block_vars('sb_row', array (
    'class'       => $eqdkp->switch_row_class(),
    'id'          => $entry['id'],
    'date'        => date($user->lang['sb_date_format'], $entry['date']),
    'name'        => get_coloredLinkedName($entry['name']),
    'text'        => $shoutbox->getCleanOutput($entry['text']),
    'CAN_DELETE'  => ($can_delete ? 'true' : ''),
  ));
}


// -- Template ----------------------------------------------------------------
$tpl->assign_vars(array (
  // Form
  'F_ARCHIVE'         => 'archive.php'.$SID,
  'F_DELETE_IMG'      => $eqdkp_root_path.'images/global/delete.png',
  'FOOTCOUNT'         => sprintf($user->lang['sb_footer'], $total_entries, SHOUTBOX_PAGE_LIMIT),
  
  // pagination
  'START'             => $start,
  'PAGINATION'        => $pagination,

  // language
  'L_DATE'            => $user->lang['sb_adm_date'],
  'L_NAME'            => $user->lang['sb_adm_name'],
  'L_TEXT'            => $user->lang['sb_adm_text'],
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
