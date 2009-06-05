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


// -- Functions ---------------------------------------------------------------
if (!function_exists('shoutbox_requirements'))
{
  /**
    * shoutbox_requirements
    * return requirements
    *
    * @returns array
    */
  function shoutbox_requirements()
  {
    $sbReqVersions = array(
        'php'   => '5.0.0',
        'eqdkp' => '0.6.2.7'
    );

    return $sbReqVersions;
  }
}

if (!function_exists('shoutbox_requirements_check'))
{
  /**
    * shoutbox_requirements_check
    * do the shoutbox requirement check
    */
  function shoutbox_requirements_check()
  {
    global $user;

    $sbReqVersions = shoutbox_requirements();
    if (version_compare(phpversion(), $sbReqVersions['php'], "<"))
    {
      message_die(sprintf($user->lang['sb_php_version'], $sbReqVersions['php'], phpversion()));
    }
    if (version_compare(EQDKPPLUS_VERSION, $sbReqVersions['eqdkp'], "<"))
    {
      message_die(sprintf($user->lang['sb_plus_version'], $sbReqVersions['eqdkp'], ((EQDKPPLUS_VERSION > 0) ? EQDKPPLUS_VERSION : '[non-PLUS]')));
    }
  }
}


if (!function_exists('shoutbox_portal_requirements_check'))
{
  /**
    * shoutbox_portal_requirements_check
    * do the shoutbox requirement check for the portal
    *
    * @returns  mixed
    */
  function shoutbox_portal_requirements_check()
  {
    global $user;

    // set defult as OK
    $result = true;

    // compare
    $sbReqVersions = shoutbox_requirements();
    if (version_compare(phpversion(), $sbReqVersions['php'], "<"))
    {
      $result = sprintf($user->lang['sb_php_version'], $sbReqVersions['php'], phpversion());
    }
    else if (version_compare(EQDKPPLUS_VERSION, $sbReqVersions['eqdkp'], "<"))
    {
      $result = sprintf($user->lang['sb_plus_version'], $sbReqVersions['eqdkp'],
                        ((EQDKPPLUS_VERSION > 0) ? EQDKPPLUS_VERSION : '[non-PLUS]'));
    }

    return $result;
  }
}
?>
