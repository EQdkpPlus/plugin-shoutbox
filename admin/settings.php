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


// -- Init WPFC ---------------------------------------------------------------
$wpfcdb = new AdditionalDB('shoutbox_config');
$sbupdater = new PluginUpdater('shoutbox','sb_','shoutbox_config','includes');


// -- reset the version? (to force an update) ---------------------------------
if ($in->get('version') == 'reset')
{
  $sbupdater->DeleteVersionString();
  redirect('plugins/shoutbox/admin/settings.php'.$SID);
}


// -- save? -------------------------------------------------------------------
if ($in->get('save_settings'))
{
  // take over new values
  $savearray = array(
      'sb_updatecheck'  =>  $in->get('sb_updatecheck', 0),
      'sb_timezone'     =>  $in->get('sb_timezone'),
      'sb_dstcorrect'   =>  $in->get('sb_dstcorrect', 0),
  );

  // update configuration
  if ($wpfcdb->UpdateConfig($savearray, $wpfcdb->CheckDBFields('config_name')))
  {
    // clear cache if dst correction has changed
    if ($savearray['sb_dstcorrect'] != $eqdkp->config['sb_dstcorrect'])
    {
      $pdc->del('pdh_shoutbox_table');
    }

    // redirect
    redirect('plugins/shoutbox/admin/settings.php'.$SID.'&save=true');
  }
}


// -- read config values ------------------------------------------------------
$sql = 'SELECT * FROM `__shoutbox_config`';
if ($config_result = $db->query($sql))
{
  while($rowc = $db->fetch_record($config_result))
  {
    $sb_conf[$rowc['config_name']] = $rowc['config_value'];
  }
  $db->free_result($config_result);
}


// -- update check ------------------------------------------------------------
$updchk_enbled = ($sb_conf['sb_updatecheck'] == 1) ? true : false;
$cachedb       = array('table' => 'shoutbox_config', 'data' => $sb_conf['vc_data'], 'f_data' => 'vc_data', 'lastcheck' => $sb_conf['vc_lastcheck'], 'f_lastcheck' => 'vc_lastcheck');
$versionthing  = array('name' => 'shoutbox', 'inclpath' => 'includes', 'version' => $pm->get_data('shoutbox', 'version'), 'build' => $pm->plugins['shoutbox']->build, 'enabled' => $updchk_enbled);
$sbvcheck = new PluginUpdCheck($versionthing, $cachedb);
$sbvcheck->PerformUpdateCheck();


// ----------------------------------------------------------------------------
// Saved message
if ($in->get('save'))
{
  $eqdkp->message($user->lang['sb_config_saved'], 'Shoutbox', 'green');
}


// -- Timezone ----------------------------------------------------------------
// load timezone array
$sb_timezones = array();
$timezone_file = $eqdkp_root_path.'plugins/shoutbox/language/'.$user->lang_name.'/lang_tz.php';
// check for file exist, if not try fallback to english
if (!file_exists($timezone_file))
{
  $timezone_file = $eqdkp_root_path.'plugins/shoutbox/language/english/lang_tz.php';
}
if (file_exists($timezone_file))
{
  include_once($timezone_file);
}

// get timezone offset
$temp = time()+Date('I')*3600;
$dst = date('I', $temp);
if ($dst == 1 && $sb_conf['sb_dstcorrect'] == 1)
{
  $cur_timezone = ($sb_conf['sb_timezone'] != '') ? $sb_conf['sb_timezone'] : intval((date('Z', $temp)-1)/3600);
}
else
{
  $cur_timezone = ($sb_conf['sb_timezone'] != '') ? $sb_conf['sb_timezone'] : intval(date('Z', $temp)/3600);
}


// -- Template ----------------------------------------------------------------
$tpl->assign_vars(array (
  // form
  'F_CONFIG'          => 'settings.php'.$SID,

  // Language
  'L_SUBMIT'          => $user->lang['submit'],
  'L_GENERAL'         => $user->lang['sb_header_general'],
  'L_UPDATE_CHECK'    => $user->lang['sb_updatecheck'],
  'L_TIMEZONE'        => $user->lang['sb_timezone'],
  'L_DSTCORRECT'      => $user->lang['sb_dstcorrect'],

  // Settings
  'UPDATE_CHECK'      => $wpfcdb->isChecked($sb_conf['sb_updatecheck']),
  'DRDWN_TZONE'       => $html->DropDown('sb_timezone', $sb_timezones, $cur_timezone),
  'DST_CORRECT'       => $wpfcdb->isChecked($sb_conf['sb_dstcorrect']),

  // update box
  'UPDATE_BOX'        => $sbupdater->OutputHTML(),
  'UPDCHECK_BOX'      => $sbvcheck->OutputHTML(),

  // credits
  'JS_ABOUT'          => $jquery->Dialog_URL('About', $user->lang['sb_about_header'], '../about.php', '400', '250'),
  'SB_INFO_IMG'       => '../images/credits/info.png',
  'L_CREDITS'         => $user->lang['sb_credits_part1'].$pm->get_data('shoutbox', 'version').$user->lang['sb_credits_part2'],
));


// -- EQDKP -------------------------------------------------------------------
$eqdkp->set_vars(array (
  'page_title'    => sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['shoutbox'],
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'template_file' => 'admin/settings.html',
  'display'       => true
  )
);

?>
