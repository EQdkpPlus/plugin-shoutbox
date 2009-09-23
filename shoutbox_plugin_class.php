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
  var $version    = '0.1.6';
  var $build      = '5921';
  var $copyright  = 'Aderyn';
  var $vstatus    = 'Stable';
  var $fwversion  = '2.0.0';  // required framework Version

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    *
    * @param    EQdkp_Plugin_Manager    $pm   Plugin Manager
    */
  function shoutbox_plugin_class($pm)
  {
    global $eqdkp_root_path, $user;

    $this->eqdkp_plugin($pm);
    $this->pm->get_language_pack('shoutbox');

    $this->add_data(array (
      'name'          => 'Shoutbox',
      'code'          => 'shoutbox',
      'path'          => 'shoutbox',
      'contact'       => 'Aderyn@gmx.net',
      'template_path' => 'plugins/shoutbox/templates/',
      'version'       => $this->version,
    ));

    // Addition Information for eqdkpPLUS
    $this->additional_data = array(
        'author'            => 'Aderyn',
        'description'       => $user->lang['sb_short_desc'],
        'long_description'  => $user->lang['sb_long_desc'],
        'homepage'          => 'http://www.eqdkp-plus.com/',
        'manuallink'        => false,
    );

    // -- Register our permissions ------------------------
    $this->add_permission('341', 'a_shoutbox_delete', 'N', $user->lang['delete']);
    $this->add_permission('342', 'u_shoutbox_add',    'Y', $user->lang['add']);

    // -- Menu --------------------------------------------
    $this->add_menu('admin_menu', $this->gen_admin_menu());

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

      // insert the Permission of the installing Person
      $perm_array = array('341', '342');
      $this->set_permissions($perm_array);

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
  function gen_admin_menu()
  {
    if ($this->pm->check(PLUGIN_INSTALLED, 'shoutbox'))
    {
      global $user, $SID, $eqdkp_root_path;

      $url_prefix = (EQDKP_VERSION < '1.3.2') ? $eqdkp_root_path : '';

      $admin_menu = array (
        'shoutbox' => array (
          0 => $user->lang['shoutbox'],
          1 => array (
            'link'  => $url_prefix.'plugins/shoutbox/admin/settings.php'.$SID,
            'text'  => $user->lang['settings'],
            'check' => 'a_shoutbox_'
          ),
          2 => array (
            'link'  => $url_prefix.'plugins/shoutbox/admin/manage.php'.$SID,
            'text'  => $user->lang['sb_manage'],
            'check' => 'a_shoutbox_delete'
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
  function insert_configuration($config_vars)
  {
    foreach ($config_vars as $config_name => $config_value)
    {
      $sql = 'INSERT INTO `__shoutbox_config` VALUES(\''.$config_name.'\', \''.$config_value.'\');';
      $this->add_sql(SQL_INSTALL, $sql);
    }
  }

  /**
    * set_permissions
    * Set default permission for current user installing the plugin
    *
    * @param    array    $perm_array    Array with all permission id's for user
    * @param    char     $perm_setting  Default to 'Y' = Yes or 'N' = No?
    */
  function set_permissions($perm_array, $perm_setting='Y')
  {
    global $user, $db;

    // do we have a logged in user?
    $userid = ($user->data['user_id'] != ANONYMOUS) ? $user->data['user_id'] : '';
    if($userid)
    {
      foreach ($perm_array as $value)
      {
        $sql = 'UPDATE `__auth_users` SET auth_setting=\''.$perm_setting.'\' WHERE user_id='.$userid.' AND auth_id='.$value;
        $db->query($sql);
        if ($db->sql_affectedrows() == 0)
        {
          // add new data
          $sql = 'INSERT INTO `__auth_users` VALUES('.$userid.', '.$value.', \''.$perm_setting.'\')';
          $this->add_sql(SQL_INSTALL, $sql);
        }
      }
    }
  }

}
?>
