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

$new_version = '0.3.0';
$updateFunction = false;
$reloadSETT = 'settings.php';

$updateDESC = array(
  '',
  'Delete guest setting',
  'Delete location setting'
);

$updateSQL = array(
  // Delete guest setting
  'DELETE FROM `__config` 
   WHERE `config_name`=\'sb_invisible_to_guests\';',
  // Delete location setting
  'DELETE FROM `__config` 
   WHERE `config_name`=\'sb_input_box_below\';'
);

?>
