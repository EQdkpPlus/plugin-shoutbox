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
    /**
     * Additional options
     */
    public $options = array();

    /**
     * post_shoutbox_add
     * POST Request to add shoutbox entry
     *
     * @param   array   $params   Parameters array
     * @param   string  $body     XML body of request
     *
     * @returns array
     */
    function post_shoutbox_add($params, $body)
    {
      global $user, $eqdkp_root_path, $core, $pdh, $pex;

      // be sure user is logged in
      if ($user->data['user_id'] != ANONYMOUS)
      {
        // parse xml request
        $xml = simplexml_load_string($body);
        $member_id = ($xml && $xml->charid) ? intval($xml->charid) : intval($pdh->get('user', 'mainchar', array($user->data['user_id'])));
        $text      = ($xml && $xml->text)   ? trim($xml->text)     : '';

        // check if member id is valid for this user
        $valid_members = $pdh->get('member', 'connection_id', array($user->data['user_id']));
        $member_valid = (is_array($valid_members) && in_array($member_id, $valid_members)) ? true : false;

        // if we are in "user" mode OR member is valid, continue
        if ($core->config('sb_use_users', 'shoutbox') || $member_valid)
        {
          // get usermember_id
          $usermember_id = ($core->config('sb_use_users', 'shoutbox') ? intval($user->data['user_id']) : $member_id);

          if (!empty($text) && $usermember_id > 0)
          {
            // insert xml text
            include_once($eqdkp_root_path.'plugins/shoutbox/includes/common.php');
            $result = $shoutbox->insertShoutboxEntry($usermember_id, trim($text));

            // return status
            $response = array('status' => ($result) ? 1 : 0);
          }
          else
          {
            // missing data
            if (empty($text))
              $response = $pex->error($user->lang('sb_missing_text'));
            else
              $response = $pex->error($user->lang('sb_missing_char_id'));
          }
        }
        else
        {
          $response = $pex->error($user->lang('sb_missing_char_id'));
        }
      }
      else
      {
        $response = $pex->error('access denied');
      }

      return $response;
    }

  }
}

?>
