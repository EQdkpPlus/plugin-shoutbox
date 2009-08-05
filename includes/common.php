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

// -- Pluskernel common.php ---------------------------------------------------
if (!isset($eqdkp_root_path))
{
  $eqdkp_root_path = './';
}
include_once($eqdkp_root_path.'common.php');


// -- Defines -----------------------------------------------------------------
if (!defined('SHOUTBOX_DEFAULT_LIMIT')) define('SHOUTBOX_DEFAULT_LIMIT', 10);
if (!defined('SHOUTBOX_WORDWRAP'))      define('SHOUTBOX_WORDWRAP',      20);
if (!defined('SHOUTBOX_PAGE_LIMIT'))    define('SHOUTBOX_PAGE_LIMIT',    50);
if (!defined('SHOUTBOX_AUTORELOAD'))    define('SHOUTBOX_AUTORELOAD',     0);


// -- Framework include -------------------------------------------------------
include_once($eqdkp_root_path.'plugins/shoutbox/includes/libloader.inc.php');


// -- read config values ------------------------------------------------------
$sql = 'SELECT * FROM `__shoutbox_config`';
if (($config_result = $db->query($sql)))
{
  while(($rowc = $db->fetch_record($config_result)))
  {
    $sb_conf[$rowc['config_name']] = $rowc['config_value'];
  }
  $db->free_result($config_result);
}


// -- Used Classes ------------------------------------------------------------
include_once($eqdkp_root_path.'plugins/shoutbox/includes/shoutbox.class.php');
$shoutbox = new Shoutbox();


// -- Check requirements ------------------------------------------------------
if (is_object($shoutbox))
{
  $sb_req_check = $shoutbox->checkRequirements();
  if ($sb_req_check !== true)
  {
    message_die($sb_req_check);
  }
}

?>
