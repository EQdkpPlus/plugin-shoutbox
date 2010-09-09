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
 * @copyright   2008-2010 Aderyn
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
  public $build      = '8668';
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
      'icon'              => 'images/adminmenu/shoutbox.png',
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
    // permissions: 'a'=admins, 'u'=user
    // ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
    // Groups: 2 = Super-Admin, 3 = Admin, 4 = Member
    $this->add_permission('a', 'delete', 'N', $user->lang['delete'], array(2,3));
    $this->add_permission('u', 'add',    'Y', $user->lang['add'],    array(2,3,4));

    // -- Menu --------------------------------------------
    $this->add_menu('admin_menu', $this->gen_admin_menu());

    // -- Portal Module -----------------------------------
    $this->add_portal_module('shoutbox');

    // -- PDH Modules -------------------------------------
    $this->add_pdh_read_module('shoutbox');
    $this->add_pdh_write_module('shoutbox');

    // -- Exchange Modules --------------------------------
    $this->add_exchange_module('shoutbox_add');
    $this->add_exchange_module('shoutbox_list');
    $this->add_exchange_module('shoutbox', true, 'shoutbox.xml');
  }

  /**
    * pre_install
    * Define Installation
    */
  public function pre_install()
  {
    global $eqdkp_root_path, $core;

    // include SQL and default configuration data for installation
    include($eqdkp_root_path.'plugins/shoutbox/includes/data/sql.php');
    include($eqdkp_root_path.'plugins/shoutbox/includes/data/config.php');

    // define installation
    for ($i = 1; $i <= count($shoutboxSQL['install']); $i++)
      $this->add_sql(SQL_INSTALL, $shoutboxSQL['install'][$i]);

    // insert configuration
    if (is_array($config_vars))
      $core->config_set($config_vars, '', 'shoutbox');
  }

  /**
    * pre_uninstall
    * Define uninstallation
    */
  public function pre_uninstall()
  {
    global $eqdkp_root_path;

    // include SQL data for uninstallation
    include($eqdkp_root_path.'plugins/shoutbox/includes/data/sql.php');

    for ($i = 1; $i <= count($shoutboxSQL['uninstall']); $i++)
      $this->add_sql(SQL_UNINSTALL, $shoutboxSQL['uninstall'][$i]);
  }

  /**
    * post_uninstall
    * Define Post Uninstall
    */
  public function post_uninstall()
  {
    global $pdc, $pcache;

    // clear cache
    $pdc->del('pdh_shoutbox_table');

    // clear RSS feed
    $pcache->Delete($pcache->FilePath('shoutbox.xml', 'shoutbox'));
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
          'icon' => './../../plugins/shoutbox/'.$this->data['icon'],
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

}

?>
