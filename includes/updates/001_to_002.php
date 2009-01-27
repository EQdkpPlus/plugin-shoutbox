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

$new_version = '0.0.2';
$updateFunction = 'SB001to002Update';
$reloadSETT = 'settings.php';

$updateDESC = array(
  '',
  'Add Configuration Table',
);

$updateSQL = array(
  'CREATE TABLE IF NOT EXISTS `__shoutbox_config` (
       `config_name` varchar(255) NOT NULL default \'\',
       `config_value` varchar(255) default NULL,
       PRIMARY KEY (`config_name`)
    )TYPE=InnoDB;',
);

function SB001to002Update()
{
  global $wpfcdb, $new_version;

  // Config Values
  $savearray = array(
      'sb_inst_version'   => $new_version,        # Save the version for Autoupdate?
      'sb_updatecheck'    => '1',                 # Enable UpdateCheck?
  );

  $wpfcdb->UpdateConfig($savearray, $wpfcdb->CheckDBFields('config_name'));
}

?>
