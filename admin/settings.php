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
define('IN_ADMIN', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check('shoutbox', PLUGIN_INSTALLED))
{
  message_die($user->lang('sb_plugin_not_installed'));
}

/*+----------------------------------------------------------------------------
  | ShoutboxSettings
  +--------------------------------------------------------------------------*/
class ShoutboxSettings extends page_generic
{
  /**
   * Constructor
   */
  public function __construct()
  {
    $handler = array(
      'sb_save' => array('process' => 'save', 'session_key' => true, 'check' => 'a_shoutbox_'),
    );
    parent::__construct('a_shoutbox_', $handler);

    $this->process();
  }

  /**
   * save
   * Save the configuration
   */
  public function save()
  {
    global $in, $user, $core, $shoutbox;

    // is use_user change?
    if ($in->get('sb_use_users', 0) != $core->config('sb_use_users', 'shoutbox'))
    {
      // convert to member?
      if ($in->get('sb_use_users', '0') == '1')
      {
        $shoutbox->convertFromMemberToUser();
        $messages[] = $user->lang('sb_convert_member_user_success');
      }
      else
      {
        $shoutbox->deleteAllEntries();
        $messages[] = $user->lang('sb_convert_user_member_success');
      }
    }

    // take over new values
    $savearray = array(
      'sb_use_users' => $in->get('sb_use_users', 0),
    );

    // update configuration
    $core->config_set($savearray, '', 'shoutbox');
    // Success message
    $messages[] = $user->lang('sb_config_saved');

    $this->display($messages);
  }

  /**
   * display
   * Display the page
   *
   * @param    array  $messages   Array of Messages to output
   */
  public function display($messages=array())
  {
    global $core, $user, $pm, $html, $jquery, $tpl;

    // -- Messages ------------------------------------------------------------
    if ($messages)
    {
      foreach($messages as $name)
      {
        $core->message($name, $user->lang('shoutbox'), 'green');
      }
    }

    // -- Template ------------------------------------------------------------
    $jquery->Dialog('AboutShoutbox', $user->lang('sb_about_header'), array('url'=>'../about.php', 'width'=>'400', 'height'=>'250'));
    $tpl->assign_vars(array (
      // form
      'F_USE_USERS'       => $html->CheckBox('sb_use_users', '', $core->config('sb_use_users', 'shoutbox')),

      // credits
      'SB_INFO_IMG'       => '../images/credits/info.png',
      'L_CREDITS'         => $user->lang('sb_credits_part1').$pm->get_data('shoutbox', 'version').$user->lang('sb_credits_part2'),
    ));

    // -- EQDKP ---------------------------------------------------------------
    $core->set_vars(array(
      'page_title'    => $user->lang('shoutbox').' '.$user->lang('settings'),
      'template_path' => $pm->get_data('shoutbox', 'template_path'),
      'template_file' => 'admin/settings.html',
      'display'       => true
    ));
  }
}

$shoutboxSettings = new ShoutboxSettings();

?>
