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


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  message_die($user->lang['sb_plugin_not_installed']);
}


// -- Check user permission ---------------------------------------------------
$user->check_auth('a_shoutbox_');


// -- save? -------------------------------------------------------------------
if ($in->get('save_settings'))
{
  // is use_user change?
  if ($in->get('sb_use_users', 0) != $core->config['shoutbox']['sb_use_users'])
  {
    // convert to member?
    if ($in->get('sb_use_users', '0') == '1')
    {
      $shoutbox->convertFromMemberToUser();
    }
    else
    {
      $shoutbox->deleteAllEntries();
    }
  }

  // take over new values
  $savearray = array(
    'sb_use_users' => $in->get('sb_use_users', 0),
  );

  // update configuration
  $core->config_set($savearray, '', 'shoutbox');
  // redirect
  //redirect('plugins/shoutbox/admin/settings.php'.$SID.'&save=true');
}


// ----------------------------------------------------------------------------
// Saved message
if ($in->get('save'))
{
  $core->message($user->lang['sb_config_saved'], 'Shoutbox', 'green');
}


// -- Template ----------------------------------------------------------------
$jquery->Dialog('AboutShoutbox', $user->lang['sb_about_header'], array('url'=>'../about.php', 'width'=>'400', 'height'=>'250'));
$tpl->assign_vars(array (
  // form
  'F_CONFIG'          => 'settings.php'.$SID,
  'F_USE_USERS'       => $html->CheckBox('sb_use_users', '', $core->config['shoutbox']['sb_use_users']),

  // Language
  'L_SETTINGS_INFO'   => $user->lang['sb_settings_info'],
  'L_USE_USERS'       => $user->lang['sb_use_users'],
  'L_USE_USERS_HELP'  => $user->lang['sb_use_users_help'],
  'L_SAVE'            => $user->lang['save'],
  'L_RESET'           => $user->lang['reset'],
  'L_GENERAL'         => $user->lang['sb_header_general'],

  // credits
  'SB_INFO_IMG'       => '../images/credits/info.png',
  'L_CREDITS'         => $user->lang['sb_credits_part1'].$pm->get_data('shoutbox', 'version').$user->lang['sb_credits_part2'],
));


// -- EQDKP -------------------------------------------------------------------
$core->set_vars(array (
  'page_title'    => $user->lang['shoutbox'].' '.$user->lang['settings'],
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'template_file' => 'admin/settings.html',
  'display'       => true
  )
);

?>
