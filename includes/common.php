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


// -- Framework include -------------------------------------------------------
$phpversionnr  = (version_compare(phpversion(), "5.0.0", ">=")) ? '5' : '4';
$myLibraryPath = $eqdkp_root_path.'libraries/libraries.php'.$phpversionnr.'.php';

// The library Loader is not available
if(!file_exists($myLibraryPath))
{
  $libnothere_txt = ($user->lang['libloader_notfound']) ? $user->lang['libloader_notfound'] : 'Library Loader not available! Check if the "eqdkp/libraries/" folder is uploaded correctly';
  message_die($libnothere_txt);
}
require_once($myLibraryPath);
$wpfccore = new pluginCore();
CheckLibVersion('pluginCore', $wpfccore->version, $pm->plugins['shoutbox']->fwversion);
$jquery = new jquery(); 


// -- Used Classes ------------------------------------------------------------
include_once($eqdkp_root_path.'plugins/shoutbox/includes/shoutbox.class.php');
$shoutbox = new Shoutbox();


// -- JQUERY Header -----------------------------------------------------------
if (is_object($tpl))
{
  $tpl->assign_vars(array('JQUERY_INCLUDES' => $jquery->Header()));
}


?>
