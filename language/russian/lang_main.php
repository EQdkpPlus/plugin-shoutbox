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

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'shoutbox'                        => 'Shoutbox',
  'sb_shoutbox'                     => 'Shoutbox',

  // Description
  'sb_short_desc'                   => 'Shoutbox',
  'sb_long_desc'                    => 'Shoutbox is a Plugin users are able to exchange short messages with.',

  // General
  'sb_plugin_not_installed'         => 'Shoutbox Plugin not installed',
  'sb_php_version'                  => "Shoutbox requires PHP %1\$s or higher. Your server runs PHP %2\$s",
  'sb_plus_version'                 => "Shoutbox requires EQDKP-PLUS %1\$s or higher. Your installed Version is %2\$s",

  // Menu
  'sb_manage'                       => 'Manage',

  // Admin -> Settings
  'sb_date_format'                  => 'Y/m/d H:i',  // YYYY/MM/DD HH:mm
  'sb_time_format'                  => 'H:i',        // HH:mm
  'sb_adm_date'                     => 'Date',
  'sb_adm_name'                     => 'Name',
  'sb_adm_text'                     => 'Message',
  'sb_adm_select_all'               => 'Select all',
  'sb_adm_select_none'              => 'Select none',

  // Configuration
  'sb_config_saved'                 => 'Settings saved successfully',
  'sb_header_general'               => 'General Shoutbox settings',
  'sb_updatecheck'                  => 'Enable check for new Plugin Versions',

  // Portal Modules
  'sb_output_count_limit'           => 'Limit of shoutbox entries.',
  'sb_show_date'                    => 'Show date also?',
  'sb_show_archive'                 => 'Show Archive?',
  'sb_input_box_below'              => 'Input box below entries?',
  'sb_autoreload'                   => 'Time in seconds to wait for automatic reload of Shoutbox (Default 10)',
  'sb_autoreload_help'              => 'Set to 0 to disable automatic reload',
  'sb_invisible_to_guests'          => '<u>Not</u> visible to guests?',
  'sb_no_character_assigned'        => 'No characters are connected yet. At least one character has to be connected to be able to post.',
  'sb_submit_text'                  => 'Send',
  'sb_save_wait'                    => 'Saving, please wait...',
  'sb_reload'                       => 'Reload',
  'sb_no_entries'                   => 'No entries',
  'sb_archive'                      => 'Archive',
  'sb_shoutbox_archive'             => 'Shoutbox Archive',
  'sb_footer'                       => "... %1\$d found / %2\$d per page",

  // About/Credits
  'sb_about_header'                 => 'About Shoutbox',
  'sb_credits_part1'                => 'Shoutbox v',
  'sb_credits_part2'                => ' by Aderyn',
  'sb_copyright'                    => 'Copyright',
);

?>
