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

$new_version = '0.1.4';
$updateFunction = false;
$reloadSETT = 'settings.php';

$updateDESC = array(
  '',
  'Insert Timezone settings',
);

$updateSQL = array(
  'INSERT INTO `__shoutbox_config` (`config_name`, `config_value`)
   VALUES (\'sb_timezone\', \'0\'), (\'sb_dstcorrect\', \'0\')',
);

?>
