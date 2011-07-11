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


/*+----------------------------------------------------------------------------
  | exchange_shoutbox_add
  +--------------------------------------------------------------------------*/
if (!class_exists('exchange_shoutbox_add'))
{
  class exchange_shoutbox_add
  {
    public $options = array();

    function post_shoutbox_add($params, $body)
    {
      global $user, $eqdkp_root_path, $pex;

      // parse xml request
      $xml = simplexml_load_string($body);
      $usermember_id = ($xml && $xml->id) ? intval($xml->id) : '';
      $text          = ($xml && $xml->text) ? trim($xml->text) : '';
      if ($xml && $text && $usermember_id > 0)
      {
        // insert xml text
        include_once($eqdkp_root_path.'plugins/shoutbox/includes/common.php');
        $result = $shoutbox->insertShoutboxEntry($usermember_id, trim($text));

        // return status
        $response = array('result' => ($result) ? 1 : 0);
      }
      else
      {
        // missing data
        if (empty($text))
          $response = $pex->error($user->lang('sb_missing_text'));
        else
          $response = $pex->error($user->lang('sb_missing_id'));
      }

      return $response;
    }

  }
}

?>
