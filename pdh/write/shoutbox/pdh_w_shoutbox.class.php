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
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_shoutbox'))
{
  class pdh_w_shoutbox extends pdh_w_generic
  {
    /*
     * Number of chars to wrap after
     */
    private $wordwrap;

    /**
     * Constructor
     */
    public function __construct()
    {
      parent::pdh_w_generic();

      // set default wordwrap
      $this->wordwrap = 20;
    }

    /**
     * add
     * Add a new shoutbox entry
     *
     * @param  int     $usermember_id  User or Member ID
     * @param  string  $text           Text to insert
     *
     * @returns mixed, on success shoutbox id, else false
     */
    public function add($usermember_id, $text)
    {
      global $db, $pdh, $time;

      // cleanup text
      $text = $this->cleanupText($text);

      // add to database
      $sql_data = array(
        'user_or_member_id' => $usermember_id,
        'shoutbox_date'     => $db->sql_escape($time->time),
        'shoutbox_text'     => $db->sql_escape($text)
      );
      $result = $db->query('INSERT INTO `__shoutbox` :params', $sql_data);
      if (!$result)
        return false;

      // do hooks
      $pdh->enqueue_hook('shoutbox_update');

      return $db->insert_id();
    }

    /**
     * delete
     * Delete a shoutbox entry from db
     *
     * @param  int   $shoutbox_id  Shoutbox ID
     *
     * @returns boolean
     */
    public function delete($shoutbox_id)
    {
      global $db, $pdh;

      // delete from db
      $sql = 'DELETE FROM `__shoutbox` WHERE shoutbox_id='.$db->sql_escape($shoutbox_id);
      $result = $db->query($sql);
      if (!$result)
        return false;

      // do hooks
      $pdh->enqueue_hook('shoutbox_update');

      return true;
    }

    /**
     * set_user
     * Updates an entry and sets member id to user id
     *
     * @param  int   $shoutbox_id  Shoutbox ID
     * @param  int   $user_id      User ID
     *
     * @returns boolean
     */
    public function set_user($shoutbox_id, $user_id)
    {
      global $db, $pdh;

      // update in db
      $sql = 'UPDATE `__shoutbox`
              SET `user_or_member_id`='.$db->sql_escape($user_id).'
              WHERE shoutbox_id='.$db->sql_escape($shoutbox_id);
      $result = $db->query($sql);
      if (!$result)
        return false;

      // do hooks
      $pdh->enqueue_hook('shoutbox_update');

      return true;
    }

    /**
     * cleanupText
     * Cleans up the text to insert to database
     *
     * @param  string   $text  Text to insert
     *
     * @returns string
     */
    private function cleanupText($text)
    {
      global $bbcode;

      // wrap words (do own handling cause of bbcodes)
      $cleanup_text = $this->shoutbox_wordwrap($text, $this->wordwrap, "\n", true);

      // wrap around with <p>
      $cleanup_text = '<p>'.$cleanup_text.'</p>';

      // bbcodes, set smiley path to special identifier cause shoutbox has to replace when showing
      $bbcode->SetSmiliePath('{SMILEY_PATH}');
      $cleanup_text = $bbcode->toHTML($cleanup_text);
      $cleanup_text = $bbcode->MyEmoticons($cleanup_text);

      return $cleanup_text;
    }

    /**
     * shoutbox_wordwrap
     * Wrap words ignoring bb code
     *
     * @param   string   $text   Text to wrap
     * @param   integer  $width  Max length of one line
     * @param   string   $break  String to insert for line break, default '\n'
     * @param   boolean  $cut    cut inside of words?
     *
     * @return  string
     */
    private function shoutbox_wordwrap($text, $width, $break="\n", $cut=false)
    {
      // explode by spaces
      $element_array = explode(' ', $text);
      $count = count($element_array);

      // loop through all the elements
      $wraped_text = '';
      foreach($element_array as $org_text)
      {
        // explode by \n
        $inner_element_array = explode("\n", $org_text);
        foreach($inner_element_array as $inner_org_text)
        {
          // strip bbcode from text
          $striped_text = preg_replace('#\[[\w=]+\](.*?)\[/[\w]+\]#si', '\1', $inner_org_text);
          // get striped size
          $striped_size = strlen($striped_text);

          // do not wrap image/urls/emails
          $inner_cut = $cut;
          if (preg_match('#\[img\](.*?)\[/img\]#si', $inner_org_text) ||
              preg_match('#\[url=?"?(.*?)"?\](.*?)\[/url\]#si', $inner_org_text) ||
              preg_match('#\[email\](.*?)\[/email\]#si', $inner_org_text))
          {
            $inner_cut = false;
          }

          // fits?
          if ($striped_size > $width)
          {
            $new_text = wordwrap($striped_text, $width, $break, $inner_cut);
            // replace in original text
            $new_text = str_replace($striped_text, $new_text, $inner_org_text);
          }
          else
          {
            // fit, so just take original text
            $new_text = $inner_org_text;
          }

          // append to output
          $wraped_text .= $new_text."\n";
        }
        // replace last char by space
        $wraped_text[strlen($wraped_text)-1]= ' ';
      }

      return $wraped_text;
    }

  } //end class
} //end if class not exists

?>
