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
  | exchange_shoutbox_list
  +--------------------------------------------------------------------------*/
if (!class_exists('exchange_shoutbox_list'))
{
  class exchange_shoutbox_list
  {
    public $options = array();
    public $type = 'REST';

    function post_shoutbox_list($params, $body)
    {
      global $user, $pdh;

      // set response
      $response  = '<response>';

      // be sure user is logged in
      if ($user->data['user_id'] != ANONYMOUS)
      {
        // get all shoutbox id's
        $shoutbox_ids = $pdh->get('shoutbox', 'id_list');
        if (is_array($shoutbox_ids))
        {
          // build entry array
          foreach ($shoutbox_ids as $shoutbox_id)
          {
            $response .= '<entry>';
            $response .= '  <id>'.$shoutbox_id.'</id>';
            $response .= '  <member_id>'.$pdh->get('shoutbox', 'memberid', array($shoutbox_id)).'</member_id>';
            $response .= '  <member_name>'.$pdh->get('shoutbox', 'membername', array($shoutbox_id)).'</member_name>';
            $response .= '  <text><![CDATA['.$pdh->geth('shoutbox', 'text', array($shoutbox_id)).']]></text>';
            $response .= '  <date>'.$pdh->get('shoutbox', 'date', array($shoutbox_id)).'</date>';
            $response .= '</entry>';
          }
        }
      }

      // end response
      $response .= '</response>';

      return $response;
    }

  }
}

?>
