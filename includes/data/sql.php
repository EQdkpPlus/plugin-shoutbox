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

$shoutboxSQL = array(

  'uninstall' => array(
    '1'     => 'DROP TABLE IF EXISTS `__shoutbox`',
    '2'     => 'DROP TABLE IF EXISTS `__shoutbox_config`',
  ),

  'install'   => array(
    '1'     => 'CREATE TABLE IF NOT EXISTS `__shoutbox` (
                  `shoutbox_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `member_id` SMALLINT(5) DEFAULT \'-1\',
                  `shoutbox_date` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
                  `shoutbox_text` TEXT COLLATE utf8_bin DEFAULT NULL,
                  PRIMARY KEY (`shoutbox_id`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;',
    '2'     => 'CREATE TABLE IF NOT EXISTS `__shoutbox_config` (
                  `config_name` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT \'\',
                  `config_value` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
                  PRIMARY KEY (`config_name`)
                ) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;',
  ),
);

?>
