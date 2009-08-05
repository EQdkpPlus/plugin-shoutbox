<?php
 /*
 * Project:     eqdkpPLUS Library Manager
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2009-08-03 22:36:07 +0200 (Mo, 03 Aug 2009) $
 * ----------------------------------------------------------------------- 
 * @author      $Author: wallenium $
 * @copyright   2008 Simon (Wallenium) Wallmann
 * @link        http://eqdkp-plus.com
 * @package     libraries
 * @version     $Rev: 5421 $
 * 
 * $Id: libloader.inc.php 5421 2009-08-03 20:36:07Z wallenium $
 */
  
  // Configuration
  $myPluginID       = 'shoutbox';         // Plugin ID, p.e. 'raidplan'
  
  // DO NOT CHANGE
  if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
  } 
  
  // EQDKP PLUS 0.7.x ++
  if(is_object($libloader)){
    $libloader->CheckLibVersion('Libraries', false, $pm->plugins[$myPluginID]->fwversion);
    $khrml = $html; $khrml->SetPluginName($myPluginID);
  
  // EQDKP PLUS 0.6.3.1 ++
  }else{
    if(!file_exists($eqdkp_root_path . 'libraries/libraries.php')){
      message_die((($user->lang['libloader_notfound']) ? $user->lang['libloader_notfound'] : 'Library Loader not available! Check if the "eqdkp/libraries/" folder is uploaded correctly'));
    }
    require_once($eqdkp_root_path . 'libraries/libraries.php');
    $libloader  = new libraries();
    $libloader->CheckLibVersion('Libraries',false, $pm->plugins[$myPluginID]->fwversion);
    $jquery = $jqueryp; $khrml = new myHTML($myPluginID);
  }
?>