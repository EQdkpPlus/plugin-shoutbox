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
$user->check_auth('a_shoutbox_');


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  message_die($user->lang['sb_plugin_not_installed']);
}


// -- Init config database ----------------------------------------------------
$wpfcdb = new AdditionalDB('shoutbox_config');


// -- save? -------------------------------------------------------------------
if ($in->get('save_settings'))
{
  // take over new values
  $savearray = array(
      'sb_updatecheck' => $in->get('sb_updatecheck', 0),
  );

  // update configuration
  if ($wpfcdb->UpdateConfig($savearray, $wpfcdb->CheckDBFields('config_name')))
  {
    // redirect
    redirect('plugins/shoutbox/admin/settings.php'.$SID.'&save=true');
  }
}


// -- update check ------------------------------------------------------------
$updchk_enabled = ($core->config['sb_updatecheck'] == 1) ? true : false;
$versionthing = array(
  'name'     => 'shoutbox',
  'version'  => $pm->get_data('shoutbox', 'version'),
  'build'    => $pm->get_data('shoutbox', 'build'),
  'enabled'  => $updchk_enabled,
  'vstatus'  => $pm->plugins['shoutbox']->vstatus,
);
$sbvcheck = new PluginUpdCheck($versionthing);
$sbvcheck->PerformUpdateCheck();


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

  // Language
  'L_SETTINGS_INFO'   => $user->lang['sb_settings_info'],
  'L_SAVE'            => $user->lang['save'],
  'L_RESET'           => $user->lang['reset'],
  'L_GENERAL'         => $user->lang['sb_header_general'],
  'L_UPDATE_CHECK'    => $user->lang['sb_updatecheck'],

  // Settings
  'UPDATE_CHECK'      => $wpfcdb->isChecked($core->config['sb_updatecheck']),

  // update box
  'UPDCHECK_BOX'      => $sbvcheck->OutputHTML(),

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
