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


/*+----------------------------------------------------------------------------
  | shoutbox_Plugin_Class
  +--------------------------------------------------------------------------*/
class shoutbox_Plugin_Class extends EQdkp_Plugin
{
  public $version    = '0.3.0';
  public $build      = '7736';
  public $copyright  = 'Aderyn';
  public $vstatus    = 'Beta';

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    *
    * @param    EQdkp_Plugin_Manager    $pm   Plugin Manager
    */
  public function __construct($pm)
  {
    global $eqdkp_root_path, $user;

    $this->eqdkp_plugin($pm);
    $this->pm->get_language_pack('shoutbox');

    $this->add_data(array (
      'name'              => 'Shoutbox',
      'code'              => 'shoutbox',
      'path'              => 'shoutbox',
      'contact'           => 'Aderyn@gmx.net',
      'template_path'     => 'plugins/shoutbox/templates/',
      'version'           => $this->version,
      'author'            => $this->copyright,
      'description'       => $user->lang['sb_short_desc'],
      'long_description'  => $user->lang['sb_long_desc'],
      'homepage'          => 'http://www.eqdkp-plus.com/',
      'manuallink'        => false,
      'plus_version'      => '0.7',
      'build'             => $this->build,
    ));

    $this->add_dependency(array(
      'plus_version'      => '0.7',
      'lib_version'       => '2.0.0',
    ));

    // -- Register our permissions ------------------------
    // 2 = Super-Admin, 3 = Admin, 4 = Member
    $this->add_permission('a_shoutbox_delete', 'N', $user->lang['delete'], array(2,3));
    $this->add_permission('u_shoutbox_add',    'Y', $user->lang['add'],    array(2,3,4));

    // -- Menu --------------------------------------------
    $this->add_menu('admin_menu', $this->gen_admin_menu());

    // -- Portal Module -----------------------------------
    $this->add_portal_module('shoutbox');

    // -- PDH Modules -------------------------------------
    $this->add_pdh_read_module('shoutbox');
    $this->add_pdh_write_module('shoutbox');
    $this->add_pdh_read_module('sb_member_user');


    // -- SQL Data ----------------------------------------
    include($eqdkp_root_path.'plugins/shoutbox/includes/data/sql.php');

    // -- install -----------------------------------------
    if (!($this->pm->check(PLUGIN_INSTALLED, 'shoutbox')))
    {
      // include default configuration data for installation
      include($eqdkp_root_path.'plugins/shoutbox/includes/data/config.php');

      // define installation
      for ($i = 1; $i <= count($shoutboxSQL['install']); $i++)
      {
        // prepend uninstall string if we have one to be sure installation is clear
        if ($shoutboxSQL['uninstall'][$i])
        {
          $this->add_sql(SQL_INSTALL, $shoutboxSQL['uninstall'][$i]);
        }
        $this->add_sql(SQL_INSTALL, $shoutboxSQL['install'][$i]);
      }

      // insert configuration
      if (is_array($config_vars))
      {
        $this->insert_configuration($config_vars);
      }
    }

    // -- uninstall ---------------------------------------
    for ($i = 1; $i <= count($shoutboxSQL['uninstall']); $i++)
    {
      if($shoutboxSQL['uninstall'][$i])
      {
        $this->add_sql(SQL_UNINSTALL, $shoutboxSQL['uninstall'][$i]);
      }
    }
  }

  /**
    * gen_admin_menu
    * Generate the Admin Menu
    */
  private function gen_admin_menu()
  {
    global $user, $SID;

    if ($this->pm->check(PLUGIN_INSTALLED, 'shoutbox') && $user->check_auth('a_shoutbox_', false))
    {
      $admin_menu = array (
        'shoutbox' => array (
          'name' => $user->lang['shoutbox'],
          'icon' => './../../plugins/shoutbox/images/adminmenu/shoutbox.png',
          1 => array (
            'link'  => 'plugins/shoutbox/admin/settings.php'.$SID,
            'text'  => $user->lang['settings'],
            'check' => 'a_shoutbox_',
            'icon'  => 'settings.png'
          ),
          2 => array (
            'link'  => 'plugins/shoutbox/admin/manage.php'.$SID,
            'text'  => $user->lang['sb_manage_archive'],
            'check' => 'a_shoutbox_delete',
            'icon'  => './../glyphs/archive.png'
          )
        )
      );

      return $admin_menu;
    }

    return;
  }

  /**
    * insert_configuration
    * Insert the default configuration values into database
    *
    * @param    array    $config_vars   Array with all default configuration values
    */
  private function insert_configuration($config_vars)
  {
    foreach ($config_vars as $config_name => $config_value)
    {
      $sql = 'INSERT INTO `__shoutbox_config` VALUES(\''.$config_name.'\', \''.$config_value.'\');';
      $this->add_sql(SQL_INSTALL, $sql);
    }
  }

}
?>
