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
    public $type = 'REST';

    function post_shoutbox_add($params, $body)
    {
      global $user, $eqdkp_root_path;

      // parse xml request
      $xml = simplexml_load_string($body);
      $usermember_id = ($xml && $xml->id) ? $xml->id : '';
      $text          = ($xml && $xml->text) ? trim($xml->text) : '';
      if ($xml && $text && $usermember_id)
      {
        // insert xml text
        include_once($eqdkp_root_path.'plugins/shoutbox/includes/common.php');
        $result = $shoutbox->insertShoutboxEntry($usermember_id, trim($text));

        // return status
        $response = '<response><result>'.$result.'</result></response>';
      }
      else
      {
        // missing data
        if (empty($text))
          $response = '<response><result>false</result><error>'.$user->lang('sb_missing_text').'</error></response>';
        else
          $response = '<response><result>false</result><error>'.$user->lang('sb_missing_id').'</error></response>';
      }

      return $response;
    }

  }
}

?>
