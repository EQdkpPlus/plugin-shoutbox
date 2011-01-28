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

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


include_once($eqdkp_root_path.'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_shoutbox_031'))
{
  class update_shoutbox_031 extends sql_update_task
  {
    public $author      = 'Aderyn';
    public $version     = '0.3.1';    // new version
    public $name        = 'Shoutbox 0.3.1 Update';
    public $type        = 'plugin_update';
    public $plugin_path = 'shoutbox'; // important!

    /**
     * Constructor
     */
    public function __construct()
    {
      parent::__construct();

      // init language
      $this->langs = array(
        'english' => array(
          'update_shoutbox_031' => 'Shoutbox 0.3.1 Update Package',
          // SQL
           1 => 'Insert new user or character setting',
           2 => 'Change to user or character field',
        ),
        'german' => array(
          'update_shoutbox_031' => 'Shoutbox 0.3.1 Update Paket',
          // SQL
           1 => 'Füge neue Benutzer oder Charakter Einstellung hinzu',
           2 => 'Ändere in Benutzer oder Charakter Eintrag',
        ),
      );

      // init SQL querys
      $this->sqls = array(
         1 => 'INSERT INTO `__backup_cnf` (config_name, config_value, config_plugin) VALUES(\'sb_use_users\', \'0\', \'shoutbox\');',
         2 => 'ALTER TABLE `__shoutbox` CHANGE `member_id` `user_or_member_id` SMALLINT(5) NOT NULL DEFAULT \'-1\';',
      );
    }

    /**
     * update_function
     * Execute update function
     *
     * @returns  true/false
     */
    /*public function update_function()
    {
      return true;
    }*/

  }
}

?>
