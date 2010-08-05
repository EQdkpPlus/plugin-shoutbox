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
      if ($xml && $xml->text && $xml->member_id)
      {
        // insert xml text
        include_once($eqdkp_root_path.'plugins/shoutbox/includes/common.php');
        $result = $shoutbox->insertShoutboxEntry($xml->member_id, $xml->text);

        // return status
        $response = '<response><result>'.$result.'</result></response>';
      }
      else
      {
        // missing data
        if (!$xml->text)
          $response = '<response><result>Missing text to insert</result></response>';
        else
          $response = '<response><result>Missing Member ID to insert</result></response>';
      }

      return $response;
    }

  }
}

?>
