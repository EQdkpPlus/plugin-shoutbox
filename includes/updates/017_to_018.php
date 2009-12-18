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

$new_version = '0.1.8';
$updateFunction = 'SB017to018Update';
$reloadSETT = 'settings.php';

$updateDESC = array(
  '',
  'Add new date field',
  'Rename text field'
);

$updateSQL = array(
  // Add new Date field
  'ALTER TABLE `__shoutbox`
   ADD `shoutbox_date` INT(11) UNSIGNED NOT NULL DEFAULT \'0\'
   AFTER `member_id`',
  // Rename text field
  'ALTER TABLE `__shoutbox`
   CHANGE `text` `shoutbox_text` TEXT NULL DEFAULT NULL'
);

/**
 * SB017to018Update
 * Update from 0.1.7 to 0.1.8
 */
function SB017to018Update()
{
  global $db;

  // loop through all entries and copy "date" -> "shoutbox_date"
  $sql = 'SELECT shoutbox_id, UNIX_TIMESTAMP(date) AS date
          FROM `__shoutbox`';
  $result = $db->query($sql);
  if ($result)
  {
    while (($row = $db->fetch_record($result)))
    {
      // create update sql
      $sql = 'UPDATE `__shoutbox`
              SET shoutbox_date='.$row['date'].'
              WHERE shoutbox_id='.$row['shoutbox_id'];
      $db->query($sql);
    }
    $db->free_result($result);
  }

  // delete old "date" field
  $sql = 'ALTER TABLE `__shoutbox` DROP `date`';
  $db->query($sql);
}

?>
